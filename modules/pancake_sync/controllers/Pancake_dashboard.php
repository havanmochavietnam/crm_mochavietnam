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
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        if (empty($start_date)) {
            $start_date = date('Y-m-01'); // Mặc định là ngày đầu tháng
        }
        if (empty($end_date)) {
            $end_date = date('Y-m-d'); // Mặc định là hôm nay
        }

        // Đơn chốt hôm nay
        $count_confirmed_today = $this->pancake_orders_model->count_orders_confirmed_today();

        //Doanh thu hôm nay
        $revenue_confirmed_today = $this->pancake_orders_model->get_revenue_of_orders_confirmed_today();

        //Đơn mới hôm nay
        $count_created_today = $this->pancake_orders_model->count_orders_by_status_today();
        
        //Số đơn hủy hôm nay
        $count_canceled_today = $this->pancake_orders_model->count_orders_by_status_today('canceled');
        
        //Số đơn xóa hôm nay
        $count_removed_today = $this->pancake_orders_model->count_orders_by_status_today('removed');

        //Khách hàng hôm nay
        $unique_customers_today = $this->pancake_orders_model->count_unique_customers_today();

        //Số sản phẩm chốt hôm nay
        $get_product_quantity_confirmed_today = $this->pancake_orders_model->get_product_quantity_confirmed_today();

        //Đơn chốt theo mốc thời gian
        $count_confirmed_in_range = $this->pancake_orders_model->count_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số theo mốc thời gian
        $revenue_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_orders_confirmed_in_range($start_date, $end_date);

        //Doanh thu theo mốc thời gian
        $sales_volume_confirmed_in_range = $this->pancake_orders_model->get_sales_volume_of_orders_confirmed_in_range($start_date, $end_date);

        //Số sản phẩm chốt theo thơi gian
        $product_quantity_confirmed_in_range = $this->pancake_orders_model->get_product_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // Số đơn tạo mới theo thời gian
        $count_created_in_range = $this->pancake_orders_model->count_orders_created_in_range($start_date, $end_date);

        // GTTB
        $aov_in_range = ($count_confirmed_in_range > 0) ? ($revenue_confirmed_in_range / $count_confirmed_in_range) : 0;

        // SL SPTB
        $avg_products_per_order_in_range = ($count_confirmed_in_range > 0) ? ($product_quantity_confirmed_in_range / $count_confirmed_in_range) : 0;

        // Tỷ lệ chốt
        $closing_rate_in_range = ($count_created_in_range > 0) ? (($count_confirmed_in_range / $count_created_in_range) * 100) : 0;

        // Tính toán chỉ số phái sinh cho ngày hôm nay
        $closing_rate_today = ($count_created_today > 0) ? (($count_confirmed_today / $count_created_today) * 100) : 0;

        // Doanh thu Affiliate theo thời gian
        $get_revenue_of_affiliate_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_affiliate_orders_confirmed_in_range($start_date, $end_date);

        // Doanh thu Facebook theo thời gian
        $get_revenue_of_facebook_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_facebook_orders_confirmed_in_range($start_date, $end_date);

        // Doanh thu Shopee theo thời gian
        $get_revenue_of_shopee_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_shopee_orders_confirmed_in_range($start_date, $end_date);
        
        // Doanh thu Zalo theo thời gian
        $get_revenue_of_zalo_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_zalo_orders_confirmed_in_range($start_date, $end_date);
        
        // Doanh thu Tiktok theo thời gian
        $get_revenue_of_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_tiktok_orders_confirmed_in_range($start_date, $end_date);
        
        // Doanh thu woocommerce theo thời gian
        $get_revenue_of_woocommerce_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_woocommerce_orders_confirmed_in_range($start_date, $end_date);
        
        // Doanh thu khác theo thời gian
        $get_revenue_of_others_orders_confirmed_in_range = $this->pancake_orders_model->get_revenue_of_others_orders_confirmed_in_range($start_date, $end_date);
        
        // Doanh số ctv theo thời gian
        $get_sale_of_ctv_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_ctv_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số facebook theo thời gian
        $get_sale_of_facebook_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_facebook_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số shopee theo thời gian
        $get_sale_of_shopee_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_shopee_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số zalo theo thời gian
        $get_sale_of_zalo_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_zalo_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số tiktok theo thời gian
        $get_sale_of_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_tiktok_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số woocommerce theo thời gian
        $get_sale_of_woocommerce_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_woocommerce_orders_confirmed_in_range($start_date, $end_date);

        // Doanh số Khác theo thời gian
        $get_sale_of_others_orders_confirmed_in_range = $this->pancake_orders_model->get_sale_of_others_orders_confirmed_in_range($start_date, $end_date);

        //Chiết khấu Affiliate theo thời gian
        $get_discount_of_ctv_orders_confirmed_in_range = $this->pancake_orders_model->get_discount_of_ctv_orders_confirmed_in_range($start_date, $end_date);
        
        //Chiết khấu Facebook theo thời gian
        $get_discount_of_facebook_confirmed_in_range = $this->pancake_orders_model->get_discount_of_facebook_confirmed_in_range($start_date, $end_date);
        
        //Chiết khấu Shopee theo thời gian
        $get_discount_of_shopee_orders_confirmed_in_range = $this->pancake_orders_model->get_discount_of_shopee_orders_confirmed_in_range($start_date, $end_date);
        
        //Chiết khấu Zalo theo thời gian
        $get_discount_of_zalo_orders_confirmed_in_range = $this->pancake_orders_model->get_discount_of_zalo_orders_confirmed_in_range($start_date, $end_date);
        
        //Chiết khấu Tiktok theo thời gian
        $get_discount_of_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->get_discount_of_tiktok_orders_confirmed_in_range($start_date, $end_date);
        
        //Chiết khấu Woocommerce theo thời gian
        $get_discount_of_woocommerce_confirmed_in_range = $this->pancake_orders_model->get_discount_of_woocommerce_confirmed_in_range($start_date, $end_date);

        //Chiết khấu Khác theo thời gian
        $get_discount_of_others_confirmed_in_range = $this->pancake_orders_model->get_discount_of_others_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Affiliate theo thời gian
        $count_ctv_orders_confirmed_in_range = $this->pancake_orders_model->count_ctv_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Facebook theo thời gian
        $count_facebook_orders_confirmed_in_range = $this->pancake_orders_model->count_facebook_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Shopee theo thời gian
        $count_shopee_orders_confirmed_in_range = $this->pancake_orders_model->count_shopee_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Zalo theo thời gian
        $count_zalo_orders_confirmed_in_range = $this->pancake_orders_model->count_zalo_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Tiktok theo thời gian
        $count_tiktok_orders_confirmed_in_range = $this->pancake_orders_model->count_tiktok_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Woocommerce theo thời gian
        $count_woocommerce_orders_confirmed_in_range = $this->pancake_orders_model->count_woocommerce_orders_confirmed_in_range($start_date, $end_date);

        // Đơn chốt Khác theo thời gian
        $count_others_orders_confirmed_in_range = $this->pancake_orders_model->count_others_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt CTV
        $get_product_ctv_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_ctv_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt Facebook
        $get_product_facebook_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_facebook_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt Shopee
        $get_product_Shopee_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_Shopee_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt Zalo
        $get_product_zalo_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_zalo_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt Tiktok
        $get_product_tiktok_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_tiktok_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt Woocommerce
        $get_product_woocommerce_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_woocommerce_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        // SL hàng chốt
        $get_product_others_quantity_of_orders_confirmed_in_range = $this->pancake_orders_model->get_product_others_quantity_of_orders_confirmed_in_range($start_date, $end_date);

        $data['count_confirmed'] = $count_confirmed_in_range;
        $data['revenue_confirmed'] = $revenue_confirmed_in_range;
        $data['sales_volume_confirmed'] = $sales_volume_confirmed_in_range;
        $data['product_quantity_confirmed'] = $product_quantity_confirmed_in_range;
        $data['aov_confirmed'] = $aov_in_range;
        $data['avg_products_per_order'] = $avg_products_per_order_in_range;
        $data['closing_rate'] = $closing_rate_in_range;
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
        $data['get_discount_of_ctv_orders_confirmed_in_range'] = $get_discount_of_ctv_orders_confirmed_in_range;
        $data['get_discount_of_facebook_confirmed_in_range'] = $get_discount_of_facebook_confirmed_in_range;
        $data['get_discount_of_shopee_orders_confirmed_in_range'] = $get_discount_of_shopee_orders_confirmed_in_range;
        $data['get_discount_of_zalo_orders_confirmed_in_range'] = $get_discount_of_zalo_orders_confirmed_in_range;
        $data['get_discount_of_tiktok_orders_confirmed_in_range'] = $get_discount_of_tiktok_orders_confirmed_in_range;
        $data['get_discount_of_woocommerce_confirmed_in_range'] = $get_discount_of_woocommerce_confirmed_in_range;
        $data['get_discount_of_others_confirmed_in_range'] = $get_discount_of_others_confirmed_in_range;
        $data['count_ctv_orders_confirmed_in_range'] = $count_ctv_orders_confirmed_in_range;
        $data['count_facebook_orders_confirmed_in_range'] = $count_facebook_orders_confirmed_in_range;
        $data['count_shopee_orders_confirmed_in_range'] = $count_shopee_orders_confirmed_in_range;
        $data['count_zalo_orders_confirmed_in_range'] = $count_zalo_orders_confirmed_in_range;
        $data['count_tiktok_orders_confirmed_in_range'] = $count_tiktok_orders_confirmed_in_range;
        $data['count_woocommerce_orders_confirmed_in_range'] = $count_woocommerce_orders_confirmed_in_range;
        $data['count_others_orders_confirmed_in_range'] = $count_others_orders_confirmed_in_range;
        $data['get_product_ctv_quantity_of_orders_confirmed_in_range'] = $get_product_ctv_quantity_of_orders_confirmed_in_range;
        $data['get_product_facebook_quantity_of_orders_confirmed_in_range'] = $get_product_facebook_quantity_of_orders_confirmed_in_range;
        $data['get_product_Shopee_quantity_of_orders_confirmed_in_range'] = $get_product_Shopee_quantity_of_orders_confirmed_in_range;
        $data['get_product_zalo_quantity_of_orders_confirmed_in_range'] = $get_product_zalo_quantity_of_orders_confirmed_in_range;
        $data['get_product_tiktok_quantity_of_orders_confirmed_in_range'] = $get_product_tiktok_quantity_of_orders_confirmed_in_range;
        $data['get_product_woocommerce_quantity_of_orders_confirmed_in_range'] = $get_product_woocommerce_quantity_of_orders_confirmed_in_range;
        $data['get_product_others_quantity_of_orders_confirmed_in_range'] = $get_product_others_quantity_of_orders_confirmed_in_range;

        // Truyền các biến điều khiển
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;
        $data['title'] = 'Dashboard Đơn Hàng Pancake';
        $this->load->view('pancake_sync/dashboard', $data);
    }
}
