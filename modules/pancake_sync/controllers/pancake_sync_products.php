<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_sync_products extends AdminController
{
    private $apiUrl;
    private $shopId;
    private $apiKey;

    public function __construct()
    {
        parent::__construct();
        // Load model
        $this->load->model('pancake_sync/pancake_products_model', 'pancake_products');

        // Config API
        $this->apiUrl = get_option('pancake_url') ?: "https://pos.pages.fm/api/v1";
        $this->shopId = get_option('pancake_shop_id') ?: "1720001063";
        $this->apiKey = get_option('api_key') ?: "fde1951a7d0e4c3b976aedb1776e731e";
    }

    public function index()
    {
        $page     = (int)($this->input->get('page_number') ?: 1);
        $pageSize = (int)($this->input->get('page_size') ?: 30);

        $params = [
            'page_number' => $page,
            'page_size'   => $pageSize,
        ];

        $response = $this->getProductsFromApi($params);
        $all_products_from_api = $response['data'] ?? [];

        // Lọc trùng theo product_id
        $unique_products   = [];
        $processed_ids     = [];
        foreach ($all_products_from_api as $variation) {
            if (isset($variation['product_id'])) {
                $pid = $variation['product_id'];
                if (!isset($processed_ids[$pid])) {
                    $unique_products[] = $variation;
                    $processed_ids[$pid] = true;
                }
            }
        }

        $data['products'] = $unique_products;
        $data['total']    = $response['total'] ?? 0;

        // Phân trang
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
                $config['suffix']    = '&' . http_build_query($queryParams);
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
            // gọi model qua alias
            $synced_count = $this->pancake_products->sync_products($all_products);
            set_alert('success', 'Đồng bộ ' . $synced_count . ' sản phẩm về database thành công!');
        } else {
            set_alert('info', 'Không tìm thấy sản phẩm nào từ Pancake để đồng bộ.');
        }

        redirect(admin_url('pancake_sync/pancake_sync_products'));
    }

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
            'total'   => (int)($jsonData['total_entries'] ?? 0)
        ];
    }
}
