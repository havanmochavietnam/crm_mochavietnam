<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pancake_dashboard_model', 'dash');
    }

    public function index()
    {
        $start_param = $this->input->get('start_date');
        $end_param   = $this->input->get('end_date');

        // Mặc định hôm nay nếu không có query
        $is_initial = ($start_param === null && $end_param === null);
        $start_date = $start_param ?: date('Y-m-d');
        $end_date   = $end_param   ?: date('Y-m-d');

        // ===== SỐ LIỆU CHÍNH =====
        $m        = $this->dash->get_dashboard_metrics($start_date, $end_date, $is_initial);
        $channels = $this->dash->get_channel_metrics($start_date, $end_date, $is_initial);
        $custSeg  = $this->dash->get_customer_segments_overall($start_date, $end_date, $is_initial);

        // ===== MARKETER =====
        $mk_rows = $this->dash->get_marketer_metrics($start_date, $end_date);
        $marketers = [];
        foreach ((array)$mk_rows as $r) {
            $marketers[] = [
                'marketer_name' => (string)($r['marketer_name'] ?? 'Chưa gán'),
                'revenue'       => (float)  ($r['revenue']  ?? 0),
                'sales'         => (float)  ($r['sales']    ?? 0),
                'orders'        => (int)    ($r['orders']   ?? 0),
                'quantity'      => (int)    ($r['quantity'] ?? 0),
                'aov'           => (float)  ($r['aov']      ?? 0),
                'discount'      => (float)  ($r['discount'] ?? 0),
            ];
        }

        // ===== CSKH =====
        $cskh_rows = $this->dash->get_cskh_metrics($start_date, $end_date);
        $cskhs = [];
        foreach ((array)$cskh_rows as $r) {
            $cskhs[] = [
                'cskh_name' => (string)($r['cskh_name'] ?? 'Chưa gán'),
                'revenue'   => (float)  ($r['revenue']   ?? 0),
                'sales'     => (float)  ($r['sales']     ?? 0),
                'orders'    => (int)    ($r['orders']    ?? 0),
                'quantity'  => (int)    ($r['quantity']  ?? 0),
                'aov'       => (float)  ($r['aov']       ?? 0),
                'discount'  => (float)  ($r['discount']  ?? 0),
            ];
        }

        // SALE
        $sale_rows = $this->dash->get_sale_metrics($start_date, $end_date);
        $saless = [];
        foreach ((array)$sale_rows as $r) {
            $saless[] = [
                'sale_name' => (string)($r['sale_name'] ?? 'Chưa gán'),
                'revenue'   => (float)  ($r['revenue']   ?? 0),
                'sales'     => (float)  ($r['sales']     ?? 0),
                'orders'    => (int)    ($r['orders']    ?? 0),
                'quantity'  => (int)    ($r['quantity']  ?? 0),
                'aov'       => (float)  ($r['aov']       ?? 0),
                'discount'  => (float)  ($r['discount']  ?? 0),
            ];
        }

        // ===== TÍNH TOÁN PHÁI SINH =====
        $count_confirmed_in_range        = (int)($m['count_confirmed_in_range'] ?? 0);
        $revenue_confirmed_in_range      = (float)($m['revenue_confirmed_in_range'] ?? 0);
        $sales_volume_confirmed_in_range = (float)($m['sales_volume_confirmed_in_range'] ?? 0);
        $product_quantity_in_range       = (int)($m['product_quantity_confirmed_in_range'] ?? 0);
        $count_created_in_range          = (int)($m['count_created_in_range'] ?? 0);

        $aov_in_range                    = $count_confirmed_in_range > 0 ? ($revenue_confirmed_in_range / $count_confirmed_in_range) : 0;
        $avg_products_per_order_in_range = $count_confirmed_in_range > 0 ? ($product_quantity_in_range / $count_confirmed_in_range) : 0;
        $closing_rate_in_range           = $count_created_in_range > 0 ? ($count_confirmed_in_range / $count_created_in_range * 100) : 0;

        $count_created_today   = (int)($m['count_created_today'] ?? 0);
        $count_confirmed_today = (int)($m['count_confirmed_today'] ?? 0);
        $closing_rate_today    = $count_created_today > 0 ? ($count_confirmed_today / $count_created_today * 100) : 0;

        // Helper lấy channel; ưu tiên CTV, fallback 'Affiliate' để không vỡ nếu dữ liệu cũ
        $get = function ($ch) use ($channels) {
            return $channels[$ch] ?? ['revenue' => 0, 'sales' => 0, 'discount' => 0, 'orders' => 0, 'quantity' => 0, 'aov' => 0, 'cust_new' => 0, 'cust_returning' => 0];
        };

        // ⚠️ Đổi nguồn: dùng 'CTV' thay cho 'Affiliate'
        $aff   = !empty($channels['CTV']) ? $channels['CTV'] : $get('Affiliate');
        $fb    = $get('Facebook');
        $shp   = $get('Shopee');
        $zl    = $get('Zalo');
        $tt    = $get('Tiktok');
        $woo   = $get('Woocommerce');
        $hl    = $get('Hotline');
        $ladi  = $get('LadiPage');
        $other = $get('Khác');

        // ===== DATA CHO VIEW =====
        $data = [];

        // Tổng quan
        $data['count_confirmed']            = $count_confirmed_in_range;
        $data['revenue_confirmed']          = $revenue_confirmed_in_range;
        $data['sales_volume_confirmed']     = $sales_volume_confirmed_in_range;
        $data['product_quantity_confirmed'] = $product_quantity_in_range;
        $data['aov_confirmed']              = $aov_in_range;
        $data['avg_products_per_order']     = $avg_products_per_order_in_range;
        $data['closing_rate']               = $closing_rate_in_range;

        // Hôm nay
        $data['count_confirmed_today']                = $count_confirmed_today;
        $data['revenue_confirmed_today']              = (float)($m['revenue_confirmed_today'] ?? 0);
        $data['count_orders_new_today']               = $count_created_today;
        $data['count_orders_canceled_today']          = (int)($m['count_canceled_today'] ?? 0);
        $data['count_orders_removed_today']           = (int)($m['count_removed_today'] ?? 0);
        $data['count_unique_customers_today']         = (int)($m['unique_customers_today'] ?? 0);
        $data['closing_rate_today']                   = $closing_rate_today;
        $data['get_product_quantity_confirmed_today'] = (int)($m['product_quantity_confirmed_today'] ?? 0);

        // Theo nguồn (giữ nguyên tên biến cũ để không phải sửa view)
        $data['get_revenue_of_affiliate_orders_confirmed_in_range']   = $aff['revenue'];
        $data['get_revenue_of_facebook_orders_confirmed_in_range']    = $fb['revenue'];
        $data['get_revenue_of_shopee_orders_confirmed_in_range']      = $shp['revenue'];
        $data['get_revenue_of_zalo_orders_confirmed_in_range']        = $zl['revenue'];
        $data['get_revenue_of_tiktok_orders_confirmed_in_range']      = $tt['revenue'];
        $data['get_revenue_of_woocommerce_orders_confirmed_in_range'] = $woo['revenue'];
        $data['get_revenue_of_hotline_orders_confirmed_in_range']     = $hl['revenue'];
        $data['get_revenue_of_ladipage_orders_confirmed_in_range']    = $ladi['revenue'];
        $data['get_revenue_of_others_orders_confirmed_in_range']      = $other['revenue'];

        $data['get_sale_of_ctv_orders_confirmed_in_range']         = $aff['sales'];
        $data['get_sale_of_facebook_orders_confirmed_in_range']    = $fb['sales'];
        $data['get_sale_of_shopee_orders_confirmed_in_range']      = $shp['sales'];
        $data['get_sale_of_zalo_orders_confirmed_in_range']        = $zl['sales'];
        $data['get_sale_of_tiktok_orders_confirmed_in_range']      = $tt['sales'];
        $data['get_sale_of_woocommerce_orders_confirmed_in_range'] = $woo['sales'];
        $data['get_sale_of_hotline_orders_confirmed_in_range']     = $hl['sales'];
        $data['get_sale_of_ladipage_orders_confirmed_in_range']    = $ladi['sales'];
        $data['get_sale_of_others_orders_confirmed_in_range']      = $other['sales'];

        $data['get_discount_of_ctv_orders_confirmed_in_range']    = $aff['discount'];
        $data['get_discount_of_facebook_confirmed_in_range']      = $fb['discount'];
        $data['get_discount_of_shopee_orders_confirmed_in_range'] = $shp['discount'];
        $data['get_discount_of_zalo_orders_confirmed_in_range']   = $zl['discount'];
        $data['get_discount_of_tiktok_orders_confirmed_in_range'] = $tt['discount'];
        $data['get_discount_of_woocommerce_confirmed_in_range']   = $woo['discount'];
        $data['get_discount_of_hotline_confirmed_in_range']       = $hl['discount'];
        $data['get_discount_of_ladipage_confirmed_in_range']      = $ladi['discount'];
        $data['get_discount_of_others_confirmed_in_range']        = $other['discount'];

        $data['count_ctv_orders_confirmed_in_range']         = $aff['orders'];
        $data['count_facebook_orders_confirmed_in_range']    = $fb['orders'];
        $data['count_shopee_orders_confirmed_in_range']      = $shp['orders'];
        $data['count_zalo_orders_confirmed_in_range']        = $zl['orders'];
        $data['count_tiktok_orders_confirmed_in_range']      = $tt['orders'];
        $data['count_woocommerce_orders_confirmed_in_range'] = $woo['orders'];
        $data['count_hotline_orders_confirmed_in_range']     = $hl['orders'];
        $data['count_ladipage_orders_confirmed_in_range']    = $ladi['orders'];
        $data['count_others_orders_confirmed_in_range']      = $other['orders'];

        $data['get_product_ctv_quantity_of_orders_confirmed_in_range']         = $aff['quantity'];
        $data['get_product_facebook_quantity_of_orders_confirmed_in_range']    = $fb['quantity'];
        $data['get_product_Shopee_quantity_of_orders_confirmed_in_range']      = $shp['quantity'];
        $data['get_product_zalo_quantity_of_orders_confirmed_in_range']        = $zl['quantity'];
        $data['get_product_tiktok_quantity_of_orders_confirmed_in_range']      = $tt['quantity'];
        $data['get_product_woocommerce_quantity_of_orders_confirmed_in_range'] = $woo['quantity'];
        $data['get_product_hotline_quantity_of_orders_confirmed_in_range']     = $hl['quantity'];
        $data['get_product_ladipage_quantity_of_orders_confirmed_in_range']    = $ladi['quantity'];
        $data['get_product_others_quantity_of_orders_confirmed_in_range']      = $other['quantity'];

        $data['get_aov_of_ctv_orders_confirmed_in_range']         = $aff['aov'];
        $data['get_aov_of_facebook_orders_confirmed_in_range']    = $fb['aov'];
        $data['get_aov_of_shopee_orders_confirmed_in_range']      = $shp['aov'];
        $data['get_aov_of_zalo_orders_confirmed_in_range']        = $zl['aov'];
        $data['get_aov_of_tiktok_orders_confirmed_in_range']      = $tt['aov'];
        $data['get_aov_of_woocommerce_orders_confirmed_in_range'] = $woo['aov'];
        $data['get_aov_of_hotline_orders_confirmed_in_range']     = $hl['aov'];
        $data['get_aov_of_ladipage_orders_confirmed_in_range']    = $ladi['aov'];
        $data['get_aov_of_others_orders_confirmed_in_range']      = $other['aov'];

        $data['cust_new_affiliate']        = $aff['cust_new'];
        $data['cust_new_facebook']         = $fb['cust_new'];
        $data['cust_new_shopee']           = $shp['cust_new'];
        $data['cust_new_zalo']             = $zl['cust_new'];
        $data['cust_new_tiktok']           = $tt['cust_new'];
        $data['cust_new_woocommerce']      = $woo['cust_new'];
        $data['cust_new_hotline']          = $hl['cust_new'];
        $data['cust_new_ladipage']         = $ladi['cust_new'];
        $data['cust_new_others']           = $other['cust_new'];

        $data['cust_returning_affiliate']  = $aff['cust_returning'];
        $data['cust_returning_facebook']   = $fb['cust_returning'];
        $data['cust_returning_shopee']     = $shp['cust_returning'];
        $data['cust_returning_zalo']       = $zl['cust_returning'];
        $data['cust_returning_tiktok']     = $tt['cust_returning'];
        $data['cust_returning_woocommerce'] = $woo['cust_returning'];
        $data['cust_returning_hotline']    = $hl['cust_returning'];
        $data['cust_returning_ladipage']   = $ladi['cust_returning'];
        $data['cust_returning_others']     = $other['cust_returning'];

        $data['cust_new_total']       = (int)($custSeg['cust_new'] ?? 0);
        $data['cust_returning_total'] = (int)($custSeg['cust_returning'] ?? 0);

        $data['count_hotline_ladipage_confirmed_in_range'] = $data['count_ladipage_orders_confirmed_in_range'];
        $data['tai_quay_doanh_thu'] = 0;
        $data['tai_quay_don_chot']  = 0;

        // Truyền mảng cho 3 bảng
        $data['marketers'] = $marketers;
        $data['cskhs']     = $cskhs;
        $data['saless']     = $saless;

        // Điều khiển view
        $data['start_date'] = $start_date;
        $data['end_date']   = $end_date;
        $data['title']      = 'Dashboard Đơn Hàng Pancake';

        $this->load->view('pancake_sync/dashboard', $data);
    }
}
