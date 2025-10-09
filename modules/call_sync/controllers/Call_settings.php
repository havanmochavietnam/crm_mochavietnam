<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_settings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('call_sync_model');
        $this->load->database();
    }

    public function index()
    {
        $data['token'] = $this->call_sync_model->get_token();
        $data['logs']  = $this->call_sync_model->get_logs(10);
        $data['title'] = _l('Cài đặt & Đồng bộ Tổng đài');
        $this->load->view('call_sync/call_settings_view', $data);
    }

    public function save()
    {
        if (!has_permission('call_sync', '', 'edit')) {
            access_denied('call_sync');
        }

        $payload = [
            'base_url'     => $this->input->post('base_url', true),
            'service_name' => $this->input->post('service_name', true),
            'auth_user'    => $this->input->post('auth_user', true),
            'auth_key'     => $this->input->post('auth_key', true),
        ];

        $this->call_sync_model->save_token($payload);

        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            set_alert('success', _l('Đã lưu thông tin token thành công.'));
            redirect(admin_url('call_sync/call_settings'));
        }
    }

    public function sync_now()
    {
        if (!has_permission('call_sync', '', 'view')) {
            echo json_encode(['success' => false, 'message' => 'access_denied']);
            exit;
        }

        $opts = [];
        if ($this->input->post('DateStart')) $opts['DateStart'] = $this->input->post('DateStart', true);
        if ($this->input->post('DateEnd'))   $opts['DateEnd']   = $this->input->post('DateEnd', true);
        if ($this->input->post('PageSize'))  $opts['PageSize']  = (int)$this->input->post('PageSize', true);
        if ($this->input->post('maxPages'))  $opts['maxPages']  = (int)$this->input->post('maxPages', true);

        $res = $this->call_sync_model->sync_range([
            'DateStart' => '2025-08-01T00:00:00',
            'DateEnd'   => '2025-12-31T23:59:59'
        ]);

        echo json_encode($res);
        exit;
    }
}
