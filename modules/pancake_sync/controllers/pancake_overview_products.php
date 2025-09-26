<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pancake_overview_products_model');
    }

    public function index()
    {
        // ngày lọc
        $date_from = $this->input->get('date_from') ?: date('Y-m-01');
        $date_to   = $this->input->get('date_to')   ?: date('Y-m-d');

        // nguồn loại trừ (GET ?exclude_sources=Tiktok,Shopee,Affiliate) – mặc định 3 nguồn
        $exclude_param = $this->input->get('exclude_sources');
        $exclude_sources = $exclude_param
            ? array_values(array_filter(array_map('trim', explode(',', $exclude_param))))
            : ['Tiktok','Shopee','Affiliate'];

        // dữ liệu
        $total_revenue    = $this->pancake_overview_products_model->get_total_revenue_excluding_sources($date_from, $date_to, $exclude_sources);
        $products_metrics = $this->pancake_overview_products_model->get_products_breakdown_excluding_sources($date_from, $date_to, $exclude_sources);
        $combos_revenue   = $this->pancake_overview_products_model->get_combos_revenue_excluding_sources($date_from, $date_to, $exclude_sources);
        $aov_by_combo     = $this->pancake_overview_products_model->get_aov_by_combo_excluding_sources($date_from, $date_to, $exclude_sources);
        $repurchase_by_combo = $this->pancake_overview_products_model->get_repurchase_rate_by_combo_excluding_sources($date_from, $date_to, $exclude_sources);
        $top_combo        = $this->pancake_overview_products_model->get_top_combo_excluding_sources($date_from, $date_to, $exclude_sources);

        $data = [
            'title'             => 'Tổng quan sản phẩm',
            'date_from'         => $date_from,
            'date_to'           => $date_to,
            'exclude_sources'   => $exclude_sources,
            'total_revenue'     => $total_revenue,
            'products_metrics'  => $products_metrics,
            'combos_revenue'    => $combos_revenue,
            'aov_by_combo'      => $aov_by_combo,
            'repurchase_by_combo' => $repurchase_by_combo,
            'top_combo'         => $top_combo,
        ];

        $this->load->view('pancake_sync/dashboard_product_pancake', $data);
    }
}
