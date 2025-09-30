<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pancake_overview_products_model', 'overview');
    }

    public function index()
    {
        // Mặc định hôm nay
        $date_from = $this->input->get('date_from') ?: date('Y-m-d');
        $date_to   = $this->input->get('date_to')   ?: date('Y-m-d');

        // Tổng doanh thu (Σ items − order.total_discount) cho các đơn confirmed (status=1)
        $total_revenue = $this->overview->get_total_revenue_had_status_in_range(
            $date_from,
            $date_to,
            1,                                   // status=1 (confirmed)
            ['Tiktok', 'Shopee', 'Affiliate'],   // loại trừ nguồn
            false,
            'INTERVAL 7 HOUR'
        );

        // Doanh thu theo sản phẩm lẻ (is_combo=0)
        $products_metrics = $this->overview->get_product_revenue_breakdown(
            $date_from,
            $date_to,
            1,
            ['Tiktok', 'Shopee', 'Affiliate'],
            true,                // exclude_gifts
            'INTERVAL 7 HOUR',
            100,
            0
        );

        /* ================== NEW: Tính "Số lượng" = số ĐƠN DISTINCT cho từng sản phẩm ================== */
        // Ưu tiên dùng hàm model nếu có; fallback tự đếm từ dữ liệu đã có.
        $orders_by_product = [];

        if (method_exists($this->overview, 'get_product_order_counts_distinct')) {
            // Lấy số đơn distinct theo từng sản phẩm non-combo trong khoảng ngày & điều kiện lọc
            $rows_cnt = $this->overview->get_product_order_counts_distinct(
                $date_from,
                $date_to,
                1,                                   // status=1
                ['Tiktok', 'Shopee', 'Affiliate'],   // loại trừ nguồn
                true,                                // exclude_gifts
                'INTERVAL 7 HOUR'
            );
            foreach ($rows_cnt as $r) {
                $pid = $r['product_id'] ?? null;
                $key = $pid !== null ? 'ID:' . (string)$pid : 'NAME:' . (string)($r['product_name'] ?? '');
                if ($key !== 'NAME:') {
                    $orders_by_product[$key] = (int)($r['orders'] ?? 0); // COUNT(DISTINCT order_id)
                }
            }
        } else {
            // Fallback: nếu $products_metrics có order_ids (mảng/CSV) thì tự đếm unique;
            // hoặc nếu đã có 'orders' thì dùng luôn.
            foreach ($products_metrics as $p) {
                $pid = $p['product_id'] ?? null;
                $key = $pid !== null ? 'ID:' . (string)$pid : 'NAME:' . (string)($p['product_name'] ?? '');
                if ($key === 'NAME:') continue;

                if (!empty($p['order_ids'])) {
                    $ids  = is_array($p['order_ids']) ? $p['order_ids'] : explode(',', (string)$p['order_ids']);
                    $uniq = [];
                    foreach ($ids as $id) {
                        $id = trim((string)$id);
                        if ($id !== '') $uniq[$id] = true;
                    }
                    $orders_by_product[$key] = count($uniq);
                } elseif (isset($p['orders']) && is_numeric($p['orders'])) {
                    $orders_by_product[$key] = (int)$p['orders'];
                }
            }
        }

        // Merge số đơn vào $products_metrics (để view hiển thị cột "Số lượng")
        if (!empty($products_metrics)) {
            foreach ($products_metrics as &$p) {
                $pid = $p['product_id'] ?? null;
                $key = $pid !== null ? 'ID:' . (string)$pid : 'NAME:' . (string)($p['product_name'] ?? '');
                $p['orders'] = $orders_by_product[$key] ?? (int)($p['orders'] ?? 0); // mặc định 0 nếu không có
            }
            unset($p);
        }
        /* ================== /NEW ================== */

        // Doanh thu theo combo (is_combo=1) + (orders, aov) đã tính ở model
        $combos_revenue = $this->overview->get_combo_revenue_breakdown(
            $date_from,
            $date_to,
            1,
            ['Tiktok', 'Shopee', 'Affiliate'],
            true,
            'INTERVAL 7 HOUR',
            100,
            0
        );

        // Sắp xếp combos_revenue theo revenue desc (phòng model chưa sort)
        if (!empty($combos_revenue)) {
            usort($combos_revenue, fn($a, $b) => (float)($b['revenue'] ?? 0) <=> (float)($a['revenue'] ?? 0));
        }

        // Xây AOV & Orders theo combo để dùng nhanh cho view
        $aov_by_combo    = [];
        $orders_by_combo = [];
        if (!empty($combos_revenue)) {
            foreach ($combos_revenue as $row) {
                $pid = $row['product_id'] ?? null;
                // Ưu tiên dùng product_id; nếu null, fallback product_name
                $key = $pid !== null ? (string)$pid : (string)($row['product_name'] ?? '');
                if ($key === '') continue;

                $revenue = (float)($row['revenue'] ?? 0);
                $orders  = isset($row['orders']) ? (int)$row['orders'] : 0;
                $aov     = isset($row['aov']) ? (float)$row['aov'] : ($orders > 0 ? $revenue / $orders : 0.0);

                $aov_by_combo[$key]    = $aov;
                $orders_by_combo[$key] = $orders;
            }
        }

        // Top combo có thêm contribution %
        $top_combo = null;
        if (!empty($combos_revenue)) {
            $top_combo = $combos_revenue[0];

            $top_revenue = (float)($top_combo['revenue'] ?? 0);
            $top_orders  = isset($top_combo['orders']) ? (int)$top_combo['orders'] : 0;
            $top_aov     = isset($top_combo['aov']) ? (float)$top_combo['aov'] : ($top_orders > 0 ? $top_revenue / $top_orders : 0.0);

            $top_combo['orders']           = $top_orders;
            $top_combo['aov']              = $top_aov;
            $top_combo['contribution_pct'] = ($total_revenue > 0)
                ? round($top_revenue * 100 / $total_revenue, 2)
                : null;
        }

        // ====== TÍCH HỢP: Repeat-rate theo combo ======
        $repurchase_by_combo = [];
        $combo_repeat_overall = [
            'unique_customer_combo_pairs' => 0,
            'repeat_customer_combo_pairs' => 0,
            'repeat_rate'                 => 0.0,
        ];

        // 1) Lấy breakdown theo combo
        $repeat_rows = [];
        if (method_exists($this->overview, 'get_combo_repeat_rate_breakdown')) {
            $repeat_rows = $this->overview->get_combo_repeat_rate_breakdown(
                $date_from,
                $date_to,
                1,
                ['Tiktok', 'Shopee', 'Affiliate'],
                true,
                'INTERVAL 7 HOUR',
                2,      // >=2 đơn/khách/combo trong khoảng
                10000,  // đủ lớn để lấy hết
                0
            );
        }

        // 2) Map repeat theo key (product_id ưu tiên, fallback product_name)
        $repeat_map = [];
        foreach ($repeat_rows as $rr) {
            $pid = $rr['product_id'] ?? null;
            $key = $pid !== null ? 'ID:' . (string)$pid : 'NAME:' . (string)($rr['product_name'] ?? '');
            $repeat_map[$key] = [
                'unique_buyers' => (int)($rr['unique_buyers'] ?? 0),
                'repeat_buyers' => (int)($rr['repeat_buyers'] ?? 0),
                'repeat_rate'   => isset($rr['repeat_rate']) ? (float)$rr['repeat_rate'] : null, // 0..1
            ];
        }

        // 3) Dựng đúng cấu trúc cho VIEW (image_url, combo_code, combo_name, repurchase_rate %)
        $repurchase_by_combo = [];
        $default_img = 'https://mochavietnam.com.vn/thumbs/600x600x2/upload/photo/tai-xuong-3278.png';

        foreach ($combos_revenue as $row) {
            $pid = $row['product_id'] ?? null;
            $key = $pid !== null ? 'ID:' . (string)$pid : 'NAME:' . (string)($row['product_name'] ?? '');
            $rep = $repeat_map[$key] ?? null;

            // (tuỳ chọn) bỏ các combo chưa có khách mua trong khoảng
            if ($rep && $rep['unique_buyers'] <= 0) {
                continue;
            }

            $rate_percent = null;
            if ($rep && $rep['repeat_rate'] !== null) {
                $rate_percent = $rep['repeat_rate'] * 100.0; // đổi sang %
            }

            $repurchase_by_combo[] = [
                'image_url'       => !empty($row['image_url']) ? $row['image_url'] : $default_img,
                'combo_code'      => $pid !== null ? (string)$pid : '-',          // mã combo
                'combo_name'      => $row['product_name'] ?? '-',                 // tên combo
                'repurchase_rate' => $rate_percent,                               // % để view format
            ];
        }

        // 4) (tuỳ chọn) sắp xếp theo tỷ lệ mua lại giảm dần
        usort($repurchase_by_combo, function ($a, $b) {
            $ra = $a['repurchase_rate'] ?? -1;
            $rb = $b['repurchase_rate'] ?? -1;
            return $rb <=> $ra;
        });

        // 5) Tổng thể
        if (method_exists($this->overview, 'get_combo_repeat_rate_overall')) {
            $combo_repeat_overall = $this->overview->get_combo_repeat_rate_overall(
                $date_from,
                $date_to,
                1,
                ['Tiktok', 'Shopee', 'Affiliate'],
                true,
                'INTERVAL 7 HOUR',
                2
            );
        }

        $data = compact(
            'date_from',
            'date_to',
            'total_revenue',
            'products_metrics',
            'combos_revenue',
            'aov_by_combo',
            'repurchase_by_combo',
            'top_combo',
            'orders_by_combo',
            'combo_repeat_overall'
        );

        $this->load->view('pancake_sync/dashboard_product_pancake', $data);
    }
}
