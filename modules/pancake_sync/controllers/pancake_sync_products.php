<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pancake_sync_products extends AdminController
{
    private $apiUrl;
    private $shopId;
    private $apiKey;

    public function __construct()
    {
        parent::__construct();
        // Tải model để hàm sync() có thể sử dụng
        $this->load->model('pancake_sync/pancake_products_model');

        // Lấy thông tin cấu hình API
        $this->apiUrl = get_option('pancake_url') ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option('pancake_shop_id') ?: "1720001063";
        $this->apiKey = get_option('api_key') ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    /**
     * HIỂN THỊ DANH SÁCH SẢN PHẨM TRỰC TIẾP TỪ API PANCAKE
     */
    public function index()
    {
        $page     = (int)($this->input->get('page_number') ?: 1);
        $pageSize = (int)($this->input->get('page_size') ?: 30);

        // Lấy các tham số filter từ URL để gửi lên API
        $params = [
            'page_number'   => $page,
            'page_size'     => $pageSize,
            // Thêm các filter khác nếu cần
        ];
        
        // Gọi API để lấy dữ liệu cho trang hiện tại
        $response = $this->getProductsFromApi($params);
        $all_products_from_api = $response['data'] ?? [];
        
        // Logic lọc sản phẩm trùng lặp để chỉ hiển thị 1 sản phẩm chính
        $unique_products = [];
        $processed_product_ids = [];
        foreach ($all_products_from_api as $variation) {
            if(isset($variation['product_id'])) {
                $product_id = $variation['product_id'];
                if (!isset($processed_product_ids[$product_id])) {
                    $unique_products[] = $variation;
                    $processed_product_ids[$product_id] = true;
                }
            }
        }

        $data['products'] = $unique_products;
        $data['total']    = isset($response['total']) ? (int)$response['total'] : 0;

        // Xử lý phân trang dựa trên kết quả từ API
        if ($data['total'] > 0 && $pageSize > 0 && ceil($data['total'] / $pageSize) > 1) {
            $this->load->library('pagination');
            $config['base_url']             = admin_url('pancake_sync_products');
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

        $data['title'] = 'Danh sách sản phẩm từ Pancake API';
        $this->load->view('pancake_sync/products', $data);
    }

    /**
     * HÀM ĐỒNG BỘ: Lấy TẤT CẢ sản phẩm từ API và lưu vào DB
     */
    public function sync()
    {
        $all_products = [];
        $page = 1;
        $pageSize = 100;

        do {
            $response = $this->getProductsFromApi(['page_number' => $page, 'page_size' => $pageSize]);
            $products_on_page = $response['data'] ?? [];
            if (!empty($products_on_page)) {
                $all_products = array_merge($all_products, $products_on_page);
            }
            $page++;
        } while (count($products_on_page) === $pageSize);

        if (!empty($all_products)) {
            $synced_count = $this->pancake_products_model->sync_products($all_products);
            set_alert('success', 'Đồng bộ ' . $synced_count . ' biến thể sản phẩm về database thành công!');
        } else {
            set_alert('info', 'Không tìm thấy sản phẩm nào từ Pancake để đồng bộ.');
        }

        redirect(admin_url('pancake_sync_products'));
    }

    /**
     * HÀM GỌI API
     */
    private function getProductsFromApi(array $params = []): array
    {
        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $params['page_number'] ?? 1,
            'page_size'   => $params['page_size'] ?? 30
        ];

        $url = $this->apiUrl . "/shops/{$this->shopId}/products/variations?" . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Accept: application/json"]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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