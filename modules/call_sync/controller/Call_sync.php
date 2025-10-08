<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_sync extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('pagination');
    }

    public function index()
    {
        $data = [
            'title'        => 'Nhật ký cuộc gọi',
            'q'            => trim($this->input->get('q', true) ?? ''),
            'start_date'   => $this->input->get('start_date', true),
            'end_date'     => $this->input->get('end_date', true),
            'direction'    => $this->input->get('direction', true),
            'status'       => $this->input->get('status', true),
            'page_number'  => (int)($this->input->get('page_number') ?: 1),
            'page_size'    => (int)($this->input->get('page_size') ?: 30),

            'calls'        => [],
            'total'        => 0,
            'pagination'   => '',
        ];


        $this->load->view('call_sync/call_view', $data);
    }

    public function sync()
    {
        set_alert('success', 'Đã chạy đồng bộ (demo).');
        redirect(admin_url('call_sync/call_view'));
    }
}
