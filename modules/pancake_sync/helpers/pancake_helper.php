<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('get_pancake_mobile_network')) {
    /**
     * Nhận diện nhà mạng dựa trên đầu số điện thoại Việt Nam.
     * @param string $phoneNumber Số điện thoại cần kiểm tra.
     * @return array Mảng chứa tên nhà mạng và class màu.
     */
    function get_pancake_mobile_network(string $phoneNumber): array
    {
        if (empty($phoneNumber)) {
            return ['name' => '', 'class' => 'default'];
        }

        // Chuẩn hóa SĐT về dạng 9 chữ số (loại bỏ +84, 0)
        if (strpos($phoneNumber, '+84') === 0) {
            $phoneNumber = substr($phoneNumber, 3);
        }
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) < 9) {
            return ['name' => 'Khác', 'class' => 'default'];
        }

        $prefix = substr($phoneNumber, 0, 2);

        $networks = [
            'Viettel'      => ['class' => 'success', 'prefixes' => ['96', '97', '98', '32', '33', '34', '35', '36', '37', '38', '39']],
            'Mobifone'     => ['class' => 'primary', 'prefixes' => ['90', '93', '70', '79', '77', '76', '78']],
            'Vinaphone'    => ['class' => 'info',    'prefixes' => ['91', '94', '83', '84', '85', '81', '82']],
            'Vietnamobile' => ['class' => 'warning', 'prefixes' => ['92', '56', '58']],
            'Gmobile'      => ['class' => 'danger',  'prefixes' => ['99', '59']],
        ];

        foreach ($networks as $name => $details) {
            if (in_array($prefix, $details['prefixes'])) {
                return ['name' => $name, 'class' => $details['class']];
            }
        }

        return ['name' => 'Khác', 'class' => 'default'];
    }
}