<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // Có thể bỏ comment dòng dưới để kiểm tra quyền nếu cần
        // if (!has_permission('call_sync', '', 'view')) { access_denied('call_sync'); }
    }

    // Trang dashboard -> hiển thị view với dữ liệu giả (nếu controller không truyền dữ liệu, view sẽ dùng fake data)
    public function index()
    {
        $data = [];
        // Nếu muốn truyền dữ liệu thật sau này: $data['calls'] = $this->your_model->get_calls(...);
        $this->load->view('call_sync/call_dashboard', $data);
    }

    // Nếu bạn muốn route tên khác (ví dụ admin/call_sync/call_logs)
    public function call_logs()
    {
        $data = [];
        $this->load->view('call_sync/call_dashboard', $data);
    }
}
