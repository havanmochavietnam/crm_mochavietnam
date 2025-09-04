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
        $CI->app_menu->add_sidebar_menu_item('pancake-sync', [
            'name'     => _l('pancake_sync'),
            'href'     => admin_url('pancake_sync'),
            'icon'     => 'fa fa-sync',
            'position' => 30,
        ]);
    }
});

// Thêm quyền
hooks()->add_filter('staff_permissions', function ($permissions) {
    $permissions['pancake_sync'] = [
        'name' => _l('pancake_sync_permissions'),
        'capabilities' => [
            'view'   => _l('permission_view'),
            'create' => _l('permission_create'),
            'edit'   => _l('permission_edit'),
            'delete' => _l('permission_delete'),
        ],
    ];
    return $permissions;
});
