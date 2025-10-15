<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_settings extends AdminController
{
    private bool $useDisplayDuration = true; // đẩy thời lượng dạng HH:MM:SS (Text)

    public function __construct()
    {
        parent::__construct();
        $this->load->model('call_sync_model');
        $this->load->database();
        $this->load->helper('security');
    }

    public function index()
    {
        $token = $this->call_sync_model->get_token();
        $lark  = $this->call_sync_model->get_lark_config();

        if (!$token) {
            $token = (object)[
                'base_url'     => '',
                'service_name' => '',
                'auth_user'    => '',
                'auth_key'     => '',
            ];
        }

        if ($lark) {
            $token->lark_auth_endpoint = $lark->auth_endpoint ?? '';
            $token->lark_app_id        = $lark->app_id ?? '';
            $token->lark_app_secret    = $lark->app_secret ?? '';
            $token->bitable_app_token  = $lark->bitable_app_token ?? '';
            $token->bitable_table_id   = $lark->bitable_table_id  ?? '';
        } else {
            $token->lark_auth_endpoint = 'https://open.larksuite.com/open-apis/auth/v3/tenant_access_token/internal/';
            $token->lark_app_id        = '';
            $token->lark_app_secret    = '';
            $token->bitable_app_token  = '';
            $token->bitable_table_id   = '';
        }

        $data['token'] = $token;
        $data['logs']  = $this->call_sync_model->get_logs(10);
        $data['title'] = _l('Cài đặt & Đồng bộ Tổng đài');
        $this->load->view('call_sync/call_settings_view', $data);
    }

    public function save()
    {
        if (!has_permission('call_sync', '', 'edit')) access_denied('call_sync');

        // Cấu hình MBO
        $payload = [
            'base_url'     => $this->input->post('base_url', true),
            'service_name' => $this->input->post('service_name', true),
            'auth_user'    => $this->input->post('auth_user', true),
            'auth_key'     => $this->input->post('auth_key', true),
        ];
        $this->call_sync_model->save_token($payload);

        // Lark + Bitable target: LẤY TOKEN XONG MỚI LƯU
        $larkPayload = [
            'lark_auth_endpoint' => $this->input->post('lark_auth_endpoint', true),
            'lark_app_id'        => $this->input->post('lark_app_id', true),
            'lark_app_secret'    => $this->input->post('lark_app_secret', true),
            'bitable_app_token'  => $this->input->post('bitable_app_token', true),
            'bitable_table_id'   => $this->input->post('bitable_table_id', true),
        ];
        $larkRes = $this->call_sync_model->upsert_lark_and_fetch_token($larkPayload);

        if ($this->input->is_ajax_request()) {
            echo json_encode([
                'success'       => $larkRes['success'],
                'message'       => $larkRes['success']
                    ? 'Đã lưu cấu hình & lấy token Lark thành công.'
                    : ($larkRes['message'] ?? 'Lỗi lấy token Lark'),
                'token_expires' => $larkRes['expires_at'] ?? null,
                'csrf_name'     => $this->security->get_csrf_token_name(),
                'csrf_hash'     => $this->security->get_csrf_hash(),
            ]);
            return;
        }

        if ($larkRes['success']) set_alert('success', _l('Đã lưu cấu hình & lấy token Lark thành công.'));
        else set_alert('danger', _l('Lỗi lấy token Lark: ') . ($larkRes['message'] ?? ''));

        redirect(admin_url('call_sync/call_settings'));
    }

    public function sync_now()
    {
        if (!has_permission('call_sync', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'access_denied']);
            return;
        }

        $opts = [];
        if ($this->input->post('DateStart')) $opts['DateStart'] = $this->input->post('DateStart', true);
        if ($this->input->post('DateEnd'))   $opts['DateEnd']   = $this->input->post('DateEnd', true);
        if ($this->input->post('PageSize'))  $opts['PageSize']  = (int)$this->input->post('PageSize', true);
        if ($this->input->post('maxPages'))  $opts['maxPages']  = (int)$this->input->post('maxPages', true);
        $opts += ['DateStart' => '2025-08-01T00:00:00', 'DateEnd' => '2025-12-31T23:59:59'];

        $res = $this->call_sync_model->sync_range($opts);
        echo json_encode($res);
    }

    public function sync_now_push_lark()
    {
        if (!has_permission('call_sync', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'access_denied']);
            return;
        }

        // Dùng lock để tránh chạy chồng
        $lockName = 'manual_push_lark';
        if (!$this->call_sync_model->acquire_job_lock($lockName, 300)) {
            $ls = $this->call_sync_model->get_job_lock($lockName);
            echo json_encode([
                'success'=>false,
                'message'=>"Đang có tiến trình khác chạy đến {$ls['locked_until']}",
                'lock'   =>$ls
            ]);
            return;
        }

        try {
            $opts = [];
            if ($this->input->post('DateStart')) $opts['DateStart'] = $this->input->post('DateStart', true);
            if ($this->input->post('DateEnd'))   $opts['DateEnd']   = $this->input->post('DateEnd', true);
            if ($this->input->post('PageSize'))  $opts['PageSize']  = (int)$this->input->post('PageSize', true);
            if ($this->input->post('maxPages'))  $opts['maxPages']  = (int)$this->input->post('maxPages', true);
            $opts += ['DateStart' => '2025-08-01T00:00:00', 'DateEnd' => '2025-12-31T23:59:59'];

            // Target Lark
            $appToken = $this->input->post('app_token', true);
            $tableId  = $this->input->post('table_id', true);
            if (!$appToken || !$tableId) {
                // fallback đọc DB
                $t = $this->call_sync_model->get_bitable_target();
                $appToken = $appToken ?: ($t['app_token'] ?? '');
                $tableId  = $tableId  ?: ($t['table_id']  ?? '');
            }
            if (!$appToken || !$tableId) {
                echo json_encode(['success'=>false,'message'=>'Thiếu app_token / table_id']);
                return;
            }

            $res = $this->call_sync_model->sync_range_and_push_to_lark($opts, [
                'app_token'       => $appToken,
                'table_id'        => $tableId,
                'field_map'       => $this->bitable_field_map_current(),
                'field_types'     => $this->useDisplayDuration ? [] : [
                    'Thời gian đàm thoại' => 'number',
                    'Tổng thời gian gọi'  => 'number',
                ],
                'batch_size'      => 120,
                'retry'           => ['times' => 3, 'sleep' => 2],
                'datetime_format' => 'c',
            ]);

            echo json_encode($res);
        } finally {
            $this->call_sync_model->release_job_lock($lockName);
        }
    }

    public function lock_status()
    {
        if (!has_permission('call_sync', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'access_denied']);
            return;
        }
        $locks = $this->call_sync_model->get_locks_status([
            'manual_push_lark',
            'cron_sync_db',
            'cron_sync_lark',
        ]);
        echo json_encode(['success'=>true,'locks'=>$locks]);
    }

    private function bitable_field_map_current(): array
    {
        return [
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
    }
}
