<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_dashboard extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * TRANG DASHBOARD (Mặc định cho controller này)
     * URL: /admin/pancake_dashboard
     */
    public function index()
    {
        $data['title'] = _l('pancake_sync_dashboard');
        $this->load->view('pancake_sync/dashboard', $data);
    }
}