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

    /* ==================== Helpers cho JSON fields ==================== */
    private function _customer_uid_expr()
    {
        // UID khách: ưu tiên customer.id; fallback theo SĐT/RC; cuối cùng là order_id (string)
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
            // FAST PATH dùng trực tiếp time_status_submitted trong khoảng
            $sql = "
                SELECT
                    -- In-range (và cũng là Today khi only_today = true)
                    agg.count_confirmed                         AS count_confirmed_in_range,
                    agg.revenue                                  AS revenue_confirmed_in_range,
                    agg.sales                                    AS sales_volume_confirmed_in_range,
                    agg.quantity                                 AS product_quantity_confirmed_in_range,
                    created.count_created_in_range               AS count_created_in_range,

                    -- Today (trùng in-range)
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
                        COUNT(*) AS count_confirmed,
                        SUM(CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price_after_sub_discount'), 0) AS DECIMAL(18,2))) AS revenue,
                        SUM(CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) AS DECIMAL(18,2)))                    AS sales,
                        SUM(CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_quantity'), 0) AS DECIMAL(18,2)))                 AS quantity,
                        COUNT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')))                              AS unique_customers
                    FROM {$table} po
                    WHERE po.time_status_submitted BETWEEN ? AND ?
                      AND (JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled','returned','returning')
                           OR JSON_EXTRACT(po.data, '$.status_name') IS NULL)
                ) agg
                CROSS JOIN
                (
                    SELECT
                        COUNT(*) AS count_created_in_range,
                        SUM(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))='canceled') AS count_canceled_in_range,
                        SUM(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))='removed')  AS count_removed_in_range
                    FROM {$table} po
                    WHERE DATE_ADD(po.created_at, INTERVAL 7 HOUR) BETWEEN ? AND ?
                ) created
            ";
            $bind = [$startDT, $endDT, $startDT, $endDT];
            $row  = $this->db->query($sql, $bind)->row_array() ?: [];
            $this->_cache_set($cache_key, $row, 300);
            return $row;
        }

        // NON-FAST: vẫn giữ logic cũ nhưng confirmed_at lấy từ cột time_status_submitted
        $sql = "
            SELECT
                /* Today */
                SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN 1 ELSE 0 END) AS count_confirmed_today,
                SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN ex.net_revenue ELSE 0 END) AS revenue_confirmed_today,
                SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE()
                         THEN 1 ELSE 0 END) AS count_created_today,
                SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE()
                          AND ex.status_name='canceled' THEN 1 ELSE 0 END) AS count_canceled_today,
                SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) = CURDATE()
                          AND ex.status_name='removed'  THEN 1 ELSE 0 END) AS count_removed_today,
                COUNT(DISTINCT CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                                      AND ex.status_name NOT IN ('canceled','returned','returning')
                                    THEN ex.customer_id END) AS unique_customers_today,
                SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_today,

                /* In range */
                SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN 1 ELSE 0 END) AS count_confirmed_in_range,
                SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN ex.net_revenue ELSE 0 END) AS revenue_confirmed_in_range,
                SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN ex.total_price ELSE 0 END) AS sales_volume_confirmed_in_range,
                SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                          AND ex.status_name NOT IN ('canceled','returned','returning')
                         THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_in_range,
                SUM(CASE WHEN DATE(DATE_ADD(ex.created_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                         THEN 1 ELSE 0 END) AS count_created_in_range
            FROM
            (
                SELECT
                    po.pancake_order_id AS order_id,
                    po.created_at,
                    po.time_status_submitted AS confirmed_at,
                    JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) AS status_name,
                    JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                    JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')) AS customer_id,
                    CAST(JSON_EXTRACT(po.data, '$.total_price_after_sub_discount') AS DECIMAL(18,2)) AS net_revenue,
                    CAST(JSON_EXTRACT(po.data, '$.total_price') AS DECIMAL(18,2)) AS total_price,
                    CAST(JSON_EXTRACT(po.data, '$.shipping_fee') AS DECIMAL(18,2)) AS shipping_fee,
                    CAST(JSON_EXTRACT(po.data, '$.total_quantity') AS DECIMAL(18,2)) AS total_quantity
                FROM {$table} po
            ) ex
        ";
        $bind = [$startDT, $endDT, $startDT, $endDT, $startDT, $endDT, $startDT, $endDT, $startDT, $endDT];
        $row  = $this->db->query($sql, $bind)->row_array() ?: [];
        $this->_cache_set($cache_key, $row, 300);
        return $row;
    }

    /* ==================== KPI THEO KÊNH ==================== */
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
            // Lọc trực tiếp theo time_status_submitted
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
                    WHERE po.time_status_submitted BETWEEN ? AND ?
                ) ex
                WHERE ex.status_name NOT IN ('canceled','returned','returning')
                GROUP BY ex.channel
            ";
            $q = $this->db->query($sql, [$startDT, $endDT]);
            $rows = is_object($q) ? ($q->result_array() ?: []) : [];
        } else {
            // NON-FAST: confirmed_at = time_status_submitted
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
                        po.time_status_submitted AS confirmed_at
                    FROM {$table} po
                ) ex
                WHERE ex.status_name NOT IN ('canceled','returned','returning')
                  AND ex.confirmed_at BETWEEN ? AND ?
                GROUP BY ex.channel
            ";
            $q = $this->db->query($sql, [$startDT, $endDT]);
            $rows = is_object($q) ? ($q->result_array() ?: []) : [];
        }

        // Chuẩn hoá output + fill các kênh thường gặp
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

    /* ==================== PHÂN KHÚC KHÁCH HÀNG TỔNG ==================== */
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
            // Dùng time_status_submitted giữa khoảng
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
                    WHERE po.time_status_submitted BETWEEN ? AND ?
                ) ex
                WHERE ex.status_name NOT IN ('canceled','returned','returning')
            ";
            $row = $this->db->query($sql, [$startDT, $endDT])->row_array() ?: ['cust_new' => 0, 'cust_returning' => 0];
        } else {
            // NON-FAST: confirmed_at = time_status_submitted
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
                        po.time_status_submitted AS confirmed_at
                    FROM {$table} po
                ) ord
            ";
            $row = $this->db->query($sql, [$startDT, $endDT, $startDT, $endDT])->row_array() ?: ['cust_new' => 0, 'cust_returning' => 0];
        }

        $row['total'] = (int)$row['cust_new'] + (int)$row['cust_returning'];
        $this->_cache_set($cache_key, $row, 300);
        return $row;
    }

    public function get_marketer_metrics($start_date, $end_date)
    {
        // Cache đơn giản 5 phút
        $cache_key = 'dash_marketers_name_' . $start_date . '_' . $end_date;
        if (($cached = $this->_cache_get($cache_key, 300)) !== false) return $cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Key gom nhóm theo tên đã TRIM để tránh lệch khoảng trắng
        // Dùng MIN(marketer_name) để hiển thị tên gốc đẹp nhất trong nhóm
        $sql = "
        SELECT
            TRIM(o.marketer_name) AS marketer_name_key,
            MIN(o.marketer_name)  AS marketer_name,
            SUM(CAST(IFNULL(o.total_order_amount, 0) AS DECIMAL(18,2))) AS revenue,
            SUM(CAST(IFNULL(o.total_discount,     0) AS DECIMAL(18,2))) AS discount
        FROM {$table} o
        WHERE o.time_status_submitted BETWEEN ? AND ?
          AND TRIM(COALESCE(o.marketer_name, '')) <> ''
          AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
        GROUP BY marketer_name_key
        ORDER BY revenue DESC
    ";

        $q = $this->db->query($sql, [$startDT, $endDT]);
        $rows = is_object($q) ? ($q->result_array() ?: []) : [];

        // Chuẩn hoá output: chỉ trả về các cột yêu cầu
        $out = [];
        foreach ($rows as $r) {
            $name = trim((string)($r['marketer_name'] ?? ''));
            if ($name === '') continue; // phòng hờ

            $out[] = [
                'marketer_name' => $name,
                'revenue'       => (float)($r['revenue']  ?? 0),   // SUM(total_order_amount)
                'discount'      => (float)($r['discount'] ?? 0),   // SUM(total_discount)
            ];
        }

        $this->_cache_set($cache_key, $out, 300);
        return $out;
    }
}
