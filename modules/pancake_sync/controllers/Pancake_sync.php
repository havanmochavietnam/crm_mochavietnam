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
        // Tải model mới để làm việc với database
        $this->load->model('pancake_orders_model');

        $this->apiUrl = get_option('pancake_url') ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option('pancake_shop_id') ?: "1720001063";
        $this->apiKey = get_option('api_key') ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    /**
     * Hiển thị danh sách đơn hàng LẤY TỪ DATABASE LOCAL.
     * Các bộ lọc (ngày, tìm kiếm,...) sẽ được áp dụng trên database này.
     */
    public function index()
    {
        // Lấy tất cả các tham số filter từ URL
        $filters = $this->input->get();

        // Lấy dữ liệu từ database thông qua model mới
        $response = $this->pancake_orders_model->get_orders_from_db($filters);

        $data['orders'] = $response['data'] ?? [];
        $data['total']  = $response['total'] ?? 0;

        // Lấy danh sách người bán để hiển thị trên bộ lọc
        $data['sellers'] = $this->pancake_orders_model->get_sellers_from_orders();

        $pageSize = (int)($filters['page_size'] ?? 30);

        // --- Cấu hình phân trang ---
        if ($data['total'] > 0 && $pageSize > 0) {
            $this->load->library('pagination');

            $config['base_url']             = admin_url('pancake_sync');
            $config['total_rows']           = $data['total'];
            $config['per_page']             = $pageSize;
            $config['page_query_string']    = TRUE;
            $config['query_string_segment'] = 'page_number';
            $config['reuse_query_string']   = TRUE;
            $config['use_page_numbers']     = TRUE;

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

        // Tải view để hiển thị
        $this->load->view('pancake_sync/orders', $data);
    }

    /**
     * Hàm này được gọi (thường qua AJAX) để kích hoạt quá trình đồng bộ.
     * Nó sẽ gọi API Pancake, lấy dữ liệu và đẩy vào database.
     */
    public function sync_from_api()
    {
        $total_orders_to_sync = 5000;
        $page_size = 1000; // Lấy 1000 đơn mỗi lần (số lượng tối đa API cho phép)
        $number_of_pages = ceil($total_orders_to_sync / $page_size); // Sẽ chạy 5 lần (5000 / 1000)

        $total_rows_affected = 0;

        // Chạy vòng lặp để lấy từng trang một
        for ($page = 1; $page <= $number_of_pages; $page++) {

            // Chuẩn bị tham số cho trang hiện tại
            $params = [
                'page_size'   => $page_size,
                'page_number' => $page, // Yêu cầu trang số 1, rồi 2, rồi 3...
            ];

            $apiResponse = $this->getOrdersFromApi($params);

            // Nếu API trả về thành công và có dữ liệu
            if ($apiResponse['success'] && !empty($apiResponse['data'])) {
                // Gọi hàm sync và cộng dồn số đơn đã xử lý
                $rows_affected_this_page = $this->pancake_orders_model->sync_orders($apiResponse['data']);
                $total_rows_affected += $rows_affected_this_page;
            } else {
                // Nếu API không trả về dữ liệu nữa (đã hết đơn) thì dừng vòng lặp
                break;
            }
        }

        // Trả về kết quả tổng cộng sau khi chạy hết các trang
        header('Content-Type: application/json');
        echo json_encode([
            'success'       => true,
            'message'       => "Đồng bộ hoàn tất! Có {$total_rows_affected} đơn hàng được thêm mới hoặc cập nhật.",
            'rows_affected' => $total_rows_affected
        ]);
    }


    /**
     * Hàm private để gọi API Pancake.
     * Hàm này không thay đổi và được sử dụng bởi sync_from_api().
     * @param array $params
     * @return array
     */
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
