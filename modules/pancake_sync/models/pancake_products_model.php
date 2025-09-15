<?php
defined('BASEPATH') or exit('No direct script access allowed');

class pancake_products_model extends App_model
{
    private $table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'pancake_products';
    }

    /**
     * Lấy sản phẩm từ cơ sở dữ liệu local
     * @param mixed $id ID sản phẩm (nếu có)
     * @param array $filters Bộ lọc (ví dụ: ['search' => 'từ khóa'])
     * @param int $page Trang hiện tại (cho phân trang)
     * @param int $pageSize Số lượng mục mỗi trang
     * @return mixed
     */
    public function get($id = '', $filters = [], $page = 1, $pageSize = 30)
    {
        // Áp dụng bộ lọc tìm kiếm
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('sku', $search);
            $this->db->group_end();
        }
        
        // Lấy một sản phẩm cụ thể bằng ID
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row(); // Trả về một object
        }

        // Cài đặt phân trang
        $offset = ($page - 1) * $pageSize;
        $this->db->limit($pageSize, $offset);
        $this->db->order_by('id', 'desc');

        // Lấy danh sách sản phẩm
        return $this->db->get($this->table)->result_array(); // Trả về mảng các sản phẩm
    }
    
    /**
     * Thêm một sản phẩm mới vào DB
     * @param array $data Dữ liệu sản phẩm
     * @return mixed ID của sản phẩm vừa thêm hoặc false nếu thất bại
     */
    public function add($data)
    {
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        return $insert_id ? $insert_id : false;
    }

    /**
     * Cập nhật thông tin sản phẩm
     * @param int $id ID sản phẩm cần cập nhật
     * @param array $data Dữ liệu mới
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Xóa một sản phẩm
     * @param int $id ID sản phẩm cần xóa
     * @return bool
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Đếm tổng số sản phẩm, có áp dụng bộ lọc
     * @param array $filters Bộ lọc (ví dụ: ['search' => 'từ khóa'])
     * @return int
     */
    public function count_all($filters = [])
    {
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('sku', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }
    
    /**
     * Đồng bộ (thêm mới hoặc cập nhật) danh sách sản phẩm từ Pancake API
     * @param array $products Mảng sản phẩm từ API
     * @return int Số lượng sản phẩm đã được đồng bộ
     */
    public function sync_products($products)
    {
        if (empty($products)) {
            return 0;
        }

        $synced_count = 0;
        foreach ($products as $product_variation) {
            $sku = $product_variation['barcode'];
            if (empty($sku)) {
                continue;
            }

            // Chuẩn bị dữ liệu đầy đủ từ API để lưu vào DB
            $data_to_save = [
                'pancake_product_id'    => $product_variation['product']['id'] ?? null,
                'name'                  => $product_variation['product']['name'] ?? 'N/A',
                'image_url'             => !empty($product_variation['images']) ? $product_variation['images'][0] : null,
                'tags'                  => !empty($product_variation['product']['tags']) ? json_encode($product_variation['product']['tags']) : null,
                'categories'            => !empty($product_variation['product']['categories']) ? json_encode($product_variation['product']['categories']) : null,
                'total_imported'        => $product_variation['variations_warehouses'][0]['total_quantity'] ?? 0,
                'price'                 => $product_variation['retail_price'] ?? 0,
                'discounted_price'      => $product_variation['retail_price_after_discount'] ?? 0,
                'total_value_remaining' => $product_variation['total_purchase_price'] ?? 0,
                'quantity_available'    => $product_variation['remain_quantity'] ?? 0,
                'raw_data'              => json_encode($product_variation)
            ];

            // Kiểm tra xem SKU đã tồn tại trong DB chưa
            $this->db->where('sku', $sku);
            $existing_product = $this->db->get($this->table)->row();

            // Quyết định UPDATE (cập nhật) hoặc INSERT (thêm mới)
            if ($existing_product) {
                $this->db->where('sku', $sku);
                $this->db->update($this->table, $data_to_save);
            } else {
                $data_to_save['sku'] = $sku;
                $this->db->insert($this->table, $data_to_save);
            }
            $synced_count++;
        }
        return $synced_count;
    }
}