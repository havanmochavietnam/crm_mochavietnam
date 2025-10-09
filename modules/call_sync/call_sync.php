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
    $prefix = db_prefix();

    // Bảng log đồng bộ
    $CI->db->query("CREATE TABLE IF NOT EXISTS {$prefix}call_sync_logs (
        id INT(11) NOT NULL AUTO_INCREMENT,
        sync_type VARCHAR(50) NOT NULL,
        records_synced INT(11) DEFAULT 0,
        date DATETIME NOT NULL,
        status VARCHAR(20) NOT NULL,
        message TEXT NULL,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Bảng lưu token
    $CI->db->query("CREATE TABLE IF NOT EXISTS {$prefix}call_token (
        id INT(11) NOT NULL AUTO_INCREMENT,
        base_url VARCHAR(1000) NOT NULL,
        service_name VARCHAR(255) DEFAULT NULL,
        auth_user VARCHAR(255) DEFAULT NULL,
        auth_key VARCHAR(255) DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Bảng lưu lịch sử cuộc gọi
    $CI->db->query("CREATE TABLE IF NOT EXISTS {$prefix}call_history (
        id INT(11) NOT NULL AUTO_INCREMENT,
        unique_key VARCHAR(255) DEFAULT NULL,
        call_key VARCHAR(255) DEFAULT NULL,
        call_date DATETIME DEFAULT NULL,
        caller_number VARCHAR(50) DEFAULT NULL,
        caller_name VARCHAR(100) DEFAULT NULL,
        head_number VARCHAR(50) DEFAULT NULL,
        receive_number VARCHAR(50) DEFAULT NULL,
        status VARCHAR(50) DEFAULT NULL,
        total_call_time INT DEFAULT 0,
        real_call_time INT DEFAULT 0,
        link_file TEXT DEFAULT NULL,
        gsm_port VARCHAR(50) DEFAULT NULL,
        type_call VARCHAR(50) DEFAULT NULL,
        raw_response JSON DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY uq_unique_key (unique_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    // Thêm option bật/tắt module
    add_option('call_sync_enabled', '0');
}

/**
 * Gỡ cài đặt module
 */
function callSyncModuleUninstall()
{
    $CI = &get_instance();
    $prefix = db_prefix();

    $CI->db->query("DROP TABLE IF EXISTS {$prefix}call_sync_logs");
    $CI->db->query("DROP TABLE IF EXISTS {$prefix}call_token");
    $CI->db->query("DROP TABLE IF EXISTS {$prefix}call_history");

    // remove options
    $CI->db->where('name', 'call_sync_enabled')->delete($prefix . 'options');
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

        // Menu con: Dashboard -> Trỏ đến controller Call_dashboard
        $CI->app_menu->add_sidebar_children_item('call-sync-parent', [
            'slug'     => 'call-sync-dashboard',
            'name'     => _l('Tổng quan'),
            'href'     => admin_url('call_sync/call_dashboard'),
            'position' => 5,
        ]);

        $CI->app_menu->add_sidebar_children_item('call-sync-parent', [
            'slug'     => 'call-sync',
            'name'     => _l('Thông tin cuộc gọi'),
            'href'     => admin_url('call_sync'),
            'position' => 10,
        ]);
        $CI->app_menu->add_sidebar_children_item('call-sync-parent', [
            'slug'     => 'call-sync-setup',
            'name'     => _l('Cài đặt & Đồng bộ'),
            'href'     => admin_url('call_sync/call_settings'),
            'position' => 15,
        ]);
    }
});

/**
 * Đăng ký quyền truy cập cho module
 */
hooks()->add_action('staff_permissions', 'call_sync_add_permissions');

function call_sync_add_permissions($permissions)
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
