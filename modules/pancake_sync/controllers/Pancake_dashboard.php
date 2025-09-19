<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard extends AdminController
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
        // === PHẦN 1 & 2: LẤY DỮ LIỆU HIỂN THỊ DANH SÁCH ===
        $page     = (int)($this->input->get('page_number') ?: 1);
        $pageSize = (int)($this->input->get('page_size') ?: 30);
        $params = [
            'page_number'     => $page,
            'page_size'       => $pageSize,
            'search'          => $this->input->get('search'),
            'filter_status'   => $this->input->get('filter_status'),
            'include_removed' => $this->input->get('include_removed'),
            'updateStatus'    => $this->input->get('updateStatus')
        ];
        $vietnamTimezone = new DateTimeZone('Asia/Ho_Chi_Minh');
        $startDateInput  = $this->input->get('startDateTime');
        $endDateInput    = $this->input->get('endDateTime');
        $startDate = !empty($startDateInput) ? $startDateInput : (new DateTime('now', $vietnamTimezone))->format('Y-m-d');
        $endDate   = !empty($endDateInput) ? $endDateInput : (new DateTime('now', $vietnamTimezone))->format('Y-m-d');
        $params['startDateTime'] = (new DateTime($startDate . ' 00:00:00', $vietnamTimezone))->getTimestamp();
        $params['endDateTime']   = (new DateTime($endDate . ' 23:59:59', $vietnamTimezone))->getTimestamp();
        $response = $this->getOrdersFromApi($params);
        $data['orders']        = $response['data'] ?? [];
        $data['total']         = isset($response['total']) ? (int)$response['total'] : 0;
        $data['startDateTime'] = $startDate;
        $data['endDateTime']   = $endDate;

        // === PHẦN 3: LẤY SỐ LIỆU TỔNG QUAN ===
        $data['soDonXacNhanHomNay'] = $this->_getTodaysStatus1UpdateCount(); // Gọi hàm mới
        
        $tongDoanhSoHienThi = 0;
        if (!empty($data['orders'])) {
            foreach ($data['orders'] as $order) {
                 $tongDoanhSoHienThi += (float)($order['total_price_after_sub_discount'] ?? 0);
            }
        }
        $data['overviewTotalPriceToday'] = $tongDoanhSoHienThi;

        // === PHẦN 4: LOGIC PHÂN TRANG (giữ nguyên) ===
        if ($data['total'] > 0 && $pageSize > 0 && ceil($data['total'] / $pageSize) > 1) {
            $this->load->library('pagination');
            $config['base_url'] = admin_url('pancake_dashboard');
            $config['total_rows'] = $data['total'];
            $config['per_page'] = $pageSize;
            $config['page_query_string'] = TRUE;
            $config['query_string_segment'] = 'page_number';
            $config['reuse_query_string'] = TRUE;
            $config['use_page_numbers'] = TRUE;
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
        $data['current_page'] = $page;
        $data['total_pages']  = ($pageSize > 0 && $data['total'] > 0) ? ceil($data['total'] / $pageSize) : 0;

        $this->load->view('pancake_sync/dashboard', $data);
    }

    /**
     * Lấy số lượng đơn hàng được cập nhật trong hôm nay VÀ có trạng thái hiện tại là 1.
     * @return int
     */
    private function _getTodaysStatus1UpdateCount(): int
    {
        $vietnamTimezone = new DateTimeZone('Asia/Ho_Chi_Minh');
        
        $params = [
            'page_size'     => 1,
            'filter_status' => [1], // Chỉ lấy đơn có trạng thái là 1
            'startDateTime' => (new DateTime('today 00:00:00', $vietnamTimezone))->getTimestamp(),
            'endDateTime'   => (new DateTime('today 23:59:59', $vietnamTimezone))->getTimestamp(),
            'updateStatus'  => 'updated_at' // Lọc theo ngày cập nhật
        ];

        $response = $this->getOrdersFromApi($params);
        return isset($response['total']) ? (int)$response['total'] : 0;
    }

    private function getOrdersFromApi(array $params = []): array
    {
        $queryParams = [
            'api_key'     => $this->apiKey,
            'page_number' => $params['page_number'] ?? 1,
            'page_size'   => $params['page_size'] ?? 30
        ];

        $filters = ['search', 'filter_status', 'include_removed', 'startDateTime', 'endDateTime', 'updateStatus'];
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
        curl_close($ch);

        $jsonData = json_decode($response, true);
        return [
            'success' => $jsonData['success'] ?? false,
            'message' => $jsonData['message'] ?? 'OK',
            'data'    => $jsonData['data'] ?? [],
            'total'   => isset($jsonData['total_entries']) ? (int)$jsonData['total_entries'] : 0
        ];
    }
}