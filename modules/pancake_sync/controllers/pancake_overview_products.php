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
        // ======= đọc params =======
        $date_from = $this->input->get('date_from') ?: date('Y-m-d');
        $date_to   = $this->input->get('date_to')   ?: date('Y-m-d');

        // status cần “đã từng có trong khoảng” (mặc định 1 = confirmed)
        $status = (int)($this->input->get('status') ?? 1);

        // loại trừ nguồn (CSV)
        $exclude_param   = $this->input->get('exclude_sources');
        $exclude_sources = $exclude_param
            ? array_values(array_filter(array_map('trim', explode(',', $exclude_param))))
            : ['Tiktok','Shopee','Affiliate'];

        // tính theo số lượng? (SUM(evenua*quantity))
        $use_quantity = (int)($this->input->get('use_quantity') ?? 0) === 1;

        // offset giờ (mặc định +7h nếu status_history là UTC)
        $tz_offset_hours = is_numeric($this->input->get('tz_offset_hours'))
            ? (int)$this->input->get('tz_offset_hours')
            : 7;

        // build INTERVAL literal cho SQL
        $tz_interval_sql = 'INTERVAL ' . (int)$tz_offset_hours . ' HOUR';

        // ======= gọi model (chỉ total revenue, theo yêu cầu) =======
        $total_revenue = $this->overview->get_total_revenue_had_status_in_range(
            $date_from,
            $date_to,
            $status,
            $exclude_sources,
            $use_quantity,
            $tz_interval_sql
        );

        // Nếu bạn CHƯA triển khai các hàm breakdown/combos… thì để null/array rỗng
        $data = [
            'title'               => 'Tổng quan sản phẩm',
            'date_from'           => $date_from,
            'date_to'             => $date_to,
            'status'              => $status,
            'exclude_sources'     => $exclude_sources,
            'use_quantity'        => $use_quantity,
            'tz_offset_hours'     => $tz_offset_hours,
            'total_revenue'       => $total_revenue,

            // Các phần dưới để trống nếu chưa dùng:
            'products_metrics'    => [],
            'combos_revenue'      => [],
            'aov_by_combo'        => [],
            'repurchase_by_combo' => [],
            'top_combo'           => null,
        ];

        $this->load->view('pancake_sync/dashboard_product_pancake', $data);
    }
}
