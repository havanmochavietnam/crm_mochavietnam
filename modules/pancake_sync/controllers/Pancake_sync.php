<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_sync extends AdminController
{
    private $apiUrl;
    private $shopId;
    private $apiKey;

    public function __construct()
    {
        parent::__construct();

        $this->apiUrl = get_option('pancake_url') ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option('pancake_shop_id') ?: "1720001063";
        $this->apiKey = get_option('api_key') ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    public function index()
    {
        $page     = (int)($this->input->get('page_number') ?: 1);
        $pageSize = (int)($this->input->get('page_size') ?: 30);

        $params = [
            'page_number'     => $page,
            'page_size'       => $pageSize,
            'search'          => $this->input->get('search'),
            'filter_status'   => $this->input->get('filter_status'),
            'include_removed' => $this->input->get('include_removed'),
            'updateStatus'    => $this->input->get('updateStatus'),
            'startDateTime'   => $this->input->get('startDateTime') ? strtotime($this->input->get('startDateTime')) : null,
            'endDateTime'     => $this->input->get('endDateTime') ? strtotime($this->input->get('endDateTime')) : null
        ];

        $response = $this->getOrdersFromApi($params);

        $data['orders'] = $response['data'] ?? [];
        // Đảm bảo total luôn là số nguyên
        $data['total']  = isset($response['total']) ? (int)$response['total'] : 0;

        // Chỉ khởi tạo phân trang nếu có dữ liệu và điều kiện hợp lệ
        if ($data['total'] > 0 && $pageSize > 0 && $data['total'] > $pageSize) {
            $this->load->library('pagination');

            $config['base_url']             = admin_url('pancake_sync');
            $config['total_rows']           = $data['total'];
            $config['per_page']             = $pageSize;
            $config['page_query_string']    = TRUE;
            $config['query_string_segment'] = 'page_number';
            $config['reuse_query_string']   = TRUE;
            $config['use_page_numbers']     = TRUE;

            // Thêm các tham số tìm kiếm vào URL phân trang
            $queryParams = $this->input->get();
            unset($queryParams['page_number']); // Loại bỏ page_number để tránh trùng lặp
            
            if (!empty($queryParams)) {
                $config['suffix'] = '&' . http_build_query($queryParams);
                $config['first_url'] = $config['base_url'] . '?' . http_build_query($queryParams);
            }

            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();
        } else {
            $data['pagination'] = '';
        }

        $data['current_page'] = $page;
        $data['total_pages']  = ($pageSize > 0 && $data['total'] > 0) ? ceil($data['total'] / $pageSize) : 0;

        $this->load->view('pancake_sync/orders', $data);
    }

    private function getOrdersFromApi(array $params = []): array
    {
        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $params['page_number'] ?? 1,
            'page_size'   => $params['page_size'] ?? 30
        ];

        $filters = ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'];
        foreach ($filters as $key) {
            if (isset($params[$key]) && $params[$key] !== '') {
                $queryParams[$key] = $params[$key];
            }
        }

        $url = $this->apiUrl . "/shops/{$this->shopId}/orders?" . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            return ['success' => false, 'message' => "API Error: HTTP {$status}", 'data' => [], 'total' => 0];
        }
        
        $jsonData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => "JSON parse error", 'data' => [], 'total' => 0];
        }

        return [
            'success' => $jsonData['success'] ?? false,
            'message' => $jsonData['message'] ?? 'OK',
            'data'    => $jsonData['data'] ?? [],
            'total'   => isset($jsonData['total_entries']) ? (int)$jsonData['total_entries'] : 0
        ];
    }
}