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

    // INDEX CHO CONTROLLER
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

        $data['title']      = 'Tất cả đơn hàng Mocha';

        $this->load->view('pancake_sync/orders', $data);
    }

    // BẮT ĐẦU ĐỒNG BỘ
    public function start_sync()
    {
        header('Content-Type: application/json');
        $page_size = 1000;
        if (function_exists('set_time_limit')) @set_time_limit(0);
        if (function_exists('ignore_user_abort')) @ignore_user_abort(true);
        $params = [
            'page_size'   => $page_size,
            'page_number' => 1,
        ];
        $head = $this->getOrdersFromApi($params);
        if (!$head['success']) {
            echo json_encode([
                'success' => false,
                'message' => 'Không thể kết nối đến API Pancake. Vui lòng kiểm tra lại cấu hình.'
            ]);
            return;
        }
        if (isset($head['data']['total_pages']) && (int)$head['data']['total_pages'] > 0) {
            $total_pages = (int)$head['data']['total_pages'];
        } else {
            $total_entries = (int)($head['total'] ?? 0);
            $total_pages   = $total_entries > 0 ? (int)ceil($total_entries / $page_size) : 1;
        }
        echo json_encode([
            'success'     => true,
            'page_size'   => $page_size,
            'total_pages' => $total_pages,
            'next_page'   => 1,
        ]);
    }

    // CẤU TRÚC ĐỒNG BỘ
    public function sync_page()
    {
        header('Content-Type: application/json');
        $page_number = (int)$this->input->post('page');
        if ($page_number <= 0) $page_number = 1;
        $page_size = 1000;
        $allowedFilters = ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'];
        $params = ['page_number' => $page_number, 'page_size' => $page_size];
        foreach ($allowedFilters as $f) {
            $v = $this->input->post($f);
            if ($v !== null && $v !== '') $params[$f] = $v;
        }
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
        $batch = [];
        if (isset($apiResponse['data']['orders']) && is_array($apiResponse['data']['orders'])) {
            $batch = $apiResponse['data']['orders'];
        } elseif (is_array($apiResponse['data']) && !isset($apiResponse['data']['total_pages'])) {
            $batch = $apiResponse['data'];
        }

        $processed_count = is_array($batch) ? count($batch) : 0;
        $rows_ok = 0;
        $rows_err = 0;
        $errs = [];
        if ($processed_count > 0) {
            $syncResult = $this->pancake_orders_model->sync_orders($batch);
            $rows_ok = (int)($syncResult['ok'] ?? 0);
            $rows_err = (int)($syncResult['err'] ?? 0);
            if (!empty($syncResult['errors'])) {
                $errs = array_slice($syncResult['errors'], 0, 10);
            }
        }

        echo json_encode([
            'status'          => 'complete',
            'processed_count' => $processed_count,
            'rows_ok'         => $rows_ok,
            'rows_err'        => $rows_err,
            'errors'          => $errs,
            'page'            => $page_number,
            'page_size'       => $page_size,
            'message'         => "Đồng bộ trang {$page_number} hoàn tất."
        ]);
    }

    // LẤY THÔNG TIN ĐƠN HÀNG
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

    // ĐỒNG BỘ 1000 ĐƠN GẦN NHẤT
    public function sync_recent_1000()
    {
        header('Content-Type: application/json');

        // Mục tiêu và cấu hình phân trang
        $target     = 1000;                // số đơn cần sync
        $page_size  = min(1000, $target);  // cố gắng lấy gọn 1 lần
        $page       = 1;
        $fetched    = 0;

        $total_processed = 0;
        $total_ok  = 0;
        $total_err = 0;
        $errors    = [];

        // Nếu muốn lọc thêm (tuỳ chọn): lấy từ POST/GET (ví dụ startDateTime/endDateTime)
        $allowedFilters = ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'];
        $baseParams = ['page_size' => $page_size];
        foreach ($allowedFilters as $f) {
            $v = $this->input->get_post($f);
            if ($v !== null && $v !== '') $baseParams[$f] = $v;
        }

        // Lặp đến khi đủ 1000 hoặc hết dữ liệu
        while ($fetched < $target) {
            $params = $baseParams;
            $params['page_number'] = $page;

            // Gọi API (dựa trên hàm có sẵn của bạn)
            $api = $this->getOrdersFromApi($params);
            if (!$api['success']) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => "API error at page {$page}: " . ($api['message'] ?? 'unknown'),
                ]);
                return;
            }

            // Chuẩn hoá batch theo cấu trúc trả về
            $batch = [];
            if (isset($api['data']['orders']) && is_array($api['data']['orders'])) {
                $batch = $api['data']['orders'];
            } elseif (is_array($api['data']) && !isset($api['data']['total_pages'])) {
                $batch = $api['data'];
            }

            $count = is_array($batch) ? count($batch) : 0;
            if ($count === 0) {
                // Hết dữ liệu
                break;
            }

            // Cắt bớt nếu vượt quá target
            $need  = $target - $fetched;
            if ($count > $need) {
                $batch = array_slice($batch, 0, $need);
                $count = $need;
            }

            // Ghi DB bằng model sync_orders()
            $sync = $this->pancake_orders_model->sync_orders($batch);

            $total_processed += $count;
            $total_ok  += (int)($sync['ok']  ?? 0);
            $total_err += (int)($sync['err'] ?? 0);
            if (!empty($sync['errors'])) {
                // gom tối đa 50 lỗi để trả về (đủ soi nguyên nhân)
                foreach ($sync['errors'] as $e) {
                    if (count($errors) >= 50) break;
                    $errors[] = $e;
                }
            }

            $fetched += $count;

            // Nếu API trả ít hơn page_size → khả năng hết trang
            if ($count < $page_size) break;

            $page++;
        }

        echo json_encode([
            'status'           => 'complete',
            'requested'        => $target,
            'processed_count'  => $total_processed,
            'rows_ok'          => $total_ok,
            'rows_err'         => $total_err,
            'errors'           => $errors,
            'pages_called'     => $page,
            'message'          => "Đồng bộ 1000 đơn gần nhất hoàn tất (đã xử lý {$total_processed} đơn)."
        ]);
    }
}
