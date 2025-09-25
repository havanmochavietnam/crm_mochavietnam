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
        $this->load->model('pancake_orders_model');

        $this->apiUrl = get_option('pancake_url') ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option('pancake_shop_id') ?: "1720001063";
        $this->apiKey = get_option('api_key') ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    // Giữ nguyên hàm index của bạn
    public function index()
    {
        $filters = $this->input->get();
        $response = $this->pancake_orders_model->get_orders_from_db($filters);

        $data['orders'] = $response['data'] ?? [];
        $data['total']  = $response['total'] ?? 0;
        $data['sellers'] = $this->pancake_orders_model->get_sellers_from_orders();
        $pageSize = (int)($filters['page_size'] ?? 30);

        if ($data['total'] > 0 && $pageSize > 0) {
            $this->load->library('pagination');
            $config['base_url']            = admin_url('pancake_sync');
            $config['total_rows']          = $data['total'];
            $config['per_page']            = $pageSize;
            $config['page_query_string']   = TRUE;
            $config['query_string_segment'] = 'page_number';
            $config['reuse_query_string']  = TRUE;
            $config['use_page_numbers']    = TRUE;
            $queryParams = $this->input->get();
            unset($queryParams['page_number']);
            if (!empty($queryParams)) {
                $config['suffix'] = '&' . http_build_query($queryParams);
                $config['first_url'] = $config['base_url'] . '?' . http_build_query($queryParams);
            }
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links();
        } else {
            $data['pagination'] = '';
        }

        $data['current_page'] = (int)($filters['page_number'] ?? 1);
        $data['total_pages']  = ($pageSize > 0 && $data['total'] > 0) ? ceil($data['total'] / $pageSize) : 0;

        $this->load->view('pancake_sync/orders', $data);
    }

    /**
     * BƯỚC 1 CỦA QUÁ TRÌNH SYNC: Được gọi đầu tiên bởi AJAX.
     * Chỉ lấy tổng số đơn hàng từ API để JS biết được tiến trình.
     */
    public function start_sync()
    {
        header('Content-Type: application/json');

        // Chốt mỗi lần đồng bộ chỉ lấy 1 page 1000 đơn
        $page_size   = 1000;
        $page_number = 1;

        // Chạm API nhẹ để xác thực cấu hình / key hợp lệ
        $head = $this->getOrdersFromApi(['page_size' => 1, 'page_number' => 1]);
        if (!$head['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể kết nối đến API Pancake. Vui lòng kiểm tra lại cấu hình.'
            ]);
            return;
        }

        echo json_encode([
            'success'     => true,
            'page_size'   => $page_size,
            'total_pages' => 1,
            'next_page'   => $page_number,
        ]);
    }

    public function sync_page()
    {
        header('Content-Type: application/json');

        $page_number = (int)$this->input->post('page');
        if ($page_number <= 0) $page_number = 1;

        $page_size = 1000;

        // Nếu muốn cho phép filter kèm theo (tùy bạn), gom ở đây
        $allowedFilters = ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'];
        $params = ['page_number' => $page_number, 'page_size' => $page_size];
        foreach ($allowedFilters as $f) {
            $v = $this->input->post($f);
            if ($v !== null && $v !== '') $params[$f] = $v;
        }

        // Retry nhẹ 1 lần
        $attempts = 0;
        $apiResponse = ['success' => false, 'data' => []];
        while ($attempts < 2) {
            $attempts++;
            $apiResponse = $this->getOrdersFromApi($params);
            if ($apiResponse['success']) break;
            usleep(300000);
        }

        if (!$apiResponse['success']) {
            echo json_encode([
                'status'  => 'error',
                'message' => 'Lỗi gọi API. Vui lòng thử lại.'
            ]);
            return;
        }

        $batch = is_array($apiResponse['data']) ? $apiResponse['data'] : [];
        $rows_affected = 0;
        if (!empty($batch)) {
            $rows_affected = $this->pancake_orders_model->sync_orders($batch);
        }

        // KẾT THÚC sau 1 trang
        echo json_encode([
            'status'          => 'complete',
            'processed_count' => count($batch),
            'rows_affected'   => $rows_affected,
            'page'            => $page_number,
            'page_size'       => $page_size,
            'message'         => 'Đồng bộ 1 trang (1000 đơn) hoàn tất.'
        ]);
    }



    // Giữ nguyên hàm getOrdersFromApi của bạn
    private function getOrdersFromApi(array $params = []): array
    {
        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $params['page_number'] ?? 1,
            'page_size'   => $params['page_size'] ?? 30,
        ];

        $filters = ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'];
        foreach ($filters as $key) {
            if (isset($params[$key]) && $params[$key] !== '') {
                $queryParams[$key] = $params[$key];
            }
        }

        $url = $this->apiUrl . "/shops/{$this->shopId}/orders?" . http_build_query($queryParams);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Accept: application/json"],
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_TCP_KEEPALIVE  => 1,
            CURLOPT_TCP_KEEPIDLE   => 30,
            CURLOPT_TCP_KEEPINTVL  => 10,
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || $response === false) {
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
            'total'   => isset($jsonData['total_entries']) ? (int)$jsonData['total_entries'] : 0,
        ];
    }
}
