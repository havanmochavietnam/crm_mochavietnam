<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Pancake Sync
Description: Đồng bộ dữ liệu với Pancake
Version: 1.0.0
*/

register_activation_hook('pancake_sync', 'pancakeSyncModuleActivation');
register_uninstall_hook('pancake_sync', 'pancakeSyncModuleUninstall');

/**
 * Kích hoạt module
 */
function pancakeSyncModuleActivation()
{
    $CI = &get_instance();

    // Tạo bảng log
    $CI->db->query("CREATE TABLE IF NOT EXISTS " . db_prefix() . "pancake_sync_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sync_type VARCHAR(50) NOT NULL,
        records_synced INT(11) NOT NULL,
        date DATETIME NOT NULL,
        status VARCHAR(20) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // Thêm option mặc định
    add_option('pancake_sync_enabled', '0');
}

/**
 * Gỡ cài đặt module
 */
function pancakeSyncModuleUninstall()
{
    $CI = &get_instance();

    $CI->db->query("DROP TABLE IF EXISTS " . db_prefix() . "pancake_sync_logs");
    $CI->db->where('name', 'pancake_sync_enabled')->delete(db_prefix() . 'options');
}

// Thêm menu admin
hooks()->add_action('admin_init', function () {
    $CI = &get_instance();

    if (has_permission('pancake_sync', '', 'view')) {
        // Tạo menu cha
        $CI->app_menu->add_sidebar_menu_item('pancake-sync-parent', [
            'name'     => _l('POS Pancake'),
            'icon'     => 'fa fa-sync',
            'position' => 30,
        ]);

        // Menu con: Dashboard -> Trỏ đến controller Pancake_dashboard
        $CI->app_menu->add_sidebar_children_item('pancake-sync-parent', [
            'slug'     => 'pancake-sync-dashboard',
            'name'     => _l('Tổng quan đơn hàng'),
            'href'     => admin_url('pancake_sync/pancake_dashboard'), // <-- THAY ĐỔI
            'position' => 5,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('pancake-sync-parent', [
            'slug'     => 'pancake-sync-customers',
            'name'     => _l('Tổng quan sản phẩm'),
            'href'     => admin_url('pancake_sync/pancake_overview_products'), // <-- THAY ĐỔI
            'position' => 10,
        ]);

        // Menu con: Đơn hàng -> Trỏ đến controller Pancake_sync (của bạn)
        $CI->app_menu->add_sidebar_children_item('pancake-sync-parent', [
            'slug'     => 'pancake-sync-orders',
            'name'     => _l('Đơn hàng'),
            'href'     => admin_url('pancake_sync'), // <-- GIỮ NGUYÊN
            'position' => 15,
        ]);

        // Menu con: Sản phẩm -> Trỏ đến controller Pancake_dashboard
        $CI->app_menu->add_sidebar_children_item('pancake-sync-parent', [
            'slug'     => 'pancake-sync-products',
            'name'     => _l('Sản phẩm'),
            'href'     => admin_url('pancake_sync/pancake_sync_products'), // <-- ĐÃ THAY ĐỔI
            'position' => 20,
        ]);
        // Menu con: Khách hàng -> Trỏ đến controller Pancake_dashboard
        $CI->app_menu->add_sidebar_children_item('pancake-sync-parent', [
            'slug'     => 'pancake-sync-customers',
            'name'     => _l('Khách hàng'),
            'href'     => admin_url('pancake_sync/pancake_sync_customers'), // <-- THAY ĐỔI
            'position' => 25,
        ]);
    }
});

/**
 * Đăng ký quyền truy cập cho module
 */
hooks()->add_action('staff_permissions', 'pancake_sync_add_permissions');

function pancake_sync_add_permissions($permissions)
{
    $permissions['pancake_sync'] = [
        'name'         => 'Pancake Sync',
        'capabilities' => [
            'view'   => 'Xem',
            'create' => 'Thêm',
            'edit'   => 'Sửa',
            'delete' => 'Xóa',
        ],
    ];
    return $permissions;
}
