<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Tổng đài
Description: Đồng bộ dữ liệu với MBO
Version: 1.0.0
*/

register_activation_hook('call_sync', 'callSyncModuleActivation');
register_uninstall_hook('call_sync', 'callSyncModuleUninstall');

/**
 * Kích hoạt module
 */
function callSyncModuleActivation()
{
    $CI = &get_instance();

    // Tạo bảng log
    $CI->db->query("CREATE TABLE IF NOT EXISTS " . db_prefix() . "call_sync_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sync_type VARCHAR(50) NOT NULL,
        records_synced INT(11) NOT NULL,
        date DATETIME NOT NULL,
        status VARCHAR(20) NOT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    // Thêm option mặc định
    add_option('call_sync_enabled', '0');
}

/**
 * Gỡ cài đặt module
 */
function callSyncModuleUninstall()
{
    $CI = &get_instance();

    $CI->db->query("DROP TABLE IF EXISTS " . db_prefix() . "call_sync_logs");
    $CI->db->where('name', 'call_sync_enabled')->delete(db_prefix() . 'options');
}

// Thêm menu admin
hooks()->add_action('admin_init', function () {
    $CI = &get_instance();

    if (has_permission('call_sync', '', 'view')) {
        // Tạo menu cha
        $CI->app_menu->add_sidebar_menu_item('call-sync-parent', [
            'name'     => _l('Tổng đài'),
            'icon'     => 'fa-solid fa-phone-volume',
            'position' => 30,
        ]);

        $CI->app_menu->add_sidebar_children_item('call-sync-parent', [
            'slug'     => 'call-sync-dashboard',
            'name'     => _l('Tổng quan'),  
            'href'     => admin_url('call_sync/call_dashboard'),
            'position' => 5,
        ]);
        
        $CI->app_menu->add_sidebar_children_item('call-sync-parent', [
            'slug'     => 'call-sync-view',
            'name'     => _l('Thông tin cuộc gọi'),
            'href'     => admin_url('call_sync'),
            'position' => 10,
        ]);
    }
});

/**
 * Đăng ký quyền truy cập cho module
 */
hooks()->add_action('staff_permissions', 'call_sync_add_permissions');

function mbo_sync_add_permissions($permissions)
{
    $permissions['call_sync'] = [
        'name'         => 'Call Sync',
        'capabilities' => [
            'view'   => 'Xem',
            'create' => 'Thêm',
            'edit'   => 'Sửa',
            'delete' => 'Xóa',
        ],
    ];
    return $permissions;
}
