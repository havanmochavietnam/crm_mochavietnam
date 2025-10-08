<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /* ======================== Utilities ======================== */
    private function placeholders($n)
    {
        return implode(',', array_fill(0, max(1, (int)$n), '?'));
    }

    // Gộp nguồn từ cột DB hoặc JSON; chuẩn hoá UPPER + TRIM (không dùng REGEXP_REPLACE)
    private function _order_source_key_expr($alias = 'o')
    {
        return "
        UPPER(
            TRIM(
                COALESCE(
                    {$alias}.order_sources_name,
                    JSON_UNQUOTE(JSON_EXTRACT({$alias}.data, '$.order_sources_name'))
                )
            )
        )
    ";
    }

    // Gom theo tên: dùng UPPER + TRIM, bỏ REGEXP_REPLACE (không collapse spaces phức tạp trên 5.x)
    private function _name_key_expr($alias = 'd')
    {
        return "UPPER(TRIM(COALESCE({$alias}.product_name, '')))";
    }

    // Khoá khách hàng: loại ký tự phổ biến khỏi số điện thoại bằng REPLACE xếp lớp (MySQL 5.x không có REGEXP_REPLACE)
    private function _customer_key_expr()
    {
        // Loại các ký tự hay gặp: khoảng trắng, -, +, (, ), ., /
        $phoneNorm = "
        NULLIF(
            REPLACE(
            REPLACE(
            REPLACE(
            REPLACE(
            REPLACE(
            REPLACE(
                REPLACE(COALESCE(o.customer_phone, ''), ' ', ''),
            '-', ''),
            '+', ''),
            '(', ''),
            ')', ''),
            '.', ''),
            '/', ''),
        '')
    ";

        return "
        COALESCE(
            {$phoneNorm},
            JSON_UNQUOTE(JSON_EXTRACT(o.data, '$.customer.id')),
            JSON_UNQUOTE(JSON_EXTRACT(o.data, '$.customer.referral_code')),
            CAST(o.pancake_order_id AS CHAR)
        )
    ";
    }

    /* ======================== Tổng doanh thu ======================== */
    public function get_total_revenue_had_status_in_range(string $start_date, string $end_date, array $exclude_sources = ['Tiktok', 'Shopee', 'CTV']): float
    {
        $orders  = db_prefix() . 'pancake_orders';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // ---------------- Chuẩn hóa & mapping nguồn cần loại ----------------
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];

        // Chuẩn hóa UPPER + trim + rút gọn space (khớp với _order_source_key_expr)
        $exclude_sources = array_map(function ($s) {
            $s = preg_replace('/\s+/', ' ', trim((string)$s));
            return mb_strtoupper($s, 'UTF-8');
        }, $exclude_sources);

        // Mapping alias: CTV <-> AFFILIATE (truyền 1 vẫn loại được cả 2)
        $need_aff = in_array('CTV', $exclude_sources, true) && !in_array('AFFILIATE', $exclude_sources, true);
        $need_ctv = in_array('AFFILIATE', $exclude_sources, true) && !in_array('CTV', $exclude_sources, true);
        if ($need_aff) $exclude_sources[] = 'AFFILIATE';
        if ($need_ctv) $exclude_sources[] = 'CTV';

        $exclude_sources = array_values(array_unique($exclude_sources));
        $ph = $this->placeholders(count($exclude_sources));

        $orderTotalExpr = "CAST(IFNULL(o.total_order_amount, 0) AS DECIMAL(18,6))";
        $srcKey         = $this->_order_source_key_expr('o'); // khóa nguồn hợp nhất (DB/JSON)

        // ---------------- SQL: lọc theo time_status_submitted (đÃ xử lý TZ ở DB, KHÔNG cộng offset) ----------------
        $sql = "
        SELECT
            COALESCE(SUM({$orderTotalExpr}), 0) AS total_revenue
        FROM {$orders} o
        WHERE o.time_status_submitted BETWEEN ? AND ?
          AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
          AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
    ";

        // ---------------- Bind ----------------
        $bind = [];
        $bind[] = $startDT;                    // BETWEEN start (đã là local, không cộng offset)
        $bind[] = $endDT;                      // BETWEEN end
        $bind = array_merge($bind, $exclude_sources); // NOT IN unified sources

        $row = $this->db->query($sql, $bind)->row_array() ?: [];
        return (float)($row['total_revenue'] ?? 0);
    }

    /* ======================== Tổng thể sản phẩm lẻ(có lọc ≥ lần 2) ======================== */
    public function get_product_revenue_breakdown(string $start_date, string $end_date, array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'], bool $exclude_first = false, int $limit = 100, int $offset = 0): array
    {
        $orders  = db_prefix() . 'pancake_orders';
        $details = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // ===== Chuẩn hóa & mapping nguồn cần loại =====
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];

        $exclude_sources = array_map(function ($s) {
            $s = preg_replace('/\s+/', ' ', trim((string)$s));
            return mb_strtoupper($s, 'UTF-8');
        }, $exclude_sources);

        // mapping alias: CTV <-> AFFILIATE
        $need_aff = in_array('CTV', $exclude_sources, true) && !in_array('AFFILIATE', $exclude_sources, true);
        $need_ctv = in_array('AFFILIATE', $exclude_sources, true) && !in_array('CTV', $exclude_sources, true);
        if ($need_aff) $exclude_sources[] = 'AFFILIATE';
        if ($need_ctv) $exclude_sources[] = 'CTV';

        $exclude_sources = array_values(array_unique($exclude_sources));
        $ph = $this->placeholders(count($exclude_sources));

        // ===== Điều kiện & biểu thức dùng chung =====
        $whereNonCombo = "d.is_combo = 0"; // KHÔNG loại quà tặng
        $whereAlloc    = "1=1";            // phân bổ discount trên tất cả dòng

        $nameKey    = $this->_name_key_expr('d');
        $nameKeyAll = $this->_name_key_expr('d_all');
        $custKey    = $this->_customer_key_expr();
        $srcKey     = $this->_order_source_key_expr('o');   // khóa nguồn hợp nhất (DB/JSON)

        $lineNet = "
        (CAST(IFNULL(d.unit_price, 0) AS DECIMAL(18,6)) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6)))
        - CAST(IFNULL(d.total_discount, 0) AS DECIMAL(18,6))
    ";

        // ===== SQL: dùng time_status_submitted thay cho status history =====
        $sql = "
        /* Tổng quantity theo đơn (để phân bổ discount theo quantity) */
        WITH q_all AS (
            SELECT o.pancake_order_id,
                   SUM(CAST(IFNULL(dd.quantity, 0) AS DECIMAL(18,6))) AS sum_qty_all
            FROM {$orders} o
            JOIN {$details} dd ON dd.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND {$whereAlloc}
            GROUP BY o.pancake_order_id
        ),
        /* Lifetime history để xếp thứ tự lần mua theo (product_key, customer_key) */
        base_all AS (
            SELECT
                {$nameKeyAll} AS product_key,
                o.pancake_order_id,
                {$custKey} AS customer_key,
                /* dùng time_status_submitted làm mốc thời gian */
                o.time_status_submitted AS confirmed_at
            FROM {$orders} o
            JOIN {$details} d_all ON d_all.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND d_all.is_combo = 0
              AND {$custKey} IS NOT NULL
            GROUP BY {$nameKeyAll}, o.pancake_order_id, {$custKey}, confirmed_at
        ),
        ranked AS (
            SELECT
                product_key, pancake_order_id, customer_key, confirmed_at,
                ROW_NUMBER() OVER (PARTITION BY product_key, customer_key ORDER BY confirmed_at) AS rn
            FROM base_all
        ),
        /* Kết quả theo sản phẩm trong khoảng thời gian */
        s AS (
            SELECT
                {$nameKey}                          AS product_key,
                MIN(COALESCE(d.product_name, ''))   AS product_name,
                MIN(COALESCE(d.image_url, ''))      AS image_url,
                GROUP_CONCAT(DISTINCT o.pancake_order_id ORDER BY o.pancake_order_id SEPARATOR ', ') AS order_ids,
                /* Doanh thu: gross - detail.discount - phần order.discount phân bổ theo quantity */
                SUM(
                    {$lineNet}
                    - CASE
                        WHEN COALESCE(q_all.sum_qty_all, 0) > 0
                          THEN ( CAST(IFNULL(JSON_EXTRACT(o.data, '$.total_discount'), 0) AS DECIMAL(18,6))
                                 / q_all.sum_qty_all
                               ) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6))
                        ELSE 0
                      END
                )                                    AS revenue,
                /* Số lượng sản phẩm bán ra (đáp ứng yêu cầu đếm 2 dòng giống nhau trong 1 đơn) */
                SUM(CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6)))     AS units,
                /* Số đơn duy nhất có chứa sản phẩm */
                COUNT(DISTINCT o.pancake_order_id)                     AS orders
            FROM {$orders} o
            JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
            LEFT JOIN q_all ON q_all.pancake_order_id = o.pancake_order_id
            LEFT JOIN ranked r
                   ON r.pancake_order_id = o.pancake_order_id
                  AND r.product_key      = {$nameKey}
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND {$whereNonCombo}
              AND ( ? = 0 OR r.rn >= 2 )
            GROUP BY {$nameKey}
        ),
        /* ===== Repeat-rate & Avg repurchase time trong khoảng ===== */
        base_rep AS (
            SELECT
                {$nameKey} AS product_key,
                o.pancake_order_id,
                {$custKey} AS customer_key,
                o.time_status_submitted AS confirmed_at
            FROM {$orders} o
            JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND {$whereNonCombo}
              AND {$custKey} IS NOT NULL
        ),
        agg_rep AS (
            SELECT product_key, customer_key,
                   COUNT(DISTINCT pancake_order_id) AS order_cnt
            FROM base_rep
            GROUP BY product_key, customer_key
        ),
        rep AS (
            SELECT product_key,
                   COUNT(*) AS unique_buyers,
                   SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) AS repeat_buyers,
                   CASE WHEN COUNT(*) > 0
                        THEN SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) / COUNT(*)
                        ELSE NULL END AS repeat_rate
            FROM agg_rep
            GROUP BY product_key
        ),
        rep_time AS (
            SELECT product_key, customer_key,
                   AVG(DATEDIFF(next_confirmed_at, confirmed_at)) AS avg_days
            FROM (
                SELECT product_key, customer_key, confirmed_at,
                       LEAD(confirmed_at) OVER (PARTITION BY product_key, customer_key ORDER BY confirmed_at) AS next_confirmed_at
                FROM base_rep
            ) t
            WHERE next_confirmed_at IS NOT NULL
            GROUP BY product_key, customer_key
        ),
        avg_time AS (
            SELECT product_key,
                   AVG(avg_days) AS avg_days_between
            FROM rep_time
            GROUP BY product_key
        )
        SELECT
            NULL AS product_id,
            s.product_name,
            s.image_url,
            s.revenue,
            s.orders,
            s.units,
            s.order_ids,
            CASE WHEN s.orders > 0 THEN s.revenue / s.orders ELSE 0 END AS aov,
            rep.repeat_rate,
            avg_time.avg_days_between
        FROM s
        LEFT JOIN rep ON rep.product_key = s.product_key
        LEFT JOIN avg_time ON avg_time.product_key = s.product_key
        ORDER BY s.revenue DESC
        LIMIT ? OFFSET ?
    ";

        // ===== Bind (theo thứ tự xuất hiện trong SQL) =====
        $bind = [];
        // q_all BETWEEN
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);    // q_all NOT IN

        $bind = array_merge($bind, $exclude_sources);    // base_all NOT IN

        // s BETWEEN
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);    // s NOT IN
        $bind[] = $exclude_first ? 1 : 0;                // toggle rn>=2

        // base_rep BETWEEN
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);    // base_rep NOT IN

        // limit/offset
        $bind[] = (int)$limit;
        $bind[] = (int)$offset;

        // ===== Execute =====
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $rev   = (float)($r['revenue'] ?? 0);
            $ord   = (int)  ($r['orders']  ?? 0);          // số ĐƠN
            $units = (float)($r['units']   ?? 0);          // số LƯỢNG SP
            $rate  = isset($r['repeat_rate']) ? (float)$r['repeat_rate'] : null;

            $r['revenue']           = $rev;                          // có thể âm
            $r['orders']            = $ord;                          // giữ nguyên field 'orders' = số đơn
            $r['units']             = $units;                        // thêm field 'units' = số lượng bán
            $r['aov']               = $ord > 0 ? ($rev / $ord) : 0.0; // AOV theo đơn (giữ nguyên)
            $r['repurchase_rate']   = is_null($rate) ? null : ($rate * 100.0);
            $r['avg_days_between']  = isset($r['avg_days_between']) ? (float)$r['avg_days_between'] : null;
            $r['product_id']        = null;
            $r['product_name']      = $r['product_name'] ?? '';
            $r['image_url']         = $r['image_url'] ?? '';
            $r['order_ids']         = $r['order_ids'] ?? '';
        }
        unset($r);
        return $rows;
    }


    /* ======================== Tổng thể Combo (có lọc ≥ lần 2) ======================== */
    public function get_combo_revenue_breakdown(string $start_date, string $end_date, array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'], bool $exclude_first = false, int $limit = 100, int $offset = 0): array
    {
        $orders  = db_prefix() . 'pancake_orders';
        $details = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // ===== Chuẩn hóa & mapping danh sách nguồn cần loại =====
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];

        // chuẩn hóa UPPER + trim + rút gọn space (khớp với _order_source_key_expr)
        $exclude_sources = array_map(function ($s) {
            $s = preg_replace('/\s+/', ' ', trim((string)$s));
            return mb_strtoupper($s, 'UTF-8');
        }, $exclude_sources);

        // mapping alias CTV <-> AFFILIATE: truyền 1 vẫn loại được cả 2
        $need_aff = in_array('CTV', $exclude_sources, true) && !in_array('AFFILIATE', $exclude_sources, true);
        $need_ctv = in_array('AFFILIATE', $exclude_sources, true) && !in_array('CTV', $exclude_sources, true);
        if ($need_aff) $exclude_sources[] = 'AFFILIATE';
        if ($need_ctv) $exclude_sources[] = 'CTV';

        $exclude_sources = array_values(array_unique($exclude_sources));
        $ph = $this->placeholders(count($exclude_sources));

        // ===== Điều kiện & biểu thức dùng chung =====
        $whereCombo = "d.is_combo = 1"; // KHÔNG loại quà tặng
        $whereAlloc = "1=1";            // phân bổ discount trên tất cả dòng

        $nameKey    = $this->_name_key_expr('d');
        $nameKeyAll = $this->_name_key_expr('d_all');
        $custKey    = $this->_customer_key_expr();
        $srcKey     = $this->_order_source_key_expr('o'); // khóa nguồn hợp nhất (DB/JSON)

        $lineNet = "
        (CAST(IFNULL(d.unit_price, 0) AS DECIMAL(18,6)) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6)))
        - CAST(IFNULL(d.total_discount, 0) AS DECIMAL(18,6))
    ";

        // ===== SQL: dùng time_status_submitted thay cho status history =====
        $sql = "
        /* Tổng quantity theo đơn (để phân bổ discount theo quantity) */
        WITH q_all AS (
            SELECT o.pancake_order_id,
                   SUM(CAST(IFNULL(dd.quantity, 0) AS DECIMAL(18,6))) AS sum_qty_all
            FROM {$orders} o
            JOIN {$details} dd ON dd.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND {$whereAlloc}
            GROUP BY o.pancake_order_id
        ),
        /* Lifetime history để xếp thứ tự lần mua theo (combo_key, customer_key) */
        base_all AS (
            SELECT
                {$nameKeyAll} AS product_key,
                o.pancake_order_id,
                {$custKey} AS customer_key,
                o.time_status_submitted AS confirmed_at
            FROM {$orders} o
            JOIN {$details} d_all ON d_all.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND d_all.is_combo = 1
              AND {$custKey} IS NOT NULL
            GROUP BY {$nameKeyAll}, o.pancake_order_id, {$custKey}, confirmed_at
        ),
        ranked AS (
            SELECT
                product_key, pancake_order_id, customer_key, confirmed_at,
                ROW_NUMBER() OVER (PARTITION BY product_key, customer_key ORDER BY confirmed_at) AS rn
            FROM base_all
        ),
        /* Kết quả theo combo trong khoảng thời gian */
        s AS (
            SELECT
                {$nameKey}                        AS product_key,
                MIN(COALESCE(d.product_name, '')) AS product_name,
                MIN(COALESCE(d.image_url, ''))    AS image_url,
                SUM(
                    {$lineNet}
                    - CASE
                        WHEN COALESCE(q_all.sum_qty_all, 0) > 0
                          THEN ( CAST(IFNULL(JSON_EXTRACT(o.data, '$.total_discount'), 0) AS DECIMAL(18,6))
                                 / q_all.sum_qty_all
                               ) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6))
                        ELSE 0
                      END
                )                                  AS revenue,
                COUNT(DISTINCT o.pancake_order_id) AS orders
            FROM {$orders} o
            JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
            LEFT JOIN q_all ON q_all.pancake_order_id = o.pancake_order_id
            LEFT JOIN ranked r
                   ON r.pancake_order_id = o.pancake_order_id
                  AND r.product_key      = {$nameKey}
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND {$whereCombo}
              AND ( ? = 0 OR r.rn >= 2 )
            GROUP BY {$nameKey}
        ),
        /* ===== Repeat-rate & Avg repurchase time trong khoảng ===== */
        base_rep AS (
            SELECT
                {$nameKey} AS product_key,
                o.pancake_order_id,
                {$custKey} AS customer_key,
                o.time_status_submitted AS confirmed_at
            FROM {$orders} o
            JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND ({$srcKey} NOT IN ({$ph}) OR {$srcKey} IS NULL)
              AND d.is_combo = 1
              AND {$custKey} IS NOT NULL
        ),
        agg_rep AS (
            SELECT product_key, customer_key,
                   COUNT(DISTINCT pancake_order_id) AS order_cnt
            FROM base_rep
            GROUP BY product_key, customer_key
        ),
        rep AS (
            SELECT product_key,
                   COUNT(*) AS unique_buyers,
                   SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) AS repeat_buyers,
                   CASE WHEN COUNT(*) > 0
                        THEN SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) / COUNT(*)
                        ELSE NULL END AS repeat_rate
            FROM agg_rep
            GROUP BY product_key
        ),
        rep_time AS (
            SELECT product_key, customer_key,
                   AVG(DATEDIFF(next_confirmed_at, confirmed_at)) AS avg_days
            FROM (
                SELECT product_key, customer_key, confirmed_at,
                       LEAD(confirmed_at) OVER (PARTITION BY product_key, customer_key ORDER BY confirmed_at) AS next_confirmed_at
                FROM base_rep
            ) t
            WHERE next_confirmed_at IS NOT NULL
            GROUP BY product_key, customer_key
        ),
        avg_time AS (
            SELECT product_key,
                   AVG(avg_days) AS avg_days_between
            FROM rep_time
            GROUP BY product_key
        )
        SELECT
            NULL AS product_id,
            s.product_name,
            s.image_url,
            s.revenue,
            s.orders,
            CASE WHEN s.orders > 0 THEN s.revenue / s.orders ELSE 0 END AS aov,
            rep.repeat_rate,
            avg_time.avg_days_between
        FROM s
        LEFT JOIN rep ON rep.product_key = s.product_key
        LEFT JOIN avg_time ON avg_time.product_key = s.product_key
        ORDER BY s.revenue DESC
        LIMIT ? OFFSET ?
    ";

        // ===== Bind =====
        $bind = [];
        // q_all BETWEEN
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);    // q_all NOT IN

        // base_all NOT IN (lifetime)
        $bind = array_merge($bind, $exclude_sources);

        // s BETWEEN + NOT IN + toggle rn
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);
        $bind[] = $exclude_first ? 1 : 0;

        // base_rep BETWEEN + NOT IN
        $bind[] = $startDT;
        $bind[] = $endDT;
        $bind = array_merge($bind, $exclude_sources);

        // limit/offset
        $bind[] = (int)$limit;
        $bind[] = (int)$offset;

        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['revenue']          = (float)($r['revenue'] ?? 0);  // có thể âm
            $r['orders']           = (int)  ($r['orders'] ?? 0);
            $r['aov']              = $r['orders'] > 0 ? ((float)$r['revenue'] / (int)$r['orders']) : 0.0; // có thể âm
            $rate                  = isset($r['repeat_rate']) ? (float)$r['repeat_rate'] : null;
            $r['repurchase_rate']  = is_null($rate) ? null : ($rate * 100.0);
            $r['avg_days_between'] = isset($r['avg_days_between']) ? (float)$r['avg_days_between'] : null;
            $r['product_id']       = null;
            $r['product_name']     = $r['product_name'] ?? '';
            $r['image_url']        = $r['image_url'] ?? '';
        }
        unset($r);

        return $rows;
    }

    /* ======================== Repeat-rate Combo ======================== */
    public function get_combo_repeat_rate_breakdown(
        string $start_date,
        string $end_date,
        array $exclude_sources = ['Tiktok', 'Shopee', 'CTV'],
        int $repeat_threshold = 2,
        int $limit = 100,
        int $offset = 0
    ): array {
        $orders  = db_prefix() . 'pancake_orders';
        $details = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Nguồn cần loại (để chống rỗng)
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->placeholders(count($exclude_sources));

        // Chỉ lấy combo, KHÔNG loại quà tặng (is_gift) nữa
        $whereCombo = "d.is_combo = 1";

        $nameKey = $this->_name_key_expr('d');   // khóa sản phẩm (đã có sẵn trong model của bạn)
        $custKey = $this->_customer_key_expr();  // khóa khách hàng (đã có sẵn trong model của bạn)

        $sql = "
        WITH base AS (
            SELECT
                {$nameKey} AS product_key,
                MIN(COALESCE(d.product_name, '')) AS product_name,
                o.pancake_order_id,
                {$custKey} AS customer_key
            FROM {$orders} o
            JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND o.time_status_submitted BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
              AND {$whereCombo}
              AND {$custKey} IS NOT NULL
            GROUP BY {$nameKey}, o.pancake_order_id, {$custKey}
        ),
        agg AS (
            SELECT
                product_key, product_name, customer_key,
                COUNT(DISTINCT pancake_order_id) AS order_cnt
            FROM base
            GROUP BY product_key, product_name, customer_key
        )
        SELECT
            NULL AS product_id,
            MIN(product_name) AS product_name,
            COUNT(*) AS unique_buyers,
            SUM(CASE WHEN order_cnt >= ? THEN 1 ELSE 0 END) AS repeat_buyers,
            CASE
                WHEN COUNT(*) > 0 THEN
                    CAST(SUM(CASE WHEN order_cnt >= ? THEN 1 ELSE 0 END) AS DECIMAL(18,6))
                    / CAST(COUNT(*) AS DECIMAL(18,6))
                ELSE 0
            END AS repeat_rate
        FROM agg
        GROUP BY product_key
        ORDER BY repeat_rate DESC, unique_buyers DESC
        LIMIT ? OFFSET ?
    ";

        $bind = array_merge(
            [$startDT, $endDT],
            $exclude_sources,
            [$repeat_threshold, $repeat_threshold, (int)$limit, (int)$offset]
        );

        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['unique_buyers'] = (int)($r['unique_buyers'] ?? 0);
            $r['repeat_buyers'] = (int)($r['repeat_buyers'] ?? 0);
            $r['repeat_rate']   = ($r['unique_buyers'] > 0)
                ? ((float)$r['repeat_buyers']) / (float)$r['unique_buyers']
                : 0.0;
            $r['product_id']    = null;
            $r['product_name']  = $r['product_name'] ?? '';
        }
        unset($r);

        return $rows;
    }


    public function get_combo_repeat_rate_overall(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $exclude_gifts = true,
        string $tz_offset_sql = 'INTERVAL 7 HOUR',
        int $repeat_threshold = 2
    ): array {
        $orders  = db_prefix() . 'pancake_orders';
        $details = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->placeholders(count($exclude_sources));

        $whereCombo = "d.is_combo = 1";
        if ($exclude_gifts) $whereCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";

        $custKey = $this->_customer_key_expr();

        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$orders} po2
                JOIN JSON_TABLE(
                    CASE WHEN JSON_VALID(po2.data) THEN po2.data ELSE JSON_ARRAY() END,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = ?
                GROUP BY po2.pancake_order_id
            ),
            base AS (
                SELECT
                    d.product_id,
                    o.pancake_order_id,
                    {$custKey} AS customer_key
                FROM {$orders} o
                JOIN c ON c.pancake_order_id = o.pancake_order_id
                JOIN {$details} d ON d.pancake_order_id = o.pancake_order_id
                WHERE JSON_VALID(o.data) = 1
                  AND c.confirmed_at BETWEEN ? AND ?
                  AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                  AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
                  AND {$whereCombo}
                  AND {$custKey} IS NOT NULL
            ),
            agg AS (
                SELECT product_id, customer_key,
                       COUNT(DISTINCT pancake_order_id) AS order_cnt
                FROM base
                GROUP BY product_id, customer_key
            )
            SELECT
                COUNT(*) AS unique_customer_combo_pairs,
                SUM(CASE WHEN order_cnt >= ? THEN 1 ELSE 0 END) AS repeat_customer_combo_pairs,
                CASE
                    WHEN COUNT(*) > 0
                        THEN CAST(SUM(CASE WHEN order_cnt >= ? THEN 1 ELSE 0 END) AS DECIMAL(18,6))
                             / CAST(COUNT(*) AS DECIMAL(18,6))
                    ELSE 0
                END AS repeat_rate
            FROM agg
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources, [$repeat_threshold, $repeat_threshold]);
        $row  = $this->db->query($sql, $bind)->row_array() ?: [];

        return [
            'unique_customer_combo_pairs' => (int)($row['unique_customer_combo_pairs'] ?? 0),
            'repeat_customer_combo_pairs' => (int)($row['repeat_customer_combo_pairs'] ?? 0),
            'repeat_rate'                 => (float)($row['repeat_rate'] ?? 0),
        ];
    }

    /** Hôm nay */
    public function get_total_revenue_confirmed_today_from_details(
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
    ): float {
        $today = date('Y-m-d');
        return $this->get_total_revenue_had_status_in_range(
            $today,
            $today,
            $exclude_sources
        );
    }
}
