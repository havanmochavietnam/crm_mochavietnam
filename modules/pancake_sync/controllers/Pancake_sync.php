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

    /**
     * Load view with orders from API
     */
    public function index()
    {
        $page = $this->input->get('page_number') ?: 1;
        $pageSize = $this->input->get('page_size') ?: 30;
        
        $params = [
            'page_number' => $page,
            'page_size'   => $pageSize,
            'search'      => $this->input->get('search') ?: '',
            'filter_status' => $this->input->get('filter_status') ?: [],
            'include_removed' => $this->input->get('include_removed') ?: 0,
            'updateStatus' => $this->input->get('updateStatus') ?: 'inserted_at',
            'startDateTime' => $this->input->get('startDateTime') ? strtotime($this->input->get('startDateTime')) : null,
            'endDateTime' => $this->input->get('endDateTime') ? strtotime($this->input->get('endDateTime')) : null
        ];

        $orders = $this->getOrdersFromApi($params);

        $data['orders'] = $orders['data'] ?? [];
        $data['total']  = $orders['totalRecords'] ?? count($orders['data'] ?? []);
        
        // Tạo phân trang
        $this->load->library('pagination');
        $config['base_url'] = admin_url('pancake_sync');
        $config['total_rows'] = $data['total'];
        $config['per_page'] = $pageSize;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'page_number';
        $config['reuse_query_string'] = true;
        
        // Thêm các tham số tìm kiếm vào phân trang
        $queryParams = [];
        if (!empty($this->input->get('search'))) {
            $queryParams['search'] = $this->input->get('search');
        }
        if (!empty($this->input->get('filter_status'))) {
            $queryParams['filter_status'] = $this->input->get('filter_status');
        }
        if (!empty($this->input->get('include_removed'))) {
            $queryParams['include_removed'] = $this->input->get('include_removed');
        }
        if (!empty($this->input->get('updateStatus'))) {
            $queryParams['updateStatus'] = $this->input->get('updateStatus');
        }
        if (!empty($this->input->get('startDateTime'))) {
            $queryParams['startDateTime'] = $this->input->get('startDateTime');
        }
        if (!empty($this->input->get('endDateTime'))) {
            $queryParams['endDateTime'] = $this->input->get('endDateTime');
        }
        if (!empty($this->input->get('page_size'))) {
            $queryParams['page_size'] = $this->input->get('page_size');
        }
        
        if (!empty($queryParams)) {
            $config['suffix'] = '&' . http_build_query($queryParams);
            $config['first_url'] = $config['base_url'] . '?' . http_build_query($queryParams);
        }
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('pancake_sync/orders', $data);
    }

    /**
     * Get orders from Pancake API
     */
    private function getOrdersFromApi(array $params = []): array
    {
        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $params['page_number'] ?? 1,
            'page_size'   => $params['page_size'] ?? 30
        ];

        if (!empty($params['search'])) {
            $queryParams['search'] = $params['search'];
        }
        if (!empty($params['filter_status'])) {
            $queryParams['filter_status'] = $params['filter_status'];
        }
        if (!empty($params['include_removed'])) {
            $queryParams['include_removed'] = $params['include_removed'];
        }
        if (!empty($params['updateStatus'])) {
            $queryParams['updateStatus'] = $params['updateStatus'];
        }
        if (!empty($params['startDateTime'])) {
            $queryParams['startDateTime'] = $params['startDateTime'];
        }
        if (!empty($params['endDateTime'])) {
            $queryParams['endDateTime'] = $params['endDateTime'];
        }

        $url = $this->apiUrl . "/shops/{$this->shopId}/orders?" . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            return [
                'success' => false,
                'message' => "API Error: HTTP $status",
                'data'    => [],
                'totalRecords' => 0
            ];
        }

        $jsonData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'message' => "JSON parse error",
                'data'    => [],
                'totalRecords' => 0
            ];
        }

        return [
            'success' => true,
            'message' => 'OK',
            'data'    => $jsonData['data'] ?? [],
            'totalRecords' => $jsonData['total'] ?? count($jsonData['data'] ?? [])
        ];
    }
}