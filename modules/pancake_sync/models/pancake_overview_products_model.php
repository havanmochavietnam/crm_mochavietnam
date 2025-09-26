<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products_model extends App_Model
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

    /* ============ tiny file cache ============ */
    private function _cache_path($key) { return $this->cache_dir . md5($key) . '.cache'; }

    private function _cache_get($key, $ttl)
    {
        $f = $this->_cache_path($key);
        if (!is_file($f)) return false;
        if (filemtime($f) + $ttl < time()) { @unlink($f); return false; }
        $raw = @file_get_contents($f);
        if ($raw === false) return false;
        $data = @unserialize($raw);
        return $data !== false ? $data : false;
    }

    private function _cache_set($key, $val, $ttl)
    {
        $p = $this->_cache_path($key);
        @file_put_contents($p, serialize($val), LOCK_EX);
        @touch($p, time());
        return true;
    }

    /* ============ helpers ============ */
    private function _placeholders($n) { return implode(',', array_fill(0, max(1, (int)$n), '?')); }

    private function _customer_uid_expr()
    {
        // alias bảng là po trong mọi CTE
        return "COALESCE(
            JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.id')),
            NULLIF(REGEXP_REPLACE(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.shipping_address.phone_number')), '[^0-9]', ''), ''),
            NULLIF(REGEXP_REPLACE(JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.bill_phone_number')), '[^0-9]', ''), ''),
            JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.customer.referral_code')),
            CAST(po.id AS CHAR)
        )";
    }

    private function _combo_field_paths()
    {
        // Dựa theo payload thật: combo là item composite, thông tin nằm trong variation_info
        return [
            'code' => [
                "$.variation_info.product_display_id",
                "$.variation_info.display_id",
                "$.product_display_id",
                "$.display_id",
                // dự phòng cũ
                "$.combo.code", "$.combo_code", "$.product.combo.code", "$.bundle.code", "$.comboCode",
            ],
            'name' => [
                "$.variation_info.name",
                "$.name",
                "$.combo.name", "$.combo_name", "$.product.combo.name", "$.bundle.name", "$.comboName",
            ],
            'image' => [
                "$.variation_info.images[0]",
                "$.image_url", "$.product.image_url", "$.product.image",
                "$.combo.image_url", "$.combo.image",
            ],
        ];
    }

    private function _coalesce_json_unquote($baseAlias, $paths)
    {
        $paths = array_values($paths);
        $expr = "JSON_UNQUOTE(JSON_EXTRACT({$baseAlias}, '{$paths[0]}'))";
        for ($k = 1; $k < count($paths); $k++) {
            $expr = "IFNULL({$expr}, JSON_UNQUOTE(JSON_EXTRACT({$baseAlias}, '{$paths[$k]}')))";
        }
        return $expr;
    }

    /* ================== TOTAL REVENUE (exclude sources) ================== */
    // order_revenue = total_price - (order_discount + sum(item.total_discount))
    public function get_total_revenue_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        $cache_key = 'prod_total_rev_ex_src_' . $start_date . '_' . $end_date . '_' . md5(json_encode($exclude_sources));
        if (($cached = $this->_cache_get($cache_key, 60)) !== false) return (float)$cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter((array)$exclude_sources, fn($x)=>$x!==null && $x!==''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->_placeholders(count($exclude_sources));

        $sql = "
        SELECT SUM(ord.order_revenue) AS total_revenue
        FROM (
            SELECT
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                (
                  SELECT MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR))
                  FROM JSON_TABLE(
                    po.data, '$.status_history[*]'
                    COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
                  ) h
                  WHERE h.status = 1
                ) AS confirmed_at,
                (
                    CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) AS DECIMAL(18,2))
                    - (
                        CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) AS DECIMAL(18,2))
                        + (SELECT IFNULL(SUM(CAST(JSON_EXTRACT(i2.value, '$.total_discount') AS DECIMAL(18,2))), 0)
                           FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i2 )
                      )
                ) AS order_revenue
            FROM {$table} po
        ) ord
        WHERE ord.confirmed_at BETWEEN ? AND ?
          AND (ord.status_name NOT IN ('canceled','returned','returning') OR ord.status_name IS NULL)
          AND (ord.channel NOT IN ({$ph}) OR ord.channel IS NULL)
        ";
        $bind = array_merge([$startDT, $endDT], $exclude_sources);

        $row = $this->db->query($sql, $bind)->row_array();
        $total = (float)($row['total_revenue'] ?? 0);
        $this->_cache_set($cache_key, $total, 60);
        return $total;
    }

    /* ================== PRODUCTS BREAKDOWN ================== */
    public function get_products_breakdown_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter((array)$exclude_sources, fn($x)=>$x!==null && $x!==''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->_placeholders(count($exclude_sources));

        $sql = "
        WITH
        confirmed AS (
            SELECT po.id AS order_id, MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR)) AS confirmed_at
            FROM {$table} po
            JOIN JSON_TABLE(
                po.data, '$.status_history[*]'
                COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
            ) h ON 1=1
            WHERE h.status = 1
            GROUP BY po.id
        ),
        ord AS (
            SELECT
                po.id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0)   AS DECIMAL(18,2)) AS order_discount
            FROM {$table} po
        ),
        items AS (
            SELECT
                po.id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(i.value, '$.product.code')) AS product_code,
                JSON_UNQUOTE(JSON_EXTRACT(i.value, '$.product.name')) AS product_name,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) AS unit_price,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2)) AS quantity,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'),0) AS DECIMAL(18,2)) AS item_discount,
                (
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) *
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2))
                  - CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'), 0) AS DECIMAL(18,2))
                ) AS item_net
            FROM {$table} po
            JOIN JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
        ),
        sums AS ( SELECT order_id, SUM(item_net) AS sum_item_net FROM items GROUP BY order_id )
        SELECT
            it.product_code, it.product_name,
            SUM(it.quantity) AS quantity,
            SUM( it.item_net - (ord.order_discount * (CASE WHEN sums.sum_item_net>0 THEN it.item_net/sums.sum_item_net ELSE 0 END)) ) AS revenue
        FROM items it
        JOIN ord         ON ord.order_id = it.order_id
        JOIN sums        ON sums.order_id = it.order_id
        JOIN confirmed c ON c.order_id    = it.order_id
        WHERE c.confirmed_at BETWEEN ? AND ?
          AND (ord.status_name NOT IN ('canceled','returned','returning') OR ord.status_name IS NULL)
          AND (ord.channel NOT IN ({$ph}) OR ord.channel IS NULL)
        GROUP BY it.product_code, it.product_name
        ORDER BY revenue DESC
        ";
        $bind  = array_merge([$startDT, $endDT], $exclude_sources);
        $rows  = $this->db->query($sql, $bind)->result_array() ?: [];

        return array_map(function($r){
            return [
                'product_code' => $r['product_code'] ?? '',
                'product_name' => $r['product_name'] ?? '',
                'quantity'     => (int)($r['quantity'] ?? 0),
                'revenue'      => (float)($r['revenue'] ?? 0),
                'pct'          => null,
                'image_url'    => null,
            ];
        }, $rows);
    }

    /* ================== COMBO HELPERS ================== */
    private function _combo_exprs()
    {
        $paths = $this->_combo_field_paths();
        return [
            'code' => $this->_coalesce_json_unquote('i.value', $paths['code']),
            'name' => $this->_coalesce_json_unquote('i.value', $paths['name']),
            'img'  => $this->_coalesce_json_unquote('i.value', $paths['image']),
        ];
    }

    /* ================== COMBO REVENUE ================== */
    public function get_combos_revenue_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        $cache_key = 'combo_rev_' . $start_date . '_' . $end_date . '_' . md5(json_encode($exclude_sources));
        if (($cached = $this->_cache_get($cache_key, 60)) !== false) return $cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter((array)$exclude_sources, fn($x)=>$x!==null && $x!==''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->_placeholders(count($exclude_sources));

        $e = $this->_combo_exprs();

        $sql = "
        WITH
        confirmed AS (
            SELECT po.id AS order_id, MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR)) AS confirmed_at
            FROM {$table} po
            JOIN JSON_TABLE(
                po.data, '$.status_history[*]'
                COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
            ) h ON 1=1
            WHERE h.status = 1
            GROUP BY po.id
        ),
        ord AS (
            SELECT
                po.id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0)   AS DECIMAL(18,2)) AS order_discount
            FROM {$table} po
        ),
        items AS (
            SELECT
                po.id AS order_id,
                {$e['code']} AS combo_code,
                {$e['name']} AS combo_name,
                {$e['img']}  AS image_url,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) AS unit_price,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2)) AS quantity,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'),0) AS DECIMAL(18,2)) AS item_discount,
                (
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) *
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2))
                  - CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'), 0) AS DECIMAL(18,2))
                ) AS item_net
            FROM {$table} po
            JOIN JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
        ),
        sums AS ( SELECT order_id, SUM(item_net) AS sum_item_net FROM items GROUP BY order_id ),
        rc AS (
            SELECT
                it.order_id, it.combo_code, it.combo_name, it.image_url,
                ( it.item_net - (ord.order_discount * (CASE WHEN sums.sum_item_net>0 THEN it.item_net/sums.sum_item_net ELSE 0 END)) ) AS item_revenue
            FROM items it
            JOIN ord  ON ord.order_id  = it.order_id
            JOIN sums ON sums.order_id = it.order_id
            WHERE it.combo_code IS NOT NULL AND it.combo_code <> ''
        )
        SELECT
            rc.combo_code, rc.combo_name,
            MIN(rc.image_url) AS image_url,
            SUM(rc.item_revenue) AS revenue
        FROM rc
        JOIN ord         ON ord.order_id = rc.order_id
        JOIN confirmed c ON c.order_id   = rc.order_id
        WHERE c.confirmed_at BETWEEN ? AND ?
          AND (ord.status_name NOT IN ('canceled','returned','returning') OR ord.status_name IS NULL)
          AND (ord.channel NOT IN ({$ph}) OR ord.channel IS NULL)
        GROUP BY rc.combo_code, rc.combo_name
        ORDER BY revenue DESC
        ";

        $bind = array_merge([$startDT, $endDT], $exclude_sources);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        $out = array_map(function ($r) {
            return [
                'combo_code' => $r['combo_code'] ?? '',
                'combo_name' => $r['combo_name'] ?? '',
                'image_url'  => $r['image_url'] ?? null,
                'revenue'    => (float)($r['revenue'] ?? 0),
            ];
        }, $rows);

        $this->_cache_set($cache_key, $out, 60);
        return $out;
    }

    /* ================== COMBO AOV ================== */
    public function get_aov_by_combo_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        $cache_key = 'combo_aov_' . $start_date . '_' . $end_date . '_' . md5(json_encode($exclude_sources));
        if (($cached = $this->_cache_get($cache_key, 60)) !== false) return $cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter((array)$exclude_sources, fn($x)=>$x!==null && $x!==''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->_placeholders(count($exclude_sources));

        $e = $this->_combo_exprs();

        $sql = "
        WITH
        confirmed AS (
            SELECT po.id AS order_id, MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR)) AS confirmed_at
            FROM {$table} po
            JOIN JSON_TABLE(
                po.data, '$.status_history[*]'
                COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
            ) h ON 1=1
            WHERE h.status = 1
            GROUP BY po.id
        ),
        ord AS (
            SELECT
                po.id AS order_id,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name,
                CAST(IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0)   AS DECIMAL(18,2)) AS order_discount
            FROM {$table} po
        ),
        items AS (
            SELECT
                po.id AS order_id,
                {$e['code']} AS combo_code,
                {$e['name']} AS combo_name,
                {$e['img']}  AS image_url,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) AS unit_price,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2)) AS quantity,
                CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'),0) AS DECIMAL(18,2)) AS item_discount,
                (
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.price'),    0) AS DECIMAL(18,2)) *
                  CAST(IFNULL(JSON_EXTRACT(i.value, '$.quantity'), 0) AS DECIMAL(18,2))
                  - CAST(IFNULL(JSON_EXTRACT(i.value, '$.total_discount'), 0) AS DECIMAL(18,2))
                ) AS item_net
            FROM {$table} po
            JOIN JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
        ),
        sums AS ( SELECT order_id, SUM(item_net) AS sum_item_net FROM items GROUP BY order_id ),
        rc AS (
            SELECT
                it.order_id, it.combo_code, it.combo_name, it.image_url,
                ( it.item_net - (ord.order_discount * (CASE WHEN sums.sum_item_net>0 THEN it.item_net/sums.sum_item_net ELSE 0 END)) ) AS item_revenue
            FROM items it
            JOIN ord  ON ord.order_id  = it.order_id
            JOIN sums ON sums.order_id = it.order_id
            WHERE it.combo_code IS NOT NULL AND it.combo_code <> ''
        ),
        per_order_combo AS (
            SELECT
                rc.order_id, rc.combo_code, rc.combo_name,
                MIN(rc.image_url) AS image_url,
                SUM(rc.item_revenue) AS revenue_per_order
            FROM rc
            GROUP BY rc.order_id, rc.combo_code, rc.combo_name
        )
        SELECT
            poc.combo_code, poc.combo_name,
            MIN(poc.image_url) AS image_url,
            (SUM(poc.revenue_per_order) / NULLIF(COUNT(DISTINCT poc.order_id), 0)) AS aov
        FROM per_order_combo poc
        JOIN ord         ON ord.order_id = poc.order_id
        JOIN confirmed c ON c.order_id   = poc.order_id
        WHERE c.confirmed_at BETWEEN ? AND ?
          AND (ord.status_name NOT IN ('canceled','returned','returning') OR ord.status_name IS NULL)
          AND (ord.channel NOT IN ({$ph}) OR ord.channel IS NULL)
        GROUP BY poc.combo_code, poc.combo_name
        ORDER BY aov DESC
        ";
        $bind = array_merge([$startDT, $endDT], $exclude_sources);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        $out = array_map(function($r){
            return [
                'combo_code' => $r['combo_code'] ?? '',
                'combo_name' => $r['combo_name'] ?? '',
                'image_url'  => $r['image_url'] ?? null,
                'aov'        => (float)($r['aov'] ?? 0),
            ];
        }, $rows);

        $this->_cache_set($cache_key, $out, 60);
        return $out;
    }

    /* ================== COMBO REPURCHASE RATE ================== */
    public function get_repurchase_rate_by_combo_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        $cache_key = 'combo_repurchase_' . $start_date . '_' . $end_date . '_' . md5(json_encode($exclude_sources));
        if (($cached = $this->_cache_get($cache_key, 60)) !== false) return $cached;

        $table   = db_prefix() . 'pancake_orders';
        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter((array)$exclude_sources, fn($x)=>$x!==null && $x!==''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->_placeholders(count($exclude_sources));

        $e = $this->_combo_exprs();
        $customer_uid = $this->_customer_uid_expr();

        $sql = "
        WITH
        confirmed AS (
            SELECT po.id AS order_id, MAX(DATE_ADD(h.updated_at, INTERVAL 7 HOUR)) AS confirmed_at
            FROM {$table} po
            JOIN JSON_TABLE(
                po.data, '$.status_history[*]'
                COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')
            ) h ON 1=1
            WHERE h.status = 1
            GROUP BY po.id
        ),
        ord AS (
            SELECT
                po.id AS order_id,
                {$customer_uid} AS customer_uid,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) AS channel,
                JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name'))        AS status_name
            FROM {$table} po
        ),
        items AS (
            SELECT
                po.id AS order_id,
                {$e['code']} AS combo_code,
                {$e['name']} AS combo_name,
                {$e['img']}  AS image_url
            FROM {$table} po
            JOIN JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) i
        ),
        cust_combo AS (
            SELECT
                o.customer_uid,
                it.combo_code,
                it.combo_name,
                MIN(it.image_url) AS image_url,
                COUNT(DISTINCT o.order_id) AS order_cnt
            FROM ord o
            JOIN items it     ON it.order_id = o.order_id
            JOIN confirmed c  ON c.order_id  = o.order_id
            WHERE
                it.combo_code IS NOT NULL AND it.combo_code <> ''
                AND c.confirmed_at BETWEEN ? AND ?
                AND (o.status_name NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
                AND (o.channel NOT IN ({$ph}) OR o.channel IS NULL)
            GROUP BY o.customer_uid, it.combo_code, it.combo_name
        )
        SELECT
            combo_code, combo_name,
            MIN(image_url) AS image_url,
            COUNT(*) AS customers,
            SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) AS repurchase_customers,
            (CASE WHEN COUNT(*)>0 THEN 100 * SUM(CASE WHEN order_cnt >= 2 THEN 1 ELSE 0 END) / COUNT(*) ELSE 0 END) AS repurchase_rate
        FROM cust_combo
        GROUP BY combo_code, combo_name
        ORDER BY repurchase_rate DESC
        ";
        $bind = array_merge([$startDT, $endDT], $exclude_sources);
        $rows = $this->db->query($sql, $bind)->result_array() ?: [];

        $out = array_map(function($r){
            return [
                'combo_code'      => $r['combo_code'] ?? '',
                'combo_name'      => $r['combo_name'] ?? '',
                'image_url'       => $r['image_url'] ?? null,
                'repurchase_rate' => isset($r['repurchase_rate']) ? (float)$r['repurchase_rate'] : null,
                'customers'       => (int)($r['customers'] ?? 0),
                'repurchase_customers' => (int)($r['repurchase_customers'] ?? 0),
            ];
        }, $rows);

        $this->_cache_set($cache_key, $out, 60);
        return $out;
    }

    /* ================== TOP COMBO (name, image, revenue, contribution %) ================== */
    public function get_top_combo_excluding_sources($start_date, $end_date, $exclude_sources = ['Tiktok','Shopee','Affiliate'])
    {
        // tái dụng list combo revenue
        $list = $this->get_combos_revenue_excluding_sources($start_date, $end_date, $exclude_sources);
        if (empty($list)) return null;

        // tổng doanh thu (cùng bộ lọc) để tính % đóng góp
        $total = array_reduce($list, function($c,$r){ return $c + (float)$r['revenue']; }, 0.0);

        $top = $list[0];
        $pct = $total > 0 ? (100.0 * ((float)$top['revenue']) / $total) : null;

        return [
            'code'              => $top['combo_code'],
            'name'              => $top['combo_name'],
            'image_url'         => $top['image_url'],
            'revenue'           => (float)$top['revenue'],
            'contribution_pct'  => $pct,
        ];
    }
}
