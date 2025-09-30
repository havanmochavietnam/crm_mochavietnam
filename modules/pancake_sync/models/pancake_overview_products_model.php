<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function placeholders($n)
    {
        return implode(',', array_fill(0, max(1, (int)$n), '?'));
    }

    public function get_total_revenue_had_status_in_range(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $use_quantity = false, // giữ để tương thích, KHÔNG dùng
        string $tz_offset_sql = 'INTERVAL 7 HOUR'
    ): float {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Nguồn loại trừ
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) {
            $exclude_sources = ['__NO_SOURCE__'];
        }
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        $itemRevenueExpr = "
            (CAST(IFNULL(d.unit_price, 0) AS DECIMAL(18,2)) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,2)))
            - CAST(IFNULL(d.total_discount, 0) AS DECIMAL(18,2))
        ";

        $sql = "
            SELECT COALESCE(SUM(per_order.items_sum - per_order.order_discount), 0) AS total_revenue
            FROM (
                SELECT
                    o.pancake_order_id,
                    COALESCE(SUM({$itemRevenueExpr}), 0) AS items_sum,
                    CAST(IFNULL(JSON_EXTRACT(o.data, '$.total_discount'), 0) AS DECIMAL(18,2)) AS order_discount
                FROM {$tableOrders} o
                JOIN (
                    SELECT
                        po2.pancake_order_id,
                        MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                    FROM {$tableOrders} po2
                    JOIN JSON_TABLE(
                        CASE WHEN JSON_VALID(po2.data) THEN po2.data ELSE JSON_ARRAY() END,
                        '$.status_history[*]'
                        COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                    ) h ON h.status = ?
                    GROUP BY po2.pancake_order_id
                ) c ON c.pancake_order_id = o.pancake_order_id
                JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
                WHERE JSON_VALID(o.data) = 1
                  AND c.confirmed_at BETWEEN ? AND ?
                  AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                  AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
                GROUP BY o.pancake_order_id, order_discount
            ) AS per_order
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources);
        $row  = $this->db->query($sql, $bind)->row_array();
        return (float)($row['total_revenue'] ?? 0);
    }

    public function get_product_revenue_breakdown(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $exclude_gifts = true,
        string $tz_offset_sql = 'INTERVAL 7 HOUR',
        int $limit = 100,
        int $offset = 0
    ): array {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        // Chỉ lấy sản phẩm lẻ
        $whereNonCombo = "d.is_combo = 0";
        if ($exclude_gifts) {
            $whereNonCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";
        }

        // Mẫu số phân bổ: tổng quantity các dòng (không tách combo/non-combo), có thể loại gift
        $whereAllForAlloc = $exclude_gifts ? "(dd.is_gift IS NULL OR dd.is_gift = 0)" : "1=1";

        // Net trên từng dòng trước khi phân bổ discount cấp đơn
        $lineNetExpr = "
            (CAST(IFNULL(d.unit_price, 0) AS DECIMAL(18,6)) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6)))
            - CAST(IFNULL(d.total_discount, 0) AS DECIMAL(18,6))
        ";

        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$tableOrders} po2
                JOIN JSON_TABLE(
                    CASE WHEN JSON_VALID(po2.data) THEN po2.data ELSE JSON_ARRAY() END,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = ?
                GROUP BY po2.pancake_order_id
            ),
            q_all AS (
                SELECT o.pancake_order_id,
                       SUM(CAST(IFNULL(dd.quantity, 0) AS DECIMAL(18,6))) AS sum_qty_all
                FROM {$tableOrders} o
                JOIN {$tableDetails} dd ON dd.pancake_order_id = o.pancake_order_id
                WHERE {$whereAllForAlloc}
                GROUP BY o.pancake_order_id
            )
            SELECT
                d.product_id,
                COALESCE(d.product_name, '') AS product_name,
                COALESCE(d.image_url, '')   AS image_url,
                -- Doanh thu sản phẩm (đã phân bổ discount đơn theo quantity)
                SUM(
                    {$lineNetExpr}
                    - CASE
                        WHEN COALESCE(q_all.sum_qty_all, 0) > 0
                          THEN ( CAST(IFNULL(JSON_EXTRACT(o.data, '$.total_discount'), 0) AS DECIMAL(18,6))
                                 / q_all.sum_qty_all
                               ) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6))
                        ELSE 0
                      END
                ) AS revenue,
                -- NEW: số ĐƠN DISTINCT có chứa sản phẩm này (đúng “có thì 1”)
                COUNT(DISTINCT o.pancake_order_id) AS orders
            FROM {$tableOrders} o
            JOIN c      ON c.pancake_order_id = o.pancake_order_id
            JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
            LEFT JOIN q_all ON q_all.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND c.confirmed_at BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
              AND {$whereNonCombo}
            GROUP BY d.product_id, d.product_name, d.image_url
            ORDER BY revenue DESC
            LIMIT ? OFFSET ?
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources, [$limit, $offset]);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['revenue']      = (float)($r['revenue'] ?? 0);
            $r['orders']       = (int)  ($r['orders']  ?? 0); // NEW: số đơn distinct
            $r['product_id']   = $r['product_id'] ?? null;
            $r['product_name'] = $r['product_name'] ?? '';
            $r['image_url']    = $r['image_url'] ?? '';
        }
        unset($r);
        return $rows;
    }

    public function get_combo_revenue_breakdown(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $exclude_gifts = true,
        string $tz_offset_sql = 'INTERVAL 7 HOUR',
        int $limit = 100,
        int $offset = 0
    ): array {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        // Chỉ combo (+ loại gift nếu chọn)
        $whereCombo = "d.is_combo = 1";
        if ($exclude_gifts) {
            $whereCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";
        }
        // Mẫu số phân bổ discount theo tổng quantity các dòng (loại gift nếu chọn)
        $whereAllForAlloc = $exclude_gifts ? "(dd.is_gift IS NULL OR dd.is_gift = 0)" : "1=1";

        $lineNetExpr = "
            (CAST(IFNULL(d.unit_price, 0) AS DECIMAL(18,6)) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6)))
            - CAST(IFNULL(d.total_discount, 0) AS DECIMAL(18,6))
        ";

        // Bọc thêm CTE 's' để gom revenue + orders, rồi ngoài cùng tính AOV
        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$tableOrders} po2
                JOIN JSON_TABLE(
                    CASE WHEN JSON_VALID(po2.data) THEN po2.data ELSE JSON_ARRAY() END,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = ?
                GROUP BY po2.pancake_order_id
            ),
            q_all AS (
                SELECT o.pancake_order_id,
                       SUM(CAST(IFNULL(dd.quantity, 0) AS DECIMAL(18,6))) AS sum_qty_all
                FROM {$tableOrders} o
                JOIN {$tableDetails} dd ON dd.pancake_order_id = o.pancake_order_id
                WHERE {$whereAllForAlloc}
                GROUP BY o.pancake_order_id
            ),
            s AS (
                SELECT
                    d.product_id,
                    COALESCE(d.product_name, '') AS product_name,
                    COALESCE(d.image_url, '')   AS image_url,
                    -- Revenue combo (đã phân bổ discount đơn theo quantity)
                    SUM(
                        {$lineNetExpr}
                        - CASE
                            WHEN COALESCE(q_all.sum_qty_all, 0) > 0
                              THEN ( CAST(IFNULL(JSON_EXTRACT(o.data, '$.total_discount'), 0) AS DECIMAL(18,6))
                                     / q_all.sum_qty_all
                                   ) * CAST(IFNULL(d.quantity, 0) AS DECIMAL(18,6))
                            ELSE 0
                          END
                    ) AS revenue,
                    -- Số đơn chứa combo này
                    COUNT(DISTINCT o.pancake_order_id) AS orders
                FROM {$tableOrders} o
                JOIN c      ON c.pancake_order_id = o.pancake_order_id
                JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
                LEFT JOIN q_all ON q_all.pancake_order_id = o.pancake_order_id
                WHERE JSON_VALID(o.data) = 1
                  AND c.confirmed_at BETWEEN ? AND ?
                  AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                  AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
                  AND {$whereCombo}
                GROUP BY d.product_id, d.product_name, d.image_url
            )
            SELECT
                s.product_id,
                s.product_name,
                s.image_url,
                s.revenue,
                s.orders,
                CASE WHEN s.orders > 0 THEN s.revenue / s.orders ELSE 0 END AS aov
            FROM s
            ORDER BY s.revenue DESC
            LIMIT ? OFFSET ?
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources, [$limit, $offset]);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['revenue']      = (float)($r['revenue'] ?? 0);
            $r['orders']       = (int)  ($r['orders'] ?? 0);
            $r['aov']          = $r['orders'] > 0 ? (float)$r['revenue'] / (int)$r['orders'] : 0.0;
            $r['product_id']   = $r['product_id'] ?? null;
            $r['product_name'] = $r['product_name'] ?? '';
            $r['image_url']    = $r['image_url'] ?? '';
        }
        unset($r);
        return $rows;
    }

    /**
     * NEW: Trả về số ĐƠN DISTINCT theo từng sản phẩm lẻ (is_combo=0)
     * Dùng chung bộ lọc status / nguồn / gift / khoảng ngày như các hàm khác.
     * Mục đích: nếu controller muốn gọi riêng để merge.
     */
    public function get_product_order_counts_distinct(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $exclude_gifts = true,
        string $tz_offset_sql = 'INTERVAL 7 HOUR'
    ): array {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        $whereNonCombo = "d.is_combo = 0";
        if ($exclude_gifts) {
            $whereNonCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";
        }

        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$tableOrders} po2
                JOIN JSON_TABLE(
                    CASE WHEN JSON_VALID(po2.data) THEN po2.data ELSE JSON_ARRAY() END,
                    '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                ) h ON h.status = ?
                GROUP BY po2.pancake_order_id
            )
            SELECT
                d.product_id,
                COALESCE(d.product_name, '') AS product_name,
                COUNT(DISTINCT o.pancake_order_id) AS orders
            FROM {$tableOrders} o
            JOIN c      ON c.pancake_order_id = o.pancake_order_id
            JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
            WHERE JSON_VALID(o.data) = 1
              AND c.confirmed_at BETWEEN ? AND ?
              AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
              AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
              AND {$whereNonCombo}
            GROUP BY d.product_id, d.product_name
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['orders']       = (int)($r['orders'] ?? 0);
            $r['product_id']   = $r['product_id'] ?? null;
            $r['product_name'] = $r['product_name'] ?? '';
        }
        unset($r);
        return $rows;
    }

    public function get_combo_repeat_rate_breakdown(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $exclude_gifts = true,
        string $tz_offset_sql = 'INTERVAL 7 HOUR',
        int $repeat_threshold = 2,   // >=2 đơn được tính là "quay lại"
        int $limit = 100,
        int $offset = 0
    ): array {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        // Nguồn loại trừ
        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        // Chỉ dòng combo; tuỳ chọn loại gift
        $whereCombo = "d.is_combo = 1";
        if ($exclude_gifts) {
            $whereCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";
        }

        // Khoá khách hàng: phone (lọc chỉ số)
        $customerKeyExpr = "
            COALESCE(
                NULLIF(REGEXP_REPLACE(COALESCE(o.customer_phone, ''), '[^0-9]', ''), '')
            )
        ";

        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$tableOrders} po2
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
                    COALESCE(d.product_name, '') AS product_name,
                    o.pancake_order_id,
                    {$customerKeyExpr} AS customer_key
                FROM {$tableOrders} o
                JOIN c ON c.pancake_order_id = o.pancake_order_id
                JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
                WHERE JSON_VALID(o.data) = 1
                  AND c.confirmed_at BETWEEN ? AND ?
                  AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                  AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
                  AND {$whereCombo}
                  AND {$customerKeyExpr} IS NOT NULL
            ),
            agg AS (
                SELECT
                    product_id,
                    product_name,
                    customer_key,
                    COUNT(DISTINCT pancake_order_id) AS order_cnt
                FROM base
                GROUP BY product_id, product_name, customer_key
            )
            SELECT
                x.product_id,
                x.product_name,
                x.unique_buyers,
                x.repeat_buyers,
                CASE
                    WHEN x.unique_buyers > 0
                        THEN CAST(x.repeat_buyers AS DECIMAL(18,6)) / CAST(x.unique_buyers AS DECIMAL(18,6))
                    ELSE 0
                END AS repeat_rate
            FROM (
                SELECT
                    product_id,
                    product_name,
                    COUNT(*) AS unique_buyers,
                    SUM(CASE WHEN order_cnt >= ? THEN 1 ELSE 0 END) AS repeat_buyers
                FROM agg
                GROUP BY product_id, product_name
            ) AS x
            ORDER BY repeat_rate DESC, unique_buyers DESC
            LIMIT ? OFFSET ?
        ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources, [$repeat_threshold, $limit, $offset]);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        foreach ($rows as &$r) {
            $r['unique_buyers'] = (int)($r['unique_buyers'] ?? 0);
            $r['repeat_buyers'] = (int)($r['repeat_buyers'] ?? 0);
            $r['repeat_rate']   = ($r['unique_buyers'] > 0)
                ? ((float)$r['repeat_buyers']) / (float)$r['unique_buyers']
                : 0.0;
            $r['product_id']    = $r['product_id'] ?? null;
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
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = implode(',', array_fill(0, count($exclude_sources), '?'));

        $whereCombo = "d.is_combo = 1";
        if ($exclude_gifts) {
            $whereCombo .= " AND (d.is_gift IS NULL OR d.is_gift = 0)";
        }

        $customerKeyExpr = "
            COALESCE(
                NULLIF(REGEXP_REPLACE(COALESCE(o.customer_phone, ''), '[^0-9]', ''), '')
            )
        ";

        $sql = "
            WITH c AS (
                SELECT po2.pancake_order_id,
                       MAX(DATE_ADD(h.updated_at, {$tz_offset_sql})) AS confirmed_at
                FROM {$tableOrders} po2
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
                    {$customerKeyExpr} AS customer_key
                FROM {$tableOrders} o
                JOIN c ON c.pancake_order_id = o.pancake_order_id
                JOIN {$tableDetails} d ON d.pancake_order_id = o.pancake_order_id
                WHERE JSON_VALID(o.data) = 1
                  AND c.confirmed_at BETWEEN ? AND ?
                  AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                  AND (o.order_sources_name NOT IN ({$ph}) OR o.order_sources_name IS NULL)
                  AND {$whereCombo}
                  AND {$customerKeyExpr} IS NOT NULL
            ),
            agg AS (
                SELECT
                    product_id,
                    customer_key,
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

    /** Helper: doanh thu hôm nay (status=1) */
    public function get_total_revenue_confirmed_today_from_details(
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        string $tz_offset_sql = 'INTERVAL 7 HOUR'
    ): float {
        $today = date('Y-m-d');
        return $this->get_total_revenue_had_status_in_range(
            $today,
            $today,
            1,
            $exclude_sources,
            false,
            $tz_offset_sql
        );
    }
}
