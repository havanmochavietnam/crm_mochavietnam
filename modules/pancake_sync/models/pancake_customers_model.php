<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_customers_model extends CI_Model
{
    private $table = 'pancake_customers';

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();

        if (!$this->db->table_exists($this->table)) {
            $this->create_table();
        }
    }

    // TẠO BẢNG
    private function create_table()
    {
        $fields = [
            'customerId' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => FALSE],
            'create_date' => ['type' => 'DATETIME', 'null' => TRUE],
            'create_month' => ['type' => 'DATETIME', 'null' => TRUE],
            'customer_name' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'date_of_birth' => ['type' => 'DATETIME', 'null' => TRUE],
            'gender' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'phone_number' => ['type' => 'TEXT', 'null' => TRUE],
            'address' => ['type' => 'TEXT', 'null' => TRUE],
            'level_customer' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'reward_point' => ['type' => 'INT', 'null' => TRUE, 'default' => 0],
            'purchased_amount' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => TRUE, 'default' => 0.00],
            'order_count' => ['type' => 'INT', 'null' => TRUE, 'default' => 0],
            'succeed_order_count' => ['type' => 'INT', 'null' => TRUE, 'default' => 0],
            'last_order_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'tags' => ['type' => 'TEXT', 'null' => TRUE],
            'emails' => ['type' => 'TEXT', 'null' => TRUE],
            'first_order_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'FB_Id' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'note' => ['type' => 'TEXT', 'null' => TRUE],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('customerId', TRUE);
        $this->dbforge->create_table($this->table, TRUE);
    }

    // ĐỒNG BỘ VỀ API
    public function sync_customers(array $customers)
    {
        if (empty($customers)) {
            return ['inserted' => 0, 'updated' => 0];
        }

        $api_customer_ids = array_filter(array_column($customers, 'customer_id'));
        if (empty($api_customer_ids)) {
            return ['inserted' => 0, 'updated' => 0];
        }

        $existing_customer_ids = array_column(
            $this->db->select('customerId')->where_in('customerId', $api_customer_ids)->get($this->table)->result_array(),
            'customerId'
        );

        $to_insert = [];
        $to_update = [];

        foreach ($customers as $customer) {
            $customerId = $customer['customer_id'] ?? null;
            if (empty($customerId)) continue;

            $data = [
                'create_date'         => isset($customer['inserted_at']) ? date('Y-m-d', strtotime($customer['inserted_at'])) : null,
                'create_month'        => isset($customer['inserted_at']) ? date('m-d', strtotime($customer['inserted_at'])) : null,
                'customer_name'       => $customer['name'] ?? null,
                'date_of_birth'       => isset($customer['date_of_birth']) ? date('Y-m-d', strtotime($customer['date_of_birth'])) : null,
                'gender'              => $customer['gender'] ?? null,
                'phone_number'        => !empty($customer['phone_numbers']) ? json_encode($customer['phone_numbers']) : null,
                'address'             => $customer['shop_customer_addresses'][0]['full_address'] ?? null,
                'level_customer'      => $customer['level']['name'] ?? null,
                'reward_point'        => $customer['reward_point'] ?? 0,
                'purchased_amount'    => $customer['purchased_amount'] ?? 0.00,
                'order_count'         => $customer['order_count'] ?? 0,
                'succeed_order_count' => $customer['succeed_order_count'] ?? 0,
                'last_order_at'       => isset($customer['last_order_at']) ? date('Y-m-d H:i:s', strtotime($customer['last_order_at'])) : null,
                'tags'                => !empty($customer['tags']) ? json_encode($customer['tags']) : null,
                'emails'              => !empty($customer['emails']) ? json_encode($customer['emails']) : null,
                'first_order_at'      => isset($customer['first_order_at']) ? date('Y-m-d', strtotime($customer['first_order_at'])) : null,
                'FB_Id'               => $customer['fb_id'] ?? null,
                'note'                => $customer['notes'][0]['message'] ?? null,
            ];

            if (in_array($customerId, $existing_customer_ids)) {
                $data['customerId'] = $customerId;
                $to_update[] = $data;
            } else {
                $data['customerId'] = $customerId;
                $to_insert[] = $data;
            }
        }

        if (!empty($to_insert)) {
            $this->db->insert_batch($this->table, $to_insert);
        }
        if (!empty($to_update)) {
            $this->db->update_batch($this->table, $to_update, 'customerId');
        }

        return ['inserted' => count($to_insert), 'updated' => count($to_update)];
    }
}