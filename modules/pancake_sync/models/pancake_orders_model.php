<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Đồng bộ một mảng đơn hàng vào cơ sở dữ liệu.
     * Kiểm tra nếu đơn hàng đã tồn tại thì cập nhật, nếu chưa thì thêm mới.
     * @param array $orders Mảng các đơn hàng từ API Pancake
     * @return int Số lượng dòng bị ảnh hưởng (thêm mới hoặc cập nhật)
     */
    public function sync_orders($orders)
    {
        if (empty($orders)) {
            return 0;
        }

        $rows_affected = 0;
        foreach ($orders as $order) {
            $order_data = [
                'pancake_order_id' => $order['id'],
                'customer_name'    => $order['shipping_address']['full_name'] ?? null,
                'customer_phone'   => $order['shipping_address']['phone_number'] ?? null,
                'status_name'      => $order['status_name'] ?? null,
                'cod'              => $order['cod'] ?? 0,
                'created_at'       => isset($order['inserted_at']) ? date('Y-m-d H:i:s', strtotime($order['inserted_at'])) : null,
                'data'             => json_encode($order), // Mã hóa toàn bộ dữ liệu đơn hàng vào cột data
            ];

            $this->db->where('pancake_order_id', $order['id']);
            $existing_order = $this->db->get(db_prefix() . 'pancake_orders')->row();

            if ($existing_order) {
                // Nếu đã tồn tại -> Cập nhật
                $this->db->where('pancake_order_id', $order['id']);
                $this->db->update(db_prefix() . 'pancake_orders', $order_data);
                if ($this->db->affected_rows() > 0) {
                    $rows_affected++;
                }
            } else {
                // Nếu chưa tồn tại -> Thêm mới
                $this->db->insert(db_prefix() . 'pancake_orders', $order_data);
                $rows_affected++;
            }
        }
        return $rows_affected;
    }

    /**
     * Lấy danh sách các người bán hàng (sellers) duy nhất từ các đơn hàng đã lưu.
     * @return array Danh sách sellers
     */
    public function get_sellers_from_orders()
    {
        $this->db->select("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) as seller_id");
        $this->db->select("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.name')) as seller_name");
        $this->db->from(db_prefix() . 'pancake_orders');
        $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) IS NOT NULL");

        // SỬA DÒNG NÀY: Thêm 'seller_name' vào group_by
        $this->db->group_by(['seller_id', 'seller_name']);

        $this->db->order_by('seller_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Lấy danh sách đơn hàng từ cơ sở dữ liệu với các bộ lọc và phân trang.
     * @param array $filters Các bộ lọc (ngày, tìm kiếm, trạng thái, người bán, trang...)
     * @return array Dữ liệu đơn hàng và tổng số lượng
     */
    public function get_orders_from_db($filters = [])
    {
        if (!empty($filters['startDateTime'])) {
            $start_date = date('Y-m-d H:i:s', strtotime($filters['startDateTime']));
            $this->db->where('created_at >=', $start_date);
        }

        if (!empty($filters['endDateTime'])) {
            $end_date = date('Y-m-d H:i:s', strtotime($filters['endDateTime'] . ' 23:59:59'));
            $this->db->where('created_at <=', $end_date);
        }

        if (!empty($filters['search'])) {
            $this->db->group_start();
            $this->db->like('customer_name', $filters['search']);
            $this->db->or_like('customer_phone', $filters['search']);
            $this->db->or_like('data', $filters['search']);
            $this->db->group_end();
        }

        if (!empty($filters['filter_status'])) {
            $this->db->where('status_name', $filters['filter_status']);
        }

        if (!empty($filters['filter_sellers']) && is_array($filters['filter_sellers'])) {
            $seller_ids = array_filter($filters['filter_sellers']);
            if (!empty($seller_ids)) {
                // Đảm bảo các ID là số để tránh SQL Injection
                $sanitized_ids = array_map('intval', $seller_ids);
                $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) IN (" . implode(',', $sanitized_ids) . ")");
            }
        }

        // Đếm tổng số dòng trước khi thêm limit để phân trang
        $total_rows = $this->db->count_all_results(db_prefix() . 'pancake_orders', false);

        // Phân trang
        $page = $filters['page_number'] ?? 1;
        $pageSize = $filters['page_size'] ?? 30;
        $offset = ($page - 1) * $pageSize;
        $this->db->limit($pageSize, $offset);

        $this->db->order_by('created_at', 'DESC');

        $orders_from_db = $this->db->get()->result_array();

        // Decode cột 'data' từ JSON thành mảng PHP
        $orders_formatted = [];
        foreach ($orders_from_db as $order_row) {
            $orders_formatted[] = json_decode($order_row['data'], true);
        }
        

        return [
            'data' => $orders_formatted,
            'total' => $total_rows
        ];
    }

    // Doanh thu
    /**
     * [KHOẢNG NGÀY] Tính tổng doanh thu của các đơn được xác nhận.
     */
    public function get_revenue_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
            SELECT SUM(t.revenue) as total_revenue
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_price_after_sub_discount') as revenue
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * [KHOẢNG NGÀY] Tính tổng doanh số của các đơn được xác nhận.
     */
    public function get_sales_volume_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
            SELECT SUM(t.sales_volume) as total_sales_volume
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_price') as sales_volume
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_sales_volume : 0;
    }

    /**
     * [KHOẢNG NGÀY] Đếm số đơn được xác nhận.
     */
    public function count_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
            SELECT COUNT(DISTINCT po.id) as total
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
        ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }


    /**
     * [KHOẢNG NGÀY] Tính tổng số lượng sản phẩm bán ra.
     */
    public function get_product_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
            SELECT SUM(t.product_quantity) as total_products
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }


    /**
     * [KHOẢNG NGÀY] Đếm tổng số đơn hàng được TẠO MỚI.
     */
    public function count_orders_created_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $this->db->where("DATE(DATE_ADD(created_at, INTERVAL 7 HOUR)) >=", $start_date);
        $this->db->where("DATE(DATE_ADD(created_at, INTERVAL 7 HOUR)) <=", $end_date);

        return $this->db->count_all_results(db_prefix() . 'pancake_orders');
    }

    /**
     * [HÔM NAY] Đếm số đơn được TẠO MỚI trong ngày, có thể lọc theo trạng thái.
     * Nếu không truyền $status, hàm sẽ đếm tất cả đơn mới.
     * @param string|null $status Tên trạng thái cần đếm (vd: 'canceled', 'removed').
     */
    public function count_orders_by_status_today($status = null)
    {
        $this->db->where("DATE(DATE_ADD(created_at, INTERVAL 7 HOUR)) =", date('Y-m-d'));

        if (!empty($status)) {
            $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.status_name')) =", $status);
        }

        return $this->db->count_all_results(db_prefix() . 'pancake_orders');
    }


    /**
     * [HÔM NAY] Đếm số đơn được xác nhận trong ngày.
     */
    public function count_orders_confirmed_today()
    {
        $sql = "
            SELECT COUNT(DISTINCT po.id) as total
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) = CURDATE()
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
        ";
        $result = $this->db->query($sql)->row();
        return $result ? (int)$result->total : 0;
    }

    /**
     * [HÔM NAY] Tính tổng doanh thu của các đơn được xác nhận trong ngày.
     */
    public function get_revenue_of_orders_confirmed_today()
    {
        $sql = "
            SELECT SUM(t.revenue) as total_revenue
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_price_after_sub_discount') as revenue
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) = CURDATE()
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";
        $result = $this->db->query($sql)->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * [HÔM NAY] Tính tổng doanh số của các đơn được xác nhận trong ngày.
     */
    public function get_sales_volume_of_orders_confirmed_today()
    {
        $sql = "
            SELECT SUM(t.sales_volume) as total_sales_volume
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_price') as sales_volume
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) = CURDATE()
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";
        $result = $this->db->query($sql)->row();
        return $result ? (float)$result->total_sales_volume : 0;
    }

    /**
     * [HÔM NAY] Tính tổng số lượng sản phẩm của các đơn được xác nhận trong ngày.
     */
    public function get_product_quantity_of_orders_confirmed_today()
    {
        $sql = "
            SELECT SUM(t.product_quantity) as total_products
            FROM (
                SELECT DISTINCT
                    po.id,
                    JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(
                    po.data,
                    '$.status_history[*]'
                    COLUMNS (
                        status INT PATH '$.status',
                        updated_at DATETIME PATH '$.updated_at'
                    )
                ) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    -- Lấy ngày hiện tại của máy chủ DB
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) = CURDATE()
                    -- Loại trừ các trạng thái không mong muốn
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";

        $result = $this->db->query($sql)->row();

        return $result ? (int)$result->total_products : 0;
    }
    /**
     * [HÔM NAY] Đếm số lượng khách hàng duy nhất đã tạo đơn trong ngày.
     */
    public function count_unique_customers_today()
    {
        $sql = "
        SELECT COUNT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(data, '$.customer.id'))) as total_customers
        FROM " . db_prefix() . "pancake_orders
        WHERE 
            DATE(DATE_ADD(created_at, INTERVAL 7 HOUR)) = CURDATE()
            AND JSON_UNQUOTE(JSON_EXTRACT(data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
    ";
        $result = $this->db->query($sql)->row();
        return $result ? (int)$result->total_customers : 0;
    }

    /**
     * [CỐ ĐỊNH] Lấy SL sản phẩm của các đơn được xác nhận HÔM NAY.
     */
    public function get_product_quantity_confirmed_today()
    {
        $sql = "
            SELECT SUM(t.product_quantity) as total_products
            FROM (
                SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
                FROM " . db_prefix() . "pancake_orders po
                JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
                WHERE 
                    history.status = 1 
                    AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) = CURDATE()
                    AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            ) as t
        ";
        $result = $this->db->query($sql)->row();
        return $result ? (int)$result->total_products : 0;
    }
    /**
     * Doanh thu từ các nguồn đơn
     */
    /**
     * [KHOẢNG NGÀY] Tính tổng doanh thu từ nguồn AFFILIATE của các đơn được xác nhận.
     */
    public function get_revenue_of_affiliate_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Affiliate'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_revenue_of_facebook_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Facebook'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_revenue_of_shopee_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Shopee'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_revenue_of_zalo_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Zalo'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_revenue_of_tiktok_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Tiktok'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_revenue_of_woocommerce_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Woocommerce'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_revenue_of_hotline_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Hotline'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_revenue_of_ladipage_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Ladipage'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_revenue_of_others_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.net_revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id,
                -- Tính doanh thu thực: total_price trừ đi tổng các loại discount
                (
                    IFNULL(JSON_EXTRACT(po.data, '$.total_price'), 0) - 
                    (
                        -- Giảm giá toàn đơn hàng
                        IFNULL(JSON_EXTRACT(po.data, '$.total_discount'), 0) +
                        -- Cộng tổng giảm giá của từng sản phẩm trong đơn
                        (
                            SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                            FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                        )
                    )
                ) as net_revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Khác'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * Doanh số từ các nguồn đơn
     */
    public function get_sale_of_ctv_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Affiliate'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_facebook_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Facebook'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_shopee_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Shopee'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_zalo_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Zalo'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_sale_of_tiktok_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Tiktok'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_woocommerce_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Woocommerce'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_hotline_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Hotline'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_ladipage_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'LadiPage'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_sale_of_others_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.revenue) as total_revenue
        FROM (
            SELECT 
                DISTINCT po.id, 
                -- Lấy tổng tiền hàng CỘNG với phí vận chuyển (nếu có)
                (JSON_EXTRACT(po.data, '$.total_price') + IFNULL(JSON_EXTRACT(po.data, '$.shipping_fee'), 0)) as revenue
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                -- THÊM ĐIỀU KIỆN LỌC THEO NGUỒN Ở ĐÂY
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Khác'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * Chiết khấu
     */
    public function get_discount_of_ctv_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Affiliate'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_discount_of_facebook_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Facebook'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_discount_of_shopee_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Shopee'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_discount_of_zalo_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Zalo'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_discount_of_tiktok_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Tiktok'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
    public function get_discount_of_woocommerce_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Woocommerce'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }
     public function get_discount_of_hotline_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Hotline'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_discount_of_ladipage_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'LadiPage'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    public function get_discount_of_others_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.total_discount) as total_revenue
        FROM (
            SELECT
                DISTINCT po.id,
                -- Lấy tổng giảm giá từ đơn hàng
                JSON_EXTRACT(po.data, '$.total_discount') +
                -- Cộng thêm tổng giảm giá từ từng sản phẩm
                (
                    SELECT IFNULL(SUM(JSON_EXTRACT(items.value, '$.total_discount')), 0)
                    FROM JSON_TABLE(po.data, '$.items[*]' COLUMNS (value JSON PATH '$')) AS items
                ) AS total_discount
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Khác'
        ) AS t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (float)$result->total_revenue : 0;
    }

    /**
     * Đơn chốt cho các nguồn
     */
    public function count_ctv_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Affiliate'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }
    public function count_facebook_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Facebook'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_shopee_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Shopee'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }


    public function count_zalo_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Zalo'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_tiktok_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Tiktok'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_woocommerce_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Woocommerce'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_hotline_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Hotline'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_ladipage_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'LadiPage'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    public function count_others_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT COUNT(DISTINCT po.id) as total
        FROM " . db_prefix() . "pancake_orders po
        JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
        WHERE 
            history.status = 1 
            AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
            -- Dòng được thêm vào để lọc nguồn Facebook
            AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Khác'
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total : 0;
    }

    //SL hàng chốt
    /////////////////////////////////////***************************************//////////////////////////
    public function get_product_ctv_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Affiliate'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_facebook_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Facebook'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_Shopee_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Shopee'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_zalo_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Zalo'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }
    public function get_product_tiktok_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Tiktok'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_woocommerce_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Woocommerce'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_hotline_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Hotline'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_ladipage_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'LadiPage'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    public function get_product_others_quantity_of_orders_confirmed_in_range($start_date, $end_date)
    {
        if (empty($start_date) || empty($end_date)) {
            return 0;
        }

        $sql = "
        SELECT SUM(t.product_quantity) as total_products
        FROM (
            SELECT DISTINCT po.id, JSON_EXTRACT(po.data, '$.total_quantity') as product_quantity
            FROM " . db_prefix() . "pancake_orders po
            JOIN JSON_TABLE(po.data, '$.status_history[*]' COLUMNS (status INT PATH '$.status', updated_at DATETIME PATH '$.updated_at')) AS history ON TRUE
            WHERE 
                history.status = 1 
                AND DATE(DATE_ADD(history.updated_at, INTERVAL 7 HOUR)) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.status_name')) NOT IN ('canceled', 'returned', 'returning')
                AND JSON_UNQUOTE(JSON_EXTRACT(po.data, '$.order_sources_name')) = 'Khác'
        ) as t
    ";

        $result = $this->db->query($sql, [$start_date, $end_date])->row();
        return $result ? (int)$result->total_products : 0;
    }

    //GTTB
    ///////////////////////////////////***********************************/////////////////////////////////////
    public function get_aov_of_ctv_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_affiliate_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_ctv_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_facebook_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_facebook_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_facebook_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_shopee_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_shopee_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_shopee_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_zalo_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_zalo_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_zalo_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_tiktok_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_tiktok_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_tiktok_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_woocommerce_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_woocommerce_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_woocommerce_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_hotline_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_hotline_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_hotline_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_ladipage_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_ladipage_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_ladipage_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }

    public function get_aov_of_others_orders_confirmed_in_range($start_date, $end_date)
    {
        // Lấy tổng doanh thu từ các đơn hàng Facebook đã xác nhận
        $total_revenue = $this->get_revenue_of_others_orders_confirmed_in_range($start_date, $end_date);

        // Đếm tổng số đơn hàng Facebook đã xác nhận
        $total_orders = $this->count_others_orders_confirmed_in_range($start_date, $end_date);

        // Tránh lỗi chia cho 0 nếu không có đơn hàng nào
        if ($total_orders == 0) {
            return 0;
        }

        // Tính và trả về giá trị trung bình
        return (float) $total_revenue / $total_orders;
    }
}
