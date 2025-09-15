<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pancake_products_model extends CI_Model
{
    private $table = 'pancake_products';

    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();

        if (!$this->db->table_exists($this->table)) {
            $this->create_table();
        }
    }

    private function create_table()
    {
        $fields = [
            'stt' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'barcode' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'product_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'product_images' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tags' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'categories' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total_quantity' => [
                'type' => 'INT',
                'null' => true,
            ],
            'retail_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'retail_price_after_discount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'total_purchase_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'remain_quantity' => [
                'type' => 'INT',
                'null' => true,
            ],
            // ✅ Thêm is_locked
            'is_locked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0, // 0 = false, 1 = true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('stt', true); // primary key
        $this->dbforge->add_key('barcode', true); // unique key
        $this->dbforge->create_table($this->table, true);
    }

    /**
     * Đồng bộ dữ liệu từ API
     */
    public function sync_products(array $products)
    {
        $count = 0;
        foreach ($products as $p) {
            $barcode = $p['barcode'] ?? null;
            if (empty($barcode)) {
                continue; // không có barcode thì bỏ qua
            }

            $tags = [];
            if (!empty($p['product']['tags'])) {
                foreach ($p['product']['tags'] as $tag) {
                    $tags[] = $tag['note'] ?? '';
                }
            }

            $categories = [];
            if (!empty($p['product']['categories'])) {
                foreach ($p['product']['categories'] as $category) {
                    $categories[] = $category['name'] ?? '';
                }
            }

            $data = [
                'barcode' => $barcode,
                'product_name' => $p['product']['name'] ?? null,
                'product_images' => !empty($p['images'][0]) ? $p['images'][0] : null,
                'tags' => !empty($tags) ? implode(', ', $tags) : null,
                'categories' => !empty($categories) ? implode(', ', $categories) : null,
                'total_quantity' => $p['variations_warehouses'][0]['total_quantity'] ?? null,
                'retail_price' => $p['retail_price'] ?? null,
                'retail_price_after_discount' => $p['retail_price_after_discount'] ?? null,
                'total_purchase_price' => $p['total_purchase_price'] ?? null,
                'remain_quantity' => $p['remain_quantity'] ?? null,
                'is_locked' => isset($p['product']['is_locked']) ? (int)$p['product']['is_locked'] : 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ];


            // Nếu barcode tồn tại thì bỏ qua -> không insert, không update
            $exists = $this->db->get_where($this->table, ['barcode' => $barcode])->row();
            if (!$exists) {
                $data['created_at'] = date('Y-m-d H:i');
                $this->db->insert($this->table, $data);
                $count++;
            }
        }
        return $count;
    }
}
