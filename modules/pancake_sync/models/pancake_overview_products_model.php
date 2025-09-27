<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_overview_products_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function placeholders($n)
    {
        return implode(',', array_fill(0, max(1, (int)$n), '?'));
    }

    /**
     * Tổng doanh thu từ details cho các đơn ĐÃ TỪNG có status=$status trong khoảng thời gian.
     * - Đọc trực tiếp JSON: status_history[*] với JSON_TABLE.
     * - Lọc loại trừ nguồn (order_sources_name) và trạng thái (canceled/returned/returning).
     * - SUM(evenua) hoặc SUM(evenua * quantity) tuỳ $use_quantity.
     * - +7h nếu dữ liệu updated_at là UTC và bạn muốn GMT+7.
     * - Vá lỗi mix collation bằng COLLATE trên phía cột.
     */
    public function get_total_revenue_had_status_in_range(
        string $start_date,
        string $end_date,
        int $status = 1,
        array $exclude_sources = ['Tiktok', 'Shopee', 'Affiliate'],
        bool $use_quantity = false,
        string $tz_offset_sql = 'INTERVAL 7 HOUR'
    ): float {
        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        $startDT = $start_date . ' 00:00:00';
        $endDT   = $end_date   . ' 23:59:59';

        $exclude_sources = array_values(array_filter($exclude_sources, fn($x) => $x !== null && $x !== ''));
        if (empty($exclude_sources)) $exclude_sources = ['__NO_SOURCE__'];
        $ph = $this->placeholders(count($exclude_sources));

        $use_quantity = (bool)$use_quantity;

        // Chọn 1 collation duy nhất cho tất cả so sánh chuỗi trong query này
        // Nếu schema của bạn chuẩn hoá sang 0900_ai_ci thì dùng dòng dưới:
        // $collate = 'utf8mb4_0900_ai_ci';
        $collate = 'utf8mb4_unicode_ci';

        $sumExpr = $use_quantity ? 'SUM(d.evenua * d.quantity)' : 'SUM(d.evenua)';

        $sql = "
        SELECT COALESCE({$sumExpr}, 0) AS total_revenue
        FROM {$tableOrders} o
        JOIN {$tableDetails} d
          ON d.pancake_order_id COLLATE {$collate}
           = o.pancake_order_id COLLATE {$collate}
        WHERE EXISTS (
            SELECT 1
            FROM JSON_TABLE(
                o.data, '$.status_history[*]'
                COLUMNS (
                    s INT PATH '$.status',
                    t DATETIME PATH '$.updated_at'
                )
            ) h
            WHERE h.s = ?
              AND DATE_ADD(h.t, {$tz_offset_sql}) BETWEEN ? AND ?
        )
          AND (o.status_name        COLLATE {$collate} NOT IN ('canceled','returned','returning') OR o.status_name IS NULL)
          AND (o.order_sources_name COLLATE {$collate} NOT IN ({$ph}) OR o.order_sources_name IS NULL)
    ";

        $bind = array_merge([$status, $startDT, $endDT], $exclude_sources);
        $row  = $this->db->query($sql, $bind)->row_array();

        return (float)($row['total_revenue'] ?? 0);
    }
}
