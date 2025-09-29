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
            1,                                 // status=1 (confirmed)
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
            true,              // exclude_gifts
            'INTERVAL 7 HOUR',
            100,
            0,
            false              // is_combo = false => sản phẩm lẻ
        );

        // Doanh thu theo combo (is_combo=1) + đã bao gồm orders & aov (từ model)
        // Nếu model của bạn KHÔNG nhận tham số cuối (is_combo), thì bỏ đối số cuối đi.
        $combos_revenue = $this->overview->get_combo_revenue_breakdown(
            $date_from,
            $date_to,
            1,
            ['Tiktok', 'Shopee', 'Affiliate'],
            true,
            'INTERVAL 7 HOUR',
            100,
            0,
            true               // <-- nếu model bạn chưa hỗ trợ tham số này, hãy xóa đối số này.
        );

        // Xây AOV theo combo: key = product_id (hoặc tên) -> aov
        $aov_by_combo = [];
        $orders_by_combo = [];
        if (!empty($combos_revenue)) {
            foreach ($combos_revenue as $row) {
                $pid = $row['product_id'] ?? null;
                // Ưu tiên dùng product_id; nếu null, fallback theo product_name
                $key = $pid !== null ? (string)$pid : (string)($row['product_name'] ?? '');
                if ($key === '') continue;

                // model đã trả 'aov' và 'orders'; nếu chưa, tự tính dự phòng
                $revenue = (float)($row['revenue'] ?? 0);
                $orders  = isset($row['orders']) ? (int)$row['orders'] : 0;
                $aov     = isset($row['aov']) ? (float)$row['aov'] : ($orders > 0 ? $revenue / $orders : 0.0);

                $aov_by_combo[$key]    = $aov;
                $orders_by_combo[$key] = $orders;
            }

            // Sắp xếp combos_revenue theo revenue desc (phòng model chưa sort)
            usort($combos_revenue, fn($a, $b) => (float)($b['revenue'] ?? 0) <=> (float)($a['revenue'] ?? 0));
        }

        // Top combo (giàu thông tin cho view)
        $top_combo = null;
        if (!empty($combos_revenue)) {
            $top_combo = $combos_revenue[0];

            // Bổ sung chỉ số đóng góp & đảm bảo có orders/aov
            $top_revenue = (float)($top_combo['revenue'] ?? 0);
            $top_orders  = isset($top_combo['orders']) ? (int)$top_combo['orders'] : 0;
            $top_aov     = isset($top_combo['aov']) ? (float)$top_combo['aov'] : ($top_orders > 0 ? $top_revenue / $top_orders : 0.0);

            $top_combo['orders']           = $top_orders;
            $top_combo['aov']              = $top_aov;
            $top_combo['contribution_pct'] = ($total_revenue > 0)
                ? round($top_revenue * 100 / $total_revenue, 2)
                : null;
        }

        // (Tuỳ chọn) repurchase_by_combo để sau này gắn: hiện để trống
        $repurchase_by_combo = [];

        $data = compact(
            'date_from',
            'date_to',
            'total_revenue',
            'products_metrics',
            'combos_revenue',
            'aov_by_combo',
            'repurchase_by_combo',
            'top_combo',
            'orders_by_combo'
        );

        $this->load->view('pancake_sync/dashboard_product_pancake', $data);
    }
}
