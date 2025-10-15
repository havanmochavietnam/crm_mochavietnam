<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_cron extends CI_Controller
{
    // Đặt secret cho HTTP cron (không bắt buộc khi chạy CLI hay từ localhost)
    private $httpSecret = 'CHANGE_ME_CRON_SECRET';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('call_sync_model');
        $this->load->database();
        date_default_timezone_set(config_item('timezone') ?: 'Asia/Ho_Chi_Minh');
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _allowed(): bool
    {
        if (is_cli()) return true;

        // Cho phép localhost không cần key
        $ip = $this->input->ip_address();
        if ($ip === '127.0.0.1' || $ip === '::1') return true;

        // Hoặc phải có ?key=
        $key = $this->input->get('key', true);
        return ($key && $key === $this->httpSecret);
    }

    private function _json($arr, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    private function _build_window_opts(int $minutes, int $pageSize = 200, int $maxPages = 0): array
    {
        $end   = time();
        $start = $end - max(1, $minutes) * 60;

        return [
            'DateStart' => date('Y-m-d\T00:00:00', $start), // có thể đổi thành H:i:s nếu muốn chính xác đến phút
            'DateEnd'   => date('Y-m-d\T23:59:59', $end),
            'PageSize'  => $pageSize,
            'maxPages'  => $maxPages,
            'PageIndex' => 1,
        ];
    }

    /**
     * B1: Chỉ làm mới tenant token (ghi log 'lark_token')
     * CLI:    php index.php call_cron/refresh_token
     * HTTP:   /index.php/call_cron/refresh_token?key=...
     */
    public function refresh_token()
    {
        if (!$this->_allowed()) return $this->_json(['success'=>false,'message'=>'forbidden'], 403);

        $job = 'cron_refresh_token';
        if (!$this->call_sync_model->acquire_job_lock($job, 60)) {
            return $this->_json(['success'=>false, 'message'=>'job_locked']);
        }

        try {
            $res = $this->call_sync_model->ensure_lark_token_logged(false);
            return $this->_json($res);
        } finally {
            $this->call_sync_model->release_job_lock($job);
        }
    }

    /**
     * B2: Chỉ đồng bộ DB (không đẩy Lark).
     * CLI:  php index.php call_cron/sync_db [minutes]
     * HTTP: /index.php/call_cron/sync_db/10?key=...
     */
    public function sync_db($minutes = 10)
    {
        if (!$this->_allowed()) return $this->_json(['success'=>false,'message'=>'forbidden'], 403);

        $job = 'cron_sync_db';
        if (!$this->call_sync_model->acquire_job_lock($job, 300)) {
            return $this->_json(['success'=>false, 'message'=>'job_locked']);
        }

        try {
            // Đảm bảo token Lark sẵn sàng (nếu bạn muốn strict theo flow “token trước”)
            $this->call_sync_model->ensure_lark_token_logged(false);

            $opts = $this->_build_window_opts((int)$minutes, 200, 0);
            $res  = $this->call_sync_model->sync_range($opts, 'cron');
            return $this->_json($res);
        } finally {
            $this->call_sync_model->release_job_lock($job);
        }
    }

    /**
     * B3: Đồng bộ DB + đẩy Lark (nếu có bitable target trong DB).
     * CLI:  php index.php call_cron/run [minutes]
     * HTTP: /index.php/call_cron/run/10?key=...
     */
    public function run($minutes = 10)
    {
        if (!$this->_allowed()) return $this->_json(['success'=>false,'message'=>'forbidden'], 403);

        $job = 'cron_sync_pipeline';
        if (!$this->call_sync_model->acquire_job_lock($job, max(300, (int)$minutes*60))) {
            return $this->_json(['success'=>false, 'message'=>'job_locked']);
        }

        try {
            // B1) Token trước
            $tok = $this->call_sync_model->ensure_lark_token_logged(false);
            if (!$tok['success']) {
                // vẫn tiếp tục sync DB để lưu lịch sử (tuỳ business, có thể return luôn)
                $this->call_sync_model->log_event('cron', 'failed', 0, 'Cannot ensure Lark token: '.$tok['message']);
            }

            // B2) Nếu chưa cấu hình Bitable target => chỉ sync DB
            $target = $this->call_sync_model->get_bitable_target();
            $opts   = $this->_build_window_opts((int)$minutes, 200, 0);

            if (empty($target['app_token']) || empty($target['table_id'])) {
                $res = $this->call_sync_model->sync_range($opts, 'cron');
                $res['note'] = 'Bitable target not set -> synced DB only';
                return $this->_json($res);
            }

            // B3) Có target -> sync + push Lark
            $fieldMap = [
                'Thời gian'           => 'call_date',
                'Ghi âm'              => 'link_file',
                'Hướng cuộc gọi'      => 'type_call',
                'Tên số gọi'          => 'caller_name',
                'Số gọi'              => 'caller_number',
                'Đầu số tổng đài'     => 'head_number',
                'Nhóm nhận'           => 'gsm_port',
                'Số nhận'             => 'receive_number',
                'Trạng thái'          => 'status',
                'Mã cuộc gọi'         => 'call_key',
                'Thời gian đàm thoại' => 'real_call_time',
                'Tổng thời gian gọi'  => 'total_call_time',
            ];
            $fieldTypes = []; // để hiển thị HH:MM:SS đã hậu xử lý thành text

            $res = $this->call_sync_model->sync_range_and_push_to_lark($opts, [
                'app_token'       => $target['app_token'],
                'table_id'        => $target['table_id'],
                'field_map'       => $fieldMap,
                'field_types'     => $fieldTypes,
                'batch_size'      => 120,
                'retry'           => ['times'=>3,'sleep'=>2],
                'datetime_format' => 'c',
            ], 'cron');

            return $this->_json($res);
        } finally {
            $this->call_sync_model->release_job_lock($job);
        }
    }

    /** Trang hướng dẫn nhanh (optional) */
    public function index()
    {
        $msg = [
            'usage' => [
                'CLI' => [
                    'refresh_token' => 'php index.php call_cron/refresh_token',
                    'sync_db'       => 'php index.php call_cron/sync_db 10',
                    'run'           => 'php index.php call_cron/run 10',
                ],
                'HTTP' => [
                    'refresh_token' => '/index.php/call_cron/refresh_token?key=YOUR_KEY',
                    'sync_db'       => '/index.php/call_cron/sync_db/10?key=YOUR_KEY',
                    'run'           => '/index.php/call_cron/run/10?key=YOUR_KEY',
                ],
            ],
        ];
        return $this->_json($msg);
    }
}
