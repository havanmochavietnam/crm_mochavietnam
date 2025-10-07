<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        // ĐÃ BỎ TOÀN BỘ CACHE: không còn tạo/thao tác thư mục cache.
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
        $tableOrders  = db_prefix() . 'pancake_orders';          // = tblpancake_orders
        $tableDetails = db_prefix() . 'pancake_order_details';   // = tblpancake_order_details

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Pre-agg SL theo đơn (tổng hàng chốt)
        $qtySub = "
        SELECT
            pod.pancake_order_id AS order_id,
            SUM(COALESCE(pod.quantity, 0)) AS qty
        FROM {$tableDetails} pod
        GROUP BY pod.pancake_order_id
    ";

        if ($only_today) {
            // FAST PATH: các chỉ số chốt dùng time_status_submitted; created/canceled/removed dùng created_at
            $sql = "
            SELECT
                -- In-range (trùng Today khi only_today = true)
                agg.count_confirmed                   AS count_confirmed_in_range,
                agg.revenue                           AS revenue_confirmed_in_range,          -- SUM(total_order_amount)
                agg.sales                             AS sales_volume_confirmed_in_range,      -- SUM(total_price)
                agg.quantity                          AS product_quantity_confirmed_in_range,  -- SUM(qty)
                created.count_created_in_range        AS count_created_in_range,

                -- Today
                agg.count_confirmed                   AS count_confirmed_today,
                agg.revenue                           AS revenue_confirmed_today,
                created.count_created_in_range        AS count_created_today,
                created.count_canceled_in_range       AS count_canceled_today,
                created.count_removed_in_range        AS count_removed_today,
                agg.unique_customers                  AS unique_customers_today,
                agg.quantity                          AS product_quantity_confirmed_today
            FROM
            (
                SELECT
                    COUNT(*) AS count_confirmed,
                    SUM(COALESCE(po.total_order_amount, 0)) AS revenue,     -- Doanh thu
                    SUM(COALESCE(po.total_price, 0))        AS sales,       -- Doanh số
                    SUM(COALESCE(q.qty, 0))                 AS quantity,    -- Tổng hàng chốt
                    COUNT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id'))) AS unique_customers
                FROM {$tableOrders} po
                LEFT JOIN ( {$qtySub} ) q ON q.order_id = po.pancake_order_id
                WHERE po.time_status_submitted BETWEEN ? AND ?
                  AND (po.status_name NOT IN ('canceled','returned','returning') OR po.status_name IS NULL)
            ) agg
            CROSS JOIN
            (
                SELECT
                    COUNT(*) AS count_created_in_range,
                    SUM(po.status_name = 'canceled') AS count_canceled_in_range,
                    SUM(po.status_name = 'removed')  AS count_removed_in_range
                FROM {$tableOrders} po
                WHERE po.created_at BETWEEN ? AND ?
            ) created
        ";
            $bind = [$startDT, $endDT, $startDT, $endDT];
            return $this->db->query($sql, $bind)->row_array() ?: [];
        }

        // NON-FAST: confirmed_at = time_status_submitted; revenue & sales theo đúng cột
        $sql = "
        SELECT
            /* Today (theo submitted) */
            SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN 1 ELSE 0 END) AS count_confirmed_today,

            SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.revenue ELSE 0 END) AS revenue_confirmed_today,      -- SUM(total_order_amount)

            SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.sales ELSE 0 END) AS sales_volume_confirmed_today,   -- SUM(total_price)

            SUM(CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_today,

            COUNT(DISTINCT CASE WHEN DATE(ex.confirmed_at) = CURDATE()
                                  AND ex.status_name NOT IN ('canceled','returned','returning')
                                THEN ex.customer_id END) AS unique_customers_today,

            /* Created/canceled/removed (theo created_at) */
            SUM(CASE WHEN DATE(ex.created_at) = CURDATE() THEN 1 ELSE 0 END) AS count_created_today,
            SUM(CASE WHEN DATE(ex.created_at) = CURDATE() AND ex.status_name='canceled' THEN 1 ELSE 0 END) AS count_canceled_today,
            SUM(CASE WHEN DATE(ex.created_at) = CURDATE() AND ex.status_name='removed'  THEN 1 ELSE 0 END) AS count_removed_today,

            /* In range (theo submitted) */
            SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN 1 ELSE 0 END) AS count_confirmed_in_range,

            SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.revenue ELSE 0 END) AS revenue_confirmed_in_range,    -- SUM(total_order_amount)

            SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.sales ELSE 0 END) AS sales_volume_confirmed_in_range, -- SUM(total_price)

            SUM(CASE WHEN ex.confirmed_at BETWEEN ? AND ?
                      AND ex.status_name NOT IN ('canceled','returned','returning')
                     THEN ex.total_quantity ELSE 0 END) AS product_quantity_confirmed_in_range,

            /* Created in range (theo created_at) */
            SUM(CASE WHEN ex.created_at BETWEEN ? AND ? THEN 1 ELSE 0 END) AS count_created_in_range
        FROM
        (
            SELECT
                po.pancake_order_id AS order_id,
                po.created_at,
                po.time_status_submitted AS confirmed_at,
                po.status_name,
                po.order_sources_name AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')) AS customer_id,

                -- Map chuẩn: doanh thu = total_order_amount; doanh số = total_price
                CAST(COALESCE(po.total_order_amount, 0) AS DECIMAL(18,2)) AS revenue,
                CAST(COALESCE(po.total_price, 0)        AS DECIMAL(18,2)) AS sales,

                CAST(COALESCE(q.qty, 0) AS DECIMAL(18,2)) AS total_quantity
            FROM {$tableOrders} po
            LEFT JOIN ( {$qtySub} ) q ON q.order_id = po.pancake_order_id
        ) ex
    ";
        $bind = [
            $startDT,
            $endDT,   // count_confirmed_in_range (start, end)
            $startDT,
            $endDT,   // revenue_confirmed_in_range
            $startDT,
            $endDT,   // sales_volume_confirmed_in_range
            $startDT,
            $endDT,   // product_quantity_confirmed_in_range
            $startDT,
            $endDT    // count_created_in_range (created_at)
        ];
        return $this->db->query($sql, $bind)->row_array() ?: [];
    }

    /* ==================== KPI THEO KÊNH ==================== */
    public function get_channel_metrics($start_date, $end_date, $only_today = false)
    {
        $tableOrders  = db_prefix() . 'pancake_orders';          // tblpancake_orders
        $tableDetails = db_prefix() . 'pancake_order_details';   // tblpancake_order_details

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Tổng SL theo đơn (quantity) từ bảng chi tiết
        $qtySub = "
        SELECT
            pod.pancake_order_id AS order_id,
            SUM(COALESCE(pod.quantity, 0)) AS qty
        FROM {$tableDetails} pod
        GROUP BY pod.pancake_order_id
    ";

        // Subquery chung: lấy thẳng từ cột SQL (chỉ còn JSON cho UID & order_count nếu chưa có cột)
        $sub = "
        SELECT
            po.pancake_order_id AS order_id,
            po.time_status_submitted AS confirmed_at,
            TRIM(COALESCE(po.order_sources_name, '')) AS channel_raw,  -- tên kênh từ DB
            po.status_name,

            CAST(COALESCE(po.total_order_amount, 0) AS DECIMAL(18,2)) AS revenue,   -- Doanh thu
            CAST(COALESCE(po.total_price,       0) AS DECIMAL(18,2)) AS sales,     -- Doanh số
            CAST(COALESCE(po.total_discount,    0) AS DECIMAL(18,2)) AS discount,  -- Chiết khấu (cấp đơn)
            CAST(COALESCE(q.qty, 0)             AS DECIMAL(18,2))    AS quantity,  -- Tổng hàng chốt

            {$this->_customer_uid_expr()}         AS customer_uid,
            {$this->_customer_order_count_expr()} AS cust_order_count
        FROM {$tableOrders} po
        LEFT JOIN ( {$qtySub} ) q ON q.order_id = po.pancake_order_id
    ";

        // Query chính (lọc theo đơn đã chốt)
        if ($only_today) {
            $sql = "
            SELECT
                ex.channel_raw,
                SUM(ex.revenue)  AS revenue,
                SUM(ex.sales)    AS sales,
                SUM(ex.discount) AS discount,
                COUNT(*)         AS orders,
                SUM(ex.quantity) AS quantity,
                COUNT(DISTINCT CASE WHEN ex.cust_order_count IN (0,1)  THEN ex.customer_uid END) AS cust_new,
                COUNT(DISTINCT CASE WHEN ex.cust_order_count NOT IN (0,1) THEN ex.customer_uid END) AS cust_returning
            FROM ( {$sub} WHERE confirmed_at BETWEEN ? AND ? ) ex
            WHERE ex.status_name NOT IN ('canceled','returned','returning')
            GROUP BY ex.channel_raw
        ";
            $rows = $this->db->query($sql, [$startDT, $endDT])->result_array() ?: [];
        } else {
            $sql = "
            SELECT
                ex.channel_raw,
                SUM(ex.revenue)  AS revenue,
                SUM(ex.sales)    AS sales,
                SUM(ex.discount) AS discount,
                COUNT(*)         AS orders,
                SUM(ex.quantity) AS quantity,
                COUNT(DISTINCT CASE WHEN ex.cust_order_count IN (0,1)  THEN ex.customer_uid END) AS cust_new,
                COUNT(DISTINCT CASE WHEN ex.cust_order_count NOT IN (0,1) THEN ex.customer_uid END) AS cust_returning
            FROM ( {$sub} ) ex
            WHERE ex.status_name NOT IN ('canceled','returned','returning')
              AND ex.confirmed_at BETWEEN ? AND ?
            GROUP BY ex.channel_raw
        ";
            $rows = $this->db->query($sql, [$startDT, $endDT])->result_array() ?: [];
        }

        // Chuẩn hoá & alias kênh (Affiliate -> CTV; gom hoa/thường/khoảng trắng)
        $out = [];

        // alias theo lowercase
        $channel_alias = [
            'affiliate' => 'CTV',
            'ctv'       => 'CTV',
            // thêm alias khác nếu bạn muốn gộp vào 1 kênh
        ];

        foreach ($rows as $r) {
            $raw = isset($r['channel_raw']) ? trim((string)$r['channel_raw']) : '';
            $key = ($raw !== '') ? $raw : 'Khác';

            // chuẩn hoá để tra alias: lowercase + gọn khoảng trắng
            $norm = preg_replace('/\s+/u', ' ', mb_strtolower($key, 'UTF-8'));
            $norm = trim($norm);
            if (isset($channel_alias[$norm])) {
                $key = $channel_alias[$norm]; // 'Affiliate' hoặc 'ctv' -> 'CTV'
            }

            if (!isset($out[$key])) {
                $out[$key] = [
                    'revenue'        => 0.0,
                    'sales'          => 0.0,
                    'discount'       => 0.0,
                    'orders'         => 0,
                    'quantity'       => 0,
                    'aov'            => 0.0,
                    'cust_new'       => 0,
                    'cust_returning' => 0,
                ];
            }

            $orders = (int)($r['orders'] ?? 0);
            $rev    = (float)($r['revenue'] ?? 0);

            $out[$key]['revenue']        += $rev;
            $out[$key]['sales']          += (float)($r['sales'] ?? 0);
            $out[$key]['discount']       += (float)($r['discount'] ?? 0);
            $out[$key]['orders']         += $orders;
            $out[$key]['quantity']       += (int)($r['quantity'] ?? 0);
            $out[$key]['cust_new']       += (int)($r['cust_new'] ?? 0);
            $out[$key]['cust_returning'] += (int)($r['cust_returning'] ?? 0);
        }

        // Fill kênh thiếu & tính AOV
        $defaults = ['CTV', 'Facebook', 'Shopee', 'Zalo', 'Tiktok', 'Woocommerce', 'Hotline', 'LadiPage', 'Khác'];
        foreach ($defaults as $c) {
            if (!isset($out[$c])) {
                $out[$c] = ['revenue' => 0, 'sales' => 0, 'discount' => 0, 'orders' => 0, 'quantity' => 0, 'aov' => 0, 'cust_new' => 0, 'cust_returning' => 0];
            }
            $o = $out[$c]['orders'] ?: 0;
            $out[$c]['aov'] = $o > 0 ? ($out[$c]['revenue'] / $o) : 0;
        }

        return $out;
    }


    /* ==================== PHÂN KHÚC KHÁCH HÀNG TỔNG ==================== */
    public function get_customer_segments_overall($start_date, $end_date, $only_today = false)
    {
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
                    WHERE po.time_status_submitted BETWEEN ? AND ?
                ) ex
                WHERE ex.status_name NOT IN ('canceled','returned','returning')
            ";
            $row = $this->db->query($sql, [$startDT, $endDT])->row_array() ?: ['cust_new' => 0, 'cust_returning' => 0];
        } else {
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
        return $row;
    }

    /* ==================== TỔNG QUAN MARKETER ==================== */
    public function get_marketer_metrics($start_date, $end_date)
    {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Tổng số lượng theo đơn để tính "SL hàng chốt"
        $qtySub = "
        SELECT
            pod.pancake_order_id AS order_id,
            SUM(COALESCE(pod.quantity, 0)) AS qty
        FROM {$tableDetails} pod
        GROUP BY pod.pancake_order_id
    ";

        $sql = "
        SELECT
            TRIM(o.marketer_name) AS marketer_name_key,
            MIN(o.marketer_name)  AS marketer_name,
            COUNT(*)                                                  AS orders,          -- Đơn chốt
            SUM(CAST(IFNULL(o.total_order_amount, 0) AS DECIMAL(18,2))) AS revenue,      -- Doanh thu
            SUM(CAST(IFNULL(o.total_price,       0) AS DECIMAL(18,2)))   AS sales,        -- Doanh số
            SUM(COALESCE(q.qty, 0))                                       AS quantity,     -- SL hàng chốt
            SUM(CAST(IFNULL(o.total_discount,   0) AS DECIMAL(18,2)))     AS discount     -- nếu cần hiển thị
        FROM {$tableOrders} o
        LEFT JOIN ( {$qtySub} ) q ON q.order_id = o.pancake_order_id
        WHERE o.time_status_submitted BETWEEN ? AND ?
          AND TRIM(COALESCE(o.marketer_name, '')) <> ''
          AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
        GROUP BY marketer_name_key
        ORDER BY revenue DESC
    ";

        $q = $this->db->query($sql, [$startDT, $endDT]);
        $rows = is_object($q) ? ($q->result_array() ?: []) : [];

        $out = [];
        foreach ($rows as $r) {
            $name = trim((string)($r['marketer_name'] ?? ''));
            if ($name === '') continue;

            $orders = (int)($r['orders'] ?? 0);
            $revenue = (float)($r['revenue'] ?? 0);

            $out[] = [
                'marketer_name' => $name,
                'revenue'       => $revenue,                               // SUM(total_order_amount)
                'sales'         => (float)($r['sales'] ?? 0),              // SUM(total_price)
                'orders'        => $orders,                                // COUNT(*)
                'quantity'      => (int)($r['quantity'] ?? 0),             // SUM(details.quantity)
                'aov'           => $orders > 0 ? ($revenue / $orders) : 0, // GTTB theo doanh thu hiện tại
                'discount'      => (float)($r['discount'] ?? 0),           // tùy bạn có hiển thị hay không
            ];
        }
        return $out;
    }

    /* ==================== TỔNG QUAN CSKH ==================== */
    public function get_cskh_metrics($start_date, $end_date)
    {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $qtySub = "
        SELECT pod.pancake_order_id AS order_id,
               SUM(COALESCE(pod.quantity, 0)) AS qty
        FROM {$tableDetails} pod
        GROUP BY pod.pancake_order_id
    ";

        $sql = "
        SELECT
            TRIM(o.assigning_care_name) AS cskh_key,
            MIN(o.assigning_care_name)  AS cskh_name,
            COUNT(*)                                                     AS orders,
            SUM(CAST(IFNULL(o.total_order_amount, 0) AS DECIMAL(18,2))) AS revenue,
            SUM(CAST(IFNULL(o.total_price,       0) AS DECIMAL(18,2)))  AS sales,
            SUM(COALESCE(q.qty, 0))                                      AS quantity,
            SUM(CAST(IFNULL(o.total_discount,   0) AS DECIMAL(18,2)))   AS discount
        FROM {$tableOrders} o
        LEFT JOIN ( {$qtySub} ) q ON q.order_id = o.pancake_order_id
        WHERE o.time_status_submitted BETWEEN ? AND ?
          AND COALESCE(TRIM(o.assigning_care_name), '') <> ''
          AND TRIM(o.assigning_care_name) IN (?, ?)
          AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
        GROUP BY cskh_key
        ORDER BY revenue DESC
    ";

        $bindings = [
            $startDT,
            $endDT,
            'Bùi Thị Lan Anh',
            'Hồ Thị Sao Mai',
        ];

        $q = $this->db->query($sql, $bindings);
        $rows = is_object($q) ? ($q->result_array() ?: []) : [];

        $out = [];
        foreach ($rows as $r) {
            $name   = trim((string)($r['cskh_name'] ?? ''));
            if ($name === '') continue;
            $orders = (int)($r['orders'] ?? 0);
            $rev    = (float)($r['revenue'] ?? 0);
            $out[] = [
                'cskh_name' => $name,
                'revenue'   => $rev,
                'sales'     => (float)($r['sales'] ?? 0),
                'orders'    => $orders,
                'quantity'  => (int)($r['quantity'] ?? 0),
                'aov'       => $orders > 0 ? ($rev / $orders) : 0,
                'discount'  => (float)($r['discount'] ?? 0),
            ];
        }
        return $out;
    }

    /* ==================== TỔNG QUAN SALE ==================== */
    public function get_sale_metrics($start_date, $end_date)
    {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $qtySub = "
        SELECT pod.pancake_order_id AS order_id,
               SUM(COALESCE(pod.quantity, 0)) AS qty
        FROM {$tableDetails} pod
        GROUP BY pod.pancake_order_id
    ";

        $sql = "
        SELECT
            TRIM(o.assigning_seller_name) AS sale_key,
            MIN(o.assigning_seller_name)  AS sale_name,
            COUNT(*)                                                     AS orders,
            SUM(CAST(IFNULL(o.total_order_amount, 0) AS DECIMAL(18,2))) AS revenue,
            SUM(CAST(IFNULL(o.total_price,       0) AS DECIMAL(18,2)))  AS sales,
            SUM(COALESCE(q.qty, 0))                                      AS quantity,
            SUM(CAST(IFNULL(o.total_discount,   0) AS DECIMAL(18,2)))   AS discount
        FROM {$tableOrders} o
        LEFT JOIN ( {$qtySub} ) q ON q.order_id = o.pancake_order_id
        WHERE o.time_status_submitted BETWEEN ? AND ?
          AND COALESCE(TRIM(o.assigning_seller_name), '') <> ''
          AND TRIM(o.assigning_seller_name) IN (?, ?, ?)
          AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
        GROUP BY sale_key
        ORDER BY revenue DESC
    ";

        $bindings = [
            $startDT,
            $endDT,
            'Đỗ Thị Thu Thảo',
            'Nguyễn Thị Thu Dung',
            'Lê Huyền Vy',
        ];

        $q = $this->db->query($sql, $bindings);
        $rows = is_object($q) ? ($q->result_array() ?: []) : [];

        $out = [];
        foreach ($rows as $r) {
            $name   = trim((string)($r['sale_name'] ?? ''));
            if ($name === '') continue;
            $orders = (int)($r['orders'] ?? 0);
            $rev    = (float)($r['revenue'] ?? 0);
            $out[] = [
                'sale_name' => $name,
                'revenue'   => $rev,
                'sales'     => (float)($r['sales'] ?? 0),
                'orders'    => $orders,
                'quantity'  => (int)($r['quantity'] ?? 0),
                'aov'       => $orders > 0 ? ($rev / $orders) : 0,
                'discount'  => (float)($r['discount'] ?? 0),
            ];
        }
        return $out;
    }
}
