<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pancake_orders_model');
    }

    public function index()
    {
        // --- PHẦN 1: XỬ LÝ ĐẦU VÀO (INPUT) ---
        // Lấy khoảng ngày từ URL
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        // Đặt giá trị mặc định nếu không có ngày nào được chọn
        if (empty($start_date)) {
            $start_date = date('Y-m-01'); // Mặc định là ngày đầu tháng
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d'); // Mặc định là hôm nay
        }

        // --- PHẦN 2: LẤY VÀ TÍNH TOÁN DỮ LIỆU THEO KHOẢNG NGÀY ĐÃ CHỌN ---
        $count_confirmed_in_range = $this->pancake_orders_model->count_orders_confirmed_in_range($start_date, $end_date);
        $revenue_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_orders_confirmed_in_range($start_date, $end_date);
        $sales_volume_confirmed_in_range = $this->pancake_orders_model->get_sales_volume_of_orders_confirmed_in_range($start_date, $end_date);
        $product_quantity_confirmed_in_range = $this->pancake_orders_model->get_product_quantity_of_orders_confirmed_in_range($start_date, $end_date);
        $count_created_in_range = $this->pancake_orders_model->count_orders_created_in_range($start_date, $end_date);

        // Tính toán chỉ số phái sinh cho khoảng ngày
        $aov_in_range = ($count_confirmed_in_range > 0) ? ($revenue_confirmed_in_range / $count_confirmed_in_range) : 0;
        $avg_products_per_order_in_range = ($count_confirmed_in_range > 0) ? ($product_quantity_confirmed_in_range / $count_confirmed_in_range) : 0;
        $closing_rate_in_range = ($count_created_in_range > 0) ? (($count_confirmed_in_range / $count_created_in_range) * 100) : 0;

        // --- PHẦN 3: LẤY VÀ TÍNH TOÁN DỮ LIỆU CỐ ĐỊNH CỦA "HÔM NAY" ---
        $count_confirmed_today = $this->pancake_orders_model->count_orders_confirmed_today();
        $revenue_confirmed_today = $this->pancake_orders_model->get_revenue_of_orders_confirmed_today();
        // Để đếm tất cả đơn mới, không truyền tham số
        $count_created_today = $this->pancake_orders_model->count_orders_by_status_today();

        // Để đếm đơn hủy, truyền vào 'canceled'
        $count_canceled_today = $this->pancake_orders_model->count_orders_by_status_today('canceled');

        // Để đếm đơn xóa, truyền vào 'removed'
        $count_removed_today = $this->pancake_orders_model->count_orders_by_status_today('removed');

        $unique_customers_today = $this->pancake_orders_model->count_unique_customers_today();
        $get_product_quantity_confirmed_today = $this->pancake_orders_model->get_product_quantity_confirmed_today();
        $get_revenue_of_affiliate_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_affiliate_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_facebook_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_facebook_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_shopee_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_shopee_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_zalo_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_zalo_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_tiktok_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_woocommerce_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_woocommerce_orders_confirmed_in_range($start_date, $end_date);
        $get_revenue_of_others_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_others_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_ctv_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_ctv_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_facebook_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_facebook_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_shopee_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_shopee_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_zalo_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_zalo_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_tiktok_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_woocommerce_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_woocommerce_orders_confirmed_in_range($start_date, $end_date);
        $get_sale_of_others_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_others_orders_confirmed_in_range($start_date, $end_date);

        // Tính toán chỉ số phái sinh cho ngày hôm nay
        $closing_rate_today = ($count_created_today > 0) ? (($count_confirmed_today / $count_created_today) * 100) : 0;

        // --- PHẦN 4: TRUYỀN TẤT CẢ DỮ LIỆU SANG VIEW ---
        // Dữ liệu cho khoảng ngày đã chọn
        $data['count_confirmed'] = $count_confirmed_in_range;
        $data['revenue_confirmed'] = $revenue_confirmed_in_range;
        $data['sales_volume_confirmed'] = $sales_volume_confirmed_in_range;
        $data['product_quantity_confirmed'] = $product_quantity_confirmed_in_range;
        $data['aov_confirmed'] = $aov_in_range;
        $data['avg_products_per_order'] = $avg_products_per_order_in_range;
        $data['closing_rate'] = $closing_rate_in_range;

        // Dữ liệu cho ngày hôm nay
        $data['count_confirmed_today'] = $count_confirmed_today;
        $data['revenue_confirmed_today'] = $revenue_confirmed_today;
        $data['count_orders_new_today'] = $count_created_today;
        $data['count_orders_canceled_today'] = $count_canceled_today;
        $data['count_orders_removed_today'] = $count_removed_today;
        $data['count_unique_customers_today'] = $unique_customers_today;
        $data['closing_rate_today'] = $closing_rate_today;
        $data['get_product_quantity_confirmed_today'] = $get_product_quantity_confirmed_today;
        $data['get_revenue_of_affiliate_orders_confirmed_in_range'] = $get_revenue_of_affiliate_orders_confirmed_in_range;
        $data['get_revenue_of_facebook_orders_confirmed_in_range'] = $get_revenue_of_facebook_orders_confirmed_in_range;
        $data['get_revenue_of_shopee_orders_confirmed_in_range'] = $get_revenue_of_shopee_orders_confirmed_in_range;
        $data['get_revenue_of_zalo_orders_confirmed_in_range'] = $get_revenue_of_zalo_orders_confirmed_in_range;
        $data['get_revenue_of_tiktok_orders_confirmed_in_range'] = $get_revenue_of_tiktok_orders_confirmed_in_range;
        $data['get_revenue_of_woocommerce_orders_confirmed_in_range'] = $get_revenue_of_woocommerce_orders_confirmed_in_range;
        $data['get_revenue_of_others_orders_confirmed_in_range'] = $get_revenue_of_others_orders_confirmed_in_range;
        $data['get_sale_of_ctv_orders_confirmed_in_range'] = $get_sale_of_ctv_orders_confirmed_in_range;
        $data['get_sale_of_facebook_orders_confirmed_in_range'] = $get_sale_of_facebook_orders_confirmed_in_range;
        $data['get_sale_of_shopee_orders_confirmed_in_range'] = $get_sale_of_shopee_orders_confirmed_in_range;
        $data['get_sale_of_zalo_orders_confirmed_in_range'] = $get_sale_of_zalo_orders_confirmed_in_range;
        $data['get_sale_of_tiktok_orders_confirmed_in_range'] = $get_sale_of_tiktok_orders_confirmed_in_range;
        $data['get_sale_of_woocommerce_orders_confirmed_in_range'] = $get_sale_of_woocommerce_orders_confirmed_in_range;
        $data['get_sale_of_others_orders_confirmed_in_range'] = $get_sale_of_others_orders_confirmed_in_range;

        // Truyền các biến điều khiển
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['title'] = 'Dashboard Đơn Hàng Pancake';
        $this->load->view('pancake_sync/dashboard', $data);
    }
}
