<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_sync_products extends AdminController
{
    private $apiUrl;
    private $shopId;
    private $apiKey;

    private const PANCAKE_URL_OPTION = 'pancake_url';
    private const PANCAKE_SHOP_ID_OPTION = 'pancake_shop_id';
    private const PANCAKE_API_KEY_OPTION = 'api_key';

    public function __construct()
    {
        parent::__construct();
        // Load model
        $this->load->model('pancake_sync/pancake_products_model', 'pancake_products');
        //Load library
        $this->load->library('pagination');

        $this->apiUrl = get_option(self::PANCAKE_URL_OPTION) ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option(self::PANCAKE_SHOP_ID_OPTION) ?: "1720001063";
        $this->apiKey = get_option(self::PANCAKE_API_KEY_OPTION) ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    // INDEX CHO TRANG CONTROLLER
    public function index()
    {
        $filters = [
            'page_number' => (int)($this->input->get('page_number') ?: 1),
            'page_size'   => (int)($this->input->get('page_size') ?: 30),
            'search_ids'  => trim($this->input->get('search_ids', true) ?? ''), // Đổi tên cho rõ nghĩa
        ];

        $response = $this->getProductsFromApi($filters);

        $data = [
            'products'     => [],
            'total'         => 0,
            'pagination'    => '',
            'error_message' => '',
            'title'         => 'Tìm kiếm khách hàng Pancake theo ID',
        ];
        $data = array_merge($data, $filters);

        if (!$response['success']) {
            $data['error_message'] = "Lỗi API: " . $response['message'];
        } else {
            $data['products'] = $this->getUniqueProducts($response['data'] ?? []);
            $data['total']     = $response['total'];

            if ($data['total'] > 0 && $filters['page_size'] > 0) {
                $data['pagination'] = $this->setupPagination($data['total'], $filters['page_size']);
            }
        }

        $data['title']      = 'Sản phẩm của Mocha';

        $this->load->view('pancake_sync/products', $data);
    }

    // LẤY THỒN TIN SẢN PHẨM
    private function getProductsFromApi(array $filters = []): array
    {
        if (empty($this->apiKey) || empty($this->shopId)) {
            return ['success' => false, 'message' => "Chưa cấu hình API Key hoặc Shop ID.", 'data' => [], 'total' => 0];
        }

        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $filters['page_number'] ?? 1,
            'page_size'   => $filters['page_size'] ?? 30,
        ];

        // ĐƠN GIẢN HÓA: Chỉ xử lý tìm kiếm theo 'customer_ids'
        if (!empty($filters['search_ids'])) {
            $queryParams['search'] = $filters['search_ids'];
        }

        $url = "{$this->apiUrl}/shops/{$this->shopId}/products/variations?" . http_build_query($queryParams);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ["Accept: application/json"],
            CURLOPT_TIMEOUT => 30, CURLOPT_CONNECTTIMEOUT => 10
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode !== 200) {
            return ['success' => false, 'message' => "Lỗi API, mã HTTP: {$httpCode}", 'data' => [], 'total' => 0];
        }

        $jsonData = json_decode($response, true);
        return [
            'success' => $jsonData['success'] ?? false,
            'message' => $jsonData['message'] ?? 'Thành công',
            'data'    => $jsonData['data'] ?? [],
            'total'   => (int)($jsonData['total_entries'] ?? 0)
        ];
    }

    // ĐỒNG BỘ SẢN PHẨM
    public function sync()
    {
        $all_products = [];
        $page = 1;
        $pageSize = 100;

        do {
            $response = $this->getProductsFromApi(['page_number' => $page, 'page_size' => $pageSize]);
            $products_on_page = $response['data'] ?? [];
            if (!empty($products_on_page)) {
                $all_products = array_merge($products_on_page, $products_on_page);
            }
            $page++;
        } while (count($products_on_page) === $pageSize);

        if (!empty($all_products)) {
            $synced_count = $this->pancake_products->sync_products($all_products);
            set_alert('success', 'Đồng bộ ' . $synced_count . ' sản phẩm về database thành công!');
        } else {
            set_alert('info', 'Không tìm thấy sản phẩm nào từ Pancake để đồng bộ.');
        }

        redirect(admin_url('pancake_sync/pancake_sync_products'));
    }

    // Hàm getUniqueCustomers và setupPagination
    private function getUniqueProducts(array $products): array
    {
        $unique_products = [];
        $processed_ids    = [];
        foreach ($products as $product) {
            if (isset($product['barcode']) && !isset($processed_ids[$product['barcode']])) {
                $unique_products[] = $product;
                $processed_ids[$product['barcode']] = true;
            }
        }
        return $unique_products;
    }

    // PHÂN TRANG THEO THƯ VIỆN SOURCE
    private function setupPagination(int $totalRows, int $pageSize): string
    {
        $config = [
            'base_url'             => admin_url('pancake_sync/pancake_sync_customers'),
            'total_rows'           => $totalRows,
            'per_page'             => $pageSize,
            'page_query_string'    => true,      // Use ?page_number=...
            'query_string_segment' => 'page_number',
            'use_page_numbers'     => true,      // Use actual page numbers (1, 2, 3...)
            'reuse_query_string'   => true,      // IMPORTANT: Keeps other query params (like search) when changing pages

            // --- HTML Customization for Bootstrap ---
            'full_tag_open'        => '<nav aria-label="Page navigation"><ul class="pagination">',
            'full_tag_close'       => '</ul></nav>',

            'first_link'           => '&laquo;', // First
            'first_tag_open'       => '<li class="page-item">',
            'first_tag_close'      => '</li>',

            'last_link'            => '&raquo;', // Last
            'last_tag_open'        => '<li class="page-item">',
            'last_tag_close'       => '</li>',

            'next_link'            => '&gt;',   // Next
            'next_tag_open'        => '<li class="page-item">',
            'next_tag_close'       => '</li>',

            'prev_link'            => '&lt;',   // Previous
            'prev_tag_open'        => '<li class="page-item">',
            'prev_tag_close'       => '</li>',

            'cur_tag_open'         => '<li class="page-item active" aria-current="page"><span class="page-link">',
            'cur_tag_close'        => '</span></li>',

            'num_tag_open'         => '<li class="page-item">',
            'num_tag_close'        => '</li>',

            'attributes'           => ['class' => 'page-link'], // Add class to all <a> tags
        ];

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }
}