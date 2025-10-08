<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: MBO Sync
Description: Đồng bộ dữ liệu với MBO
Version: 1.0.0
*/

register_activation_hook('mbo_sync', 'mboSyncModuleActivation');
register_uninstall_hook('mbo_sync', 'mboSyncModuleUninstall');

/**
 * Kích hoạt module
 */
function mboSyncModuleActivation()
{
    $CI = &get_instance();

    // Tạo bảng log
    $CI->db->query("CREATE TABLE IF NOT EXISTS " . db_prefix() . "mbo_sync_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sync_type VARCHAR(50) NOT NULL,
        records_synced INT(11) NOT NULL,
        date DATETIME NOT NULL,
        status VARCHAR(20) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // Thêm option mặc định
    add_option('mbo_sync_enabled', '0');
}

/**
 * Gỡ cài đặt module
 */
function mboSyncModuleUninstall()
{
    $CI = &get_instance();

    $CI->db->query("DROP TABLE IF EXISTS " . db_prefix() . "mbo_sync_logs");
    $CI->db->where('name', 'mbo_sync_enabled')->delete(db_prefix() . 'options');
}

// Thêm menu admin
hooks()->add_action('admin_init', function () {
    $CI = &get_instance();

    if (has_permission('mbo_sync', '', 'view')) {
        // Tạo menu cha
        $CI->app_menu->add_sidebar_menu_item('mbo-sync-parent', [
            'name'     => _l('MBO Mắt bảo'),
            'icon'     => 'fa-solid fa-phone-volume',
            'position' => 30,
        ]);

        // Menu con: Dashboard -> Trỏ đến controller MBO_dashboard
        $CI->app_menu->add_sidebar_children_item('mbo-sync-parent', [
            'slug'     => 'mbo-sync-dashboard',
            'name'     => _l('Tổng quan MBO'),
            'href'     => admin_url('mbo_sync/mbo_dashboard'),
            'position' => 5,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('mbo-sync-parent', [
            'slug'     => 'mbo-sync',
            'name'     => _l('Thông tin MBO'),
            'href'     => admin_url('mbo_sync/mbo_view'),
            'position' => 10,
        ]);
    }
});

/**
 * Đăng ký quyền truy cập cho module
 */
hooks()->add_action('staff_permissions', 'mbo_sync_add_permissions');

function mbo_sync_add_permissions($permissions)
{
    $permissions['mbo_sync'] = [
        'name'         => 'MBO Sync',
        'capabilities' => [
            'view'   => 'Xem',
            'create' => 'Thêm',
            'edit'   => 'Sửa',
            'delete' => 'Xóa',
        ],
    ];
    return $permissions;
}
