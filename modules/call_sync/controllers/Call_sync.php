<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_sync extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('call_sync_model');
        $this->load->database();
        $this->load->library('pagination');
    }

    /**
     * Trang index hiển thị history từ bảng call_history
     */
    public function index()
    {
        // Safe input retrieval: coalesce to empty string and cast to string before trim
        $q          = trim((string)($this->input->get('q', true) ?? ''));
        $status     = (string)($this->input->get('status', true) ?? '');
        $start_date = (string)($this->input->get('start_date', true) ?? '');
        $end_date   = (string)($this->input->get('end_date', true) ?? '');
        $page_size  = (int)($this->input->get('page_size') ?? 30);
        $page_number= (int)($this->input->get('page_number') ?: 1);

        // Table name
        $table = db_prefix() . 'call_history';

        // Build query cache so we can count and then fetch
        $this->db->start_cache();
        $this->db->from($table);

        // search q: phone, name, link_file, status
        if ($q !== '') {
            $this->db->group_start();
            $this->db->like('caller_number', $q);
            $this->db->or_like('receive_number', $q);
            $this->db->or_like('caller_name', $q);
            $this->db->or_like('call_key', $q);
            $this->db->or_like('raw_response', $q);
            $this->db->group_end();
        }

        if ($status !== '') {
            $this->db->where('status', $status);
        }

        // date filters (expecting YYYY-MM-DD)
        if ($start_date !== '') {
            // treat as midnight start
            $this->db->where('call_date >=', $start_date . ' 00:00:00');
        }
        if ($end_date !== '') {
            $this->db->where('call_date <=', $end_date . ' 23:59:59');
        }

        $this->db->stop_cache();

        // Count total
        $total = (int)$this->db->count_all_results();

        // Pagination calculations
        $page_size = max(1, $page_size);
        $page_number = max(1, $page_number);
        $offset = ($page_number - 1) * $page_size;

        // Fetch page
        $this->db->order_by('call_date', 'DESC');
        $this->db->limit($page_size, $offset);
        $query = $this->db->get();
        $calls = $query->result_array();

        // flush cache to avoid affecting other queries
        $this->db->flush_cache();

        // prepare pagination html
        $pagination = $this->setupPagination($total, $page_size);

        $data = [
            'title' => _l('Nhật ký cuộc gọi'),
            'calls' => $calls,
            'total' => $total,
            'pagination' => $pagination,
        ];

        $this->load->view('call_sync/call_sync_view', $data);
    }

    /**
     * Helper: init pagination (uses page_number query string)
     */
    private function setupPagination(int $totalRows, int $pageSize): string
    {
        $config = [
            'base_url'             => admin_url('call_sync'),
            'total_rows'           => $totalRows,
            'per_page'             => $pageSize,
            'page_query_string'    => true,
            'query_string_segment' => 'page_number',
            'use_page_numbers'     => true,
            'reuse_query_string'   => true,

            // HTML for bootstrap style
            'full_tag_open'        => '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">',
            'full_tag_close'       => '</ul></nav>',

            'first_link'           => '&laquo;',
            'first_tag_open'       => '<li class="page-item">',
            'first_tag_close'      => '</li>',

            'last_link'            => '&raquo;',
            'last_tag_open'        => '<li class="page-item">',
            'last_tag_close'       => '</li>',

            'next_link'            => '&gt;',
            'next_tag_open'        => '<li class="page-item">',
            'next_tag_close'       => '</li>',

            'prev_link'            => '&lt;',
            'prev_tag_open'        => '<li class="page-item">',
            'prev_tag_close'       => '</li>',

            'cur_tag_open'         => '<li class="page-item active" aria-current="page"><span class="page-link">',
            'cur_tag_close'        => '</span></li>',

            'num_tag_open'         => '<li class="page-item">',
            'num_tag_close'        => '</li>',

            'attributes'           => ['class' => 'page-link'],
        ];

        $this->pagination->initialize($config);
        return $this->pagination->create_links();
    }
}
