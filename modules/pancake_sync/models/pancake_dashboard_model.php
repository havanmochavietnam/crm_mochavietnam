<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard_model extends App_Model
{
    private $cache_dir;

    public function __construct()
    {
        parent::__construct();
        $this->cache_dir = APPPATH . 'cache/pancake/';
        if (!is_dir($this->cache_dir)) {
            @mkdir($this->cache_dir, 0755, true);
        }
    }

    /* ==================== FILE CACHE TỐI GIẢN ==================== */
    private function _cache_path($key)
    {
        return $this->cache_dir . md5($key) . '.cache';
    }

    /* ==================== TTL tính bằng giây ==================== */
    private function _cache_get($key, $ttl)
    {
        $file = $this->_cache_path($key);
        if (!is_file($file)) return false;
        if (filemtime($file) + $ttl < time()) {
            @unlink($file);
            return false;
        }
        $raw = @file_get_contents($file);
        if ($raw === false) return false;
        $data = @unserialize($raw);
        return $data !== false ? $data : false;
    }

    private function _cache_set($key, $value, $ttl)
    {
        $file = $this->_cache_path($key);
        @file_put_contents($file, serialize($value), LOCK_EX);
        @touch($file, time()); // mtime = now
        return true;
    }

    private function _customer_uid_expr()
    {
        return "COALESCE(
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')),
                NULLIF(REGEXP_REPLACE(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.shipping_address.phone_number')), '[^0-9]', ''), ''),
                NULLIF(REGEXP_REPLACE(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.bill_phone_number')), '[^0-9]', ''), ''),
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.referral_code')),
                CAST(po.pancake_order_id AS CHAR)
            )";
    }

    private function _customer_order_count_expr()
    {
        return "CAST(IFNULL(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.order_count')), '0') AS UNSIGNED)";
    }

    /* ==================== DASHBOARD METRICS (TỔNG QUAN) ==================== */
    public function get_dashboard_metrics($start_date, $end_date, $only_today = false)
    {
        $cache_key = 'dash_metrics_' . $start_date . '_' . $end_date . ($only_today ? '_fast' : '');
        if (($cached = $this->_cache_get($cache_key, 300)) !== false) return $cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        if ($only_today) {
            // FAST PATH: chỉ quét trong [startDT, endDT]
            $sql = "
        SELECT
            -- In-range (và cũng là 'today' khi only_today = true)
            agg.count_confirmed                         AS count_confirmed_in_range,
            agg.revenue                                  AS revenue_confirmed_in_range,
            agg.sales                                    AS sales_volume_confirmed_in_range,
            agg.quantity                                 AS product_quantity_confirmed_in_range,
            created.count_created_in_range               AS count_created_in_range,

            -- Hôm nay (trùng in-range)
            agg.count_confirmed                         AS count_confirmed_today,
            agg.revenue                                  AS revenue_confirmed_today,
            created.count_created_in_range               AS count_created_today,
            created.count_canceled_in_range              AS count_canceled_today,
            created.count_removed_in_range               AS count_removed_today,
            agg.unique_customers                         AS unique_customers_today,
            agg.quantity                                 AS product_quantity_confirmed_today
        FROM
        (
            SELECT
                COUNT(*)                                   AS count_confirmed,
                SUM(ex.net_revenue)                        AS revenue,
                SUM(ex.total_price)                        AS sales,
                SUM(ex.total_quantity)                     AS quantity,
                COUNT(DISTINCT ex.customer_id)             AS unique_customers
            FROM (
                SELECT
                    po.pancake_order_id AS order_id,
                    JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id'))                   AS customer_id,
                    CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price_after_sub_discount'), 0) AS DECIMAL(18,2)) AS net_revenue,
                    CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) AS DECIMAL(18,2)) AS total_price,
                    CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_quantity'), 0) AS DECIMAL(18,2)) AS total_quantity,
                    JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))                    AS status_name
                FROM {$table} po
                JOIN (
                    SELECT po2.pancake_order_id
                    FROM {$table} po2
                    JOIN JSON_TABLE(
                        po2.data,
                        '$.status_history[*]'
                        COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                    ) h ON h.status = 1
                    WHERE DATE_ADD(h.updated_at, INTERVAL 7 HOUR) BETWEEN ? AND ?
                    GROUP BY po2.pancake_order_id
                ) hr ON hr.pancake_order_id = po.pancake_order_id
            ) ex
            WHERE ex.status_name NOT IN ('canceled','returned','returning')
        ) agg
        CROSS JOIN
        (
            SELECT
                COUNT(*) AS count_created_in_range,
                SUM(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))='canceled') AS count_canceled_in_range,
                SUM(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))='removed')  AS count_removed_in_range
            FROM {$table} po
            WHERE DATE_ADD(po.created_at, INTERVAL 7 HOUR) BETWEEN ? AND ?
        ) created";
            $bind = [$startDT, $endDT, $startDT, $endDT];
            $row  = $this->db->query($sql, $bind)->row_array() ?: [];
            $this->_cache_set($cache_key, $row, 300);
            return $row;
        }

        // === PATH CŨ (khi người dùng chọn range khác) ===
        $sql = "
        SELECT
            /* Today */
            SUM(CASE WHEN DATE(h.confirmed_at) = CURDATE() AND ex.status_name NOT IN ('canceled','returned','returning') THEN 1 ELSE 0 END) AS count_confirmed_today,
            SUM(CASE WHEN DATE(h.confirmed_at) = CURDATE() AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.net_revenue ELSE 0 END) AS revenue_confirmed_today,
            SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE() THEN 1 ELSE 0 END) AS count_created_today,
            SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE() AND ex.status_name='canceled' THEN 1 ELSE 0 END) AS count_canceled_today,
            SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE() AND ex.status_name='removed'  THEN 1 ELSE 0 END) AS count_removed_today,
            COUNT(DISTINCT CASE WHEN DATE(h.confirmed_at) = CURDATE() AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.customer_id END) AS unique_customers_today,
            SUM(CASE WHEN DATE(h.confirmed_at) = CURDATE() AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_today,

            /* In range */
            SUM(CASE WHEN h.confirmed_at BETWEEN ? AND ? AND ex.status_name NOT IN ('canceled','returned','returning') THEN 1 ELSE 0 END) AS count_confirmed_in_range,
            SUM(CASE WHEN h.confirmed_at BETWEEN ? AND ? AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.net_revenue ELSE 0 END) AS revenue_confirmed_in_range,
            SUM(CASE WHEN h.confirmed_at BETWEEN ? AND ? AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.total_price ELSE 0 END) AS sales_volume_confirmed_in_range,
            SUM(CASE WHEN h.confirmed_at BETWEEN ? AND ? AND ex.status_name NOT IN ('canceled','returned','returning') THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_in_range,
            SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) BETWEEN ? AND ? THEN 1 ELSE 0 END) AS count_created_in_range
        FROM
        (
            SELECT
                po.pancake_order_id AS order_id,
                po.created_at,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) AS status_name,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')) AS customer_id,
                CAST(JSON_EXTRACT(po.data, '$.total_price_after_sub_discount') AS DECIMAL(18,2)) AS net_revenue,
                CAST(JSON_EXTRACT(po.data, '$.total_price') AS DECIMAL(18,2)) AS total_price,
                CAST(JSON_EXTRACT(po.data, '$.shipping_fee') AS DECIMAL(18,2)) AS shipping_fee,
                CAST(JSON_EXTRACT(po.data, '$.total_quantity') AS DECIMAL(18,2)) AS total_quantity
            FROM {$table} po
        ) ex
        LEFT JOIN
        (
            SELECT
                po.pancake_order_id AS order_id,
                MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR)) AS confirmed_at
            FROM {$table} po
            JOIN JSON_TABLE(
                po.data,
                '$.status_history[*]'
                COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
            ) h
            WHERE h.status = 1
            GROUP BY po.pancake_order_id
        ) h ON h.order_id = ex.order_id";
        $bind = [$startDT, $endDT, $startDT, $endDT, $startDT, $endDT, $startDT, $endDT, $startDT, $endDT];
        $row  = $this->db->query($sql, $bind)->row_array() ?: [];
        $this->_cache_set($cache_key, $row, 300);
        return $row;
    }

    public function get_channel_metrics($start_date, $end_date, $only_today = false)
    {
        $cache_key = 'dash_channels_' . $start_date . '_' . $end_date . ($only_today ? '_fast' : '_like_affiliate_formula');
        if (($cached = $this->_cache_get($cache_key, 300)) !== false) return $cached;

        $table         = db_prefix() . 'pancake_orders';
        $startDT       = $start_date . ' 00:00:00';
        $endDT         = $end_date   . ' 23:59:59';
        $customerUID   = $this->_customer_uid_expr();
        $custOrderCnt  = $this->_customer_order_count_expr();

        if ($only_today) {
            $sql = "
        SELECT
            ex.channel,
            SUM(ex.total_price - (ex.order_discount + ex.item_discount_sum)) AS revenue,
            SUM(ex.total_price)                                            AS sales,
            SUM(ex.order_discount + ex.item_discount_sum)                  AS discount,
            COUNT(*)                                                       AS orders,
            SUM(ex.total_quantity)                                         AS quantity,
            COUNT(DISTINCT CASE WHEN ex.cust_order_count IN (0,1)  THEN ex.customer_uid END) AS cust_new,
            COUNT(DISTINCT CASE WHEN ex.cust_order_count NOT IN (0,1) THEN ex.customer_uid END) AS cust_returning
        FROM (
            SELECT
                po.pancake_order_id AS order_id,
                {$customerUID}  AS customer_uid,
                {$custOrderCnt} AS cust_order_count,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price'),    0) AS DECIMAL(18,2)) AS total_price,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_quantity'), 0) AS DECIMAL(18,2)) AS total_quantity,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) AS DECIMAL(18,2)) AS order_discount,
                (
                    SELECT IFNULL(SUM(CAST(JSON_EXTRACT(i.value, '$.total_discount') AS DECIMAL(18,2))), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
                ) AS item_discount_sum
            FROM {$table} po
            JOIN (
                SELECT po2.pancake_order_id
                FROM {$table} po2
                JOIN JSON_TABLE(
                    po2.data,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = 1
                WHERE DATE_ADD(h.updated_at, INTERVAL 7 HOUR) BETWEEN ? AND ?
                GROUP BY po2.pancake_order_id
            ) hr ON hr.pancake_order_id = po.pancake_order_id
        ) ex
        WHERE ex.status_name NOT IN ('canceled','returned','returning')
        GROUP BY ex.channel";

            $q = $this->db->query($sql, [$startDT, $endDT]);
            $rows = is_object($q) ? ($q->result_array() ?: []) : [];
        } else {
            // NON-FAST PATH: lọc theo thời gian confirmed_at trong khoảng người dùng chọn
            $sql = "
        SELECT
            ex.channel,
            SUM(ex.total_price - (ex.order_discount + ex.item_discount_sum)) AS revenue,
            SUM(ex.total_price)                                            AS sales,
            SUM(ex.order_discount + ex.item_discount_sum)                  AS discount,
            COUNT(*)                                                       AS orders,
            SUM(ex.total_quantity)                                         AS quantity,
            COUNT(DISTINCT CASE WHEN ex.cust_order_count IN (0,1)  THEN ex.customer_uid END) AS cust_new,
            COUNT(DISTINCT CASE WHEN ex.cust_order_count NOT IN (0,1) THEN ex.customer_uid END) AS cust_returning
        FROM (
            SELECT
                po.pancake_order_id AS order_id,
                {$customerUID}  AS customer_uid,
                {$custOrderCnt} AS cust_order_count,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price'),    0) AS DECIMAL(18,2)) AS total_price,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_quantity'), 0) AS DECIMAL(18,2)) AS total_quantity,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) AS DECIMAL(18,2)) AS order_discount,
                (
                    SELECT IFNULL(SUM(CAST(JSON_EXTRACT(i.value, '$.total_discount') AS DECIMAL(18,2))), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
                ) AS item_discount_sum,
                (
                  SELECT MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR))
                  FROM JSON_TABLE(
                        po.data,
                        '$.status_history[*]'
                        COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                  ) h
                  WHERE h.status = 1
                ) AS confirmed_at
            FROM {$table} po
        ) ex
        WHERE ex.status_name NOT IN ('canceled','returned','returning')
          AND ex.confirmed_at BETWEEN ? AND ?
        GROUP BY ex.channel";

            $q = $this->db->query($sql, [$startDT, $endDT]);
            $rows = is_object($q) ? ($q->result_array() ?: []) : [];
        }

        // Chuẩn hoá output + fill kênh thiếu
        $out = [];
        foreach ($rows as $r) {
            $ch     = $r['channel'] ?: 'Khác';
            $orders = (int)($r['orders'] ?? 0);
            $rev    = (float)($r['revenue'] ?? 0);
            $out[$ch] = [
                'revenue'        => $rev,
                'sales'          => (float)($r['sales'] ?? 0),
                'discount'       => (float)($r['discount'] ?? 0),
                'orders'         => $orders,
                'quantity'       => (int)($r['quantity'] ?? 0),
                'aov'            => $orders > 0 ? ($rev / $orders) : 0,
                'cust_new'       => (int)($r['cust_new'] ?? 0),
                'cust_returning' => (int)($r['cust_returning'] ?? 0),
            ];
        }
        foreach (['Affiliate', 'Facebook', 'Shopee', 'Zalo', 'Tiktok', 'Woocommerce', 'Hotline', 'LadiPage', 'Khác'] as $c) {
            if (!isset($out[$c])) {
                $out[$c] = ['revenue' => 0, 'sales' => 0, 'discount' => 0, 'orders' => 0, 'quantity' => 0, 'aov' => 0, 'cust_new' => 0, 'cust_returning' => 0];
            }
        }
        $this->_cache_set($cache_key, $out, 300);
        return $out;
    }

    public function get_customer_segments_overall($start_date, $end_date, $only_today = false)
    {
        $cache_key = 'cust_segments_overall_' . $start_date . '_' . $end_date . ($only_today ? '_fast' : '');
        if (($cached = $this->_cache_get($cache_key, 300)) !== false) return $cached;

        $table        = db_prefix() . 'pancake_orders';
        $startDT      = $start_date . ' 00:00:00';
        $endDT        = $end_date   . ' 23:59:59';
        $customerUID  = $this->_customer_uid_expr();
        $custOrderCnt = $this->_customer_order_count_expr();

        if ($only_today) {
            $sql = "
        SELECT
            COUNT(DISTINCT CASE WHEN ex.cust_order_count IN (0,1)  THEN ex.customer_uid END) AS cust_new,
            COUNT(DISTINCT CASE WHEN ex.cust_order_count NOT IN (0,1) THEN ex.customer_uid END) AS cust_returning
        FROM (
            SELECT
                {$customerUID}  AS customer_uid,
                {$custOrderCnt} AS cust_order_count,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) AS status_name
            FROM {$table} po
            JOIN (
                SELECT po2.pancake_order_id
                FROM {$table} po2
                JOIN JSON_TABLE(
                    po2.data,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = 1
                WHERE DATE_ADD(h.updated_at, INTERVAL 7 HOUR) BETWEEN ? AND ?
                GROUP BY po2.pancake_order_id
            ) hr ON hr.pancake_order_id = po.pancake_order_id
        ) ex
        WHERE ex.status_name NOT IN ('canceled','returned','returning')";
            $row = $this->db->query($sql, [$startDT, $endDT])->row_array() ?: ['cust_new' => 0, 'cust_returning' => 0];
        } else {
            // === PATH CŨ của bạn giữ nguyên ===
            $sql = "
        SELECT
            COUNT(DISTINCT CASE
                WHEN ord.confirmed_at BETWEEN ? AND ?
                 AND ord.status_name NOT IN ('canceled','returned','returning')
                 AND ord.cust_order_count IN (0,1)
                THEN ord.customer_uid END) AS cust_new,
            COUNT(DISTINCT CASE
                WHEN ord.confirmed_at BETWEEN ? AND ?
                 AND ord.status_name NOT IN ('canceled','returned','returning')
                 AND ord.cust_order_count NOT IN (0,1)
                THEN ord.customer_uid END) AS cust_returning
        FROM
        (
            SELECT
                po.pancake_order_id AS order_id,
                {$customerUID}  AS customer_uid,
                {$custOrderCnt} AS cust_order_count,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))  AS status_name,
                (
                  SELECT MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR))
                  FROM JSON_TABLE(
                        po.data,
                        '$.status_history[*]'
                        COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                  ) h
                  WHERE h.status = 1
                ) AS confirmed_at
            FROM {$table} po
        ) ord";
            $row = $this->db->query($sql, [$startDT, $endDT, $startDT, $endDT])->row_array() ?: ['cust_new' => 0, 'cust_returning' => 0];
        }

        $row['total'] = (int)$row['cust_new'] + (int)$row['cust_returning'];
        $this->_cache_set($cache_key, $row, 300);
        return $row;
    }
}
