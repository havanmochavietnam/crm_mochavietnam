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
        // Gọi API chỉ với 1 đơn hàng để lấy được tổng số 'total_entries'
        $apiResponse = $this->getOrdersFromApi(['page_size' => 1]);

        if (!$apiResponse['success']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không thể kết nối đến API Pancake. Vui lòng kiểm tra lại cấu hình.']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'total_orders' => $apiResponse['total'], // Trả về tổng số đơn có trên Pancake
        ]);
    }

    /**
     * BƯỚC 2 CỦA QUÁ TRÌNH SYNC: Đồng bộ một trang duy nhất.
     * Được gọi lặp đi lặp lại bởi AJAX cho đến khi hoàn tất.
     */
    public function sync_page()
    {
        $page = (int)$this->input->post('page');
        if ($page <= 0) {
            $page = 1;
        }
        
        $page_size = 100; // Mỗi lần đồng bộ 100 đơn -> nhanh và an toàn, không timeout.

        $apiResponse = $this->getOrdersFromApi([
            'page_size'   => $page_size,
            'page_number' => $page,
        ]);

        // Nếu API trả về lỗi hoặc không có đơn hàng nào nữa (đã hết)
        if (!$apiResponse['success'] || empty($apiResponse['data'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'complete', // Báo cho JS biết là đã đồng bộ xong
                'message' => "Đồng bộ hoàn tất!",
            ]);
            return;
        }
        
        // Gọi model để lưu dữ liệu của trang này vào DB
        $rows_affected = $this->pancake_orders_model->sync_orders($apiResponse['data']);

        // Trả về kết quả của lần chạy này, và báo cho JS tiếp tục xử lý trang kế tiếp
        header('Content-Type: application/json');
        echo json_encode([
            'status'          => 'processing', // Báo cho JS biết vẫn đang xử lý, chưa xong
            'next_page'       => $page + 1,
            'processed_count' => count($apiResponse['data']), // Số đơn xử lý trong lần này
            'rows_affected'   => $rows_affected,
        ]);
    }

    // Giữ nguyên hàm getOrdersFromApi của bạn
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