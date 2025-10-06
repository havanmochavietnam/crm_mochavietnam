<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_orders_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        // Không cần cache ở model Orders nữa
    }

    /* ==================== SYNC ORDERS (NGUYÊN VẸN NHƯ BẠN GỬI) ==================== */
    public function sync_orders($orders)
    {
        if (empty($orders)) {
            return ['ok' => 0, 'err' => 0, 'errors' => []];
        }

        $ok = 0;
        $err = 0;
        $errors = [];

        // ===== Helpers =====
        $str = function ($v, $max = null) {
            if ($v === null) return null;
            if (!is_string($v)) $v = (string)$v;
            $v = mb_convert_encoding($v, 'UTF-8', 'UTF-8');
            if ($max !== null) $v = mb_substr($v, 0, $max);
            $v = trim($v);
            return ($v === '') ? null : $v;
        };
        $num = function ($v) {
            if ($v === null || $v === '') return 0;
            $x = filter_var($v, FILTER_VALIDATE_FLOAT);
            return $x !== false ? (float)$x : 0;
        };
        $dt_auto = function ($v) {
            if ($v === null || $v === '') return null;

            // 1) Epoch number (giây hoặc millis)
            if (is_int($v) || (is_string($v) && ctype_digit($v))) {
                $ts = (int)$v;
                if ($ts > 2000000000) { // có thể là millis
                    $ts = (int)round($ts / 1000);
                }
                // Epoch luôn là UTC -> cộng +7
                return date('Y-m-d H:i:s', $ts + 7 * 3600);
            }

            // 2) ISO string / các chuỗi ngày-giờ
            $s = trim((string)$v);

            // Bỏ phần mili-giây để strtotime đỡ kén
            $s_norm = preg_replace('/\.\d{1,6}(?=(Z|[+\-]\d{2}:\d{2}|$))/', '', $s);

            $ts = strtotime($s_norm);
            if ($ts === false) return null;

            $has_offset = (bool)preg_match('/(Z|[+\-]\d{2}:\d{2}| \+\d{4})$/i', $s_norm);
            $is_utc     = (bool)preg_match('/(Z|[+\-]00:00| \+0000)$/i', $s_norm);
            $is_plus7   = (bool)preg_match('/(\+07:00| \+0700)$/i', $s_norm);
            $has_T      = strpos($s_norm, 'T') !== false;

            if ($is_utc) {
                $ts += 7 * 3600;
            } elseif ($is_plus7) {
                // giữ nguyên
            } else {
                // Không có offset rõ ràng
                // Quy ước: ISO có 'T' mà KHÔNG có offset => coi là UTC -> +7
                // Còn định dạng local kiểu 'Y-m-d H:i:s' => giữ nguyên
                if ($has_T && !$has_offset) {
                    $ts += 7 * 3600;
                }
            }

            return date('Y-m-d H:i:s', $ts);
        };
        // So sánh & lấy MAX theo thời gian (input là 'Y-m-d H:i:s' hoặc null)
        $max_time = function ($a, $b) {
            if ($a === null) return $b;
            if ($b === null) return $a;
            return (strtotime($a) >= strtotime($b)) ? $a : $b;
        };
        $js  = function ($v) {
            return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        };

        $tableOrders  = db_prefix() . 'pancake_orders';
        $tableDetails = db_prefix() . 'pancake_order_details';

        foreach ($orders as $o) {
            try {
                // ====== Phần tính toán giữ nguyên ý tưởng của bạn ======
                $items_discount_sum = 0;
                if (!empty($o['items']) && is_array($o['items'])) {
                    foreach ($o['items'] as $item) {
                        $items_discount_sum += $item['total_discount'] ?? 0;
                    }
                }
                $final_discount = ($o['total_discount'] ?? 0) + $items_discount_sum;

                // Thu thập MAX thời gian cho từng trạng thái
                $status_times = [
                    'time_status_new'        => null,
                    'time_status_submitted'  => null,
                    'time_status_shipped'    => null,
                    'time_status_delivered'  => null,
                    'time_status_returning'  => null,
                    'time_status_returned'   => null,
                    'time_status_canceled'   => null,
                ];
                if (!empty($o['status_history']) && is_array($o['status_history'])) {
                    foreach ($o['status_history'] as $history) {
                        $t = $dt_auto($history['updated_at'] ?? null);
                        if (!$t) continue;
                        switch ((int)($history['status'] ?? -1)) {
                            case 0:
                                $status_times['time_status_new']        = $max_time($status_times['time_status_new'],        $t);
                                break;
                            case 1:
                                $status_times['time_status_submitted']  = $max_time($status_times['time_status_submitted'],  $t);
                                break;
                            case 2:
                                $status_times['time_status_shipped']    = $max_time($status_times['time_status_shipped'],    $t);
                                break;
                            case 3:
                                $status_times['time_status_delivered']  = $max_time($status_times['time_status_delivered'],  $t);
                                break;
                            case 6:
                                $status_times['time_status_returning']  = $max_time($status_times['time_status_returning'],  $t);
                                break;
                            case 7:
                                $status_times['time_status_returned']   = $max_time($status_times['time_status_returned'],   $t);
                                break;
                            case 5:
                                $status_times['time_status_canceled']   = $max_time($status_times['time_status_canceled'],   $t);
                                break;
                        }
                    }
                }

                // Tổng sau tất cả discount (dựa trên items + discount đơn)
                $total_after_all_discounts = 0;
                if (!empty($o['items']) && is_array($o['items'])) {
                    foreach ($o['items'] as $item) {
                        $qty = (int)($item['quantity'] ?? 0);
                        $retail_price = (float)($item['variation_info']['retail_price'] ?? 0);
                        $total_after_all_discounts += ($retail_price * $qty) - (float)($item['total_discount'] ?? 0);
                    }
                }
                $total_after_all_discounts -= (float)($o['total_discount'] ?? 0);
                // ====== Hết phần tính ======

                // created_at: đồng bộ theo dt_auto
                $createdRaw = $o['inserted_at'] ?? $o['created_at'] ?? $o['created_time'] ?? $o['createdAt'] ?? null;

                // phone format
                $raw_phone       = $o['shipping_address']['phone_number'] ?? '';
                $formatted_phone = preg_replace('/^\+84/', '0', $raw_phone);

                // tags
                $tags = null;
                if (!empty($o['tags']) && is_array($o['tags'])) {
                    $names = array_map(function ($t) {
                        return is_array($t) ? ($t['name'] ?? null) : $t;
                    }, $o['tags']);
                    $names = array_filter($names, fn($x) => !empty($x));
                    $tags  = implode(', ', $names);
                }

                // customer type
                $customer_level_name = $o['customer']['level']['name'] ?? null;
                $customer_type = (in_array($customer_level_name, [null, 'Mua lần 1'], true)) ? 'Mới' : 'Cũ';

                // nguồn: map Affiliate -> CTV
                $order_sources_name = $o['order_sources_name'] ?? null;
                if ($order_sources_name === 'Affiliate') $order_sources_name = 'CTV';

                $data = [
                    'pancake_order_id'        => $str($o['id'] ?? null, 64),

                    'customer_name'           => $str($o['shipping_address']['full_name'] ?? null, 191),
                    'customer_phone'          => $str($formatted_phone, 32),

                    'assigning_seller_id'     => $o['assigning_seller']['id'] ?? null,
                    'assigning_seller_name'   => $str($o['assigning_seller']['name'] ?? null, 255),
                    'assigning_care_id'       => $o['assigning_care']['id'] ?? null,
                    'assigning_care_name'     => $str($o['assigning_care']['name'] ?? null, 255),
                    'time_assign_care'        => $dt_auto($o['time_assign_care'] ?? null),

                    'marketer_id'             => $o['marketer']['id'] ?? null,
                    'marketer_name'           => $str($o['marketer']['name'] ?? null, 255),

                    'status_name'             => $str($o['status_name'] ?? null, 64),
                    'order_source'            => $str($o['order_sources'] ?? null, 191),
                    'order_sources_name'      => $str($order_sources_name, 191),

                    'cod'                     => $num($o['cod'] ?? $o['cash_on_delivery'] ?? 0),
                    'total_discount'          => $num($final_discount),
                    'shipping_fee'            => $num($o['shipping_fee'] ?? 0),
                    'total_order_amount'      => $num($total_after_all_discounts),
                    'total_price'             => $num($o['total_price'] ?? 0),
                    'revenue'                 => $num($o['total_price_after_sub_discount'] ?? 0),

                    'partner_fee'             => $num($o['partner_fee'] ?? 0),
                    'fee_marketplace_voucher' => $num($o['advanced_platform_fee']['marketplace_voucher'] ?? 0),
                    'fee_payment'             => $num($o['advanced_platform_fee']['payment_fee'] ?? 0),
                    'fee_platform_commission' => $num($o['advanced_platform_fee']['platform_commission'] ?? 0),
                    'fee_affiliate_commission' => $num($o['advanced_platform_fee']['affiliate_commission'] ?? 0),
                    'fee_sfp_service'         => $num($o['advanced_platform_fee']['sfp_service_fee'] ?? 0),
                    'fee_seller_transaction'  => $num($o['advanced_platform_fee']['seller_transaction_fee'] ?? 0),
                    'fee_service'             => $num($o['advanced_platform_fee']['service_fee'] ?? 0),

                    'shipping_code'           => $str($o['partner']['order_number_vtp'] ?? null, 191),
                    'shipping_partner'        => $str($o['partner']['partner_name'] ?? null, 191),
                    'shipping_status'         => $str($o['partner']['extend_update'][0]['status'] ?? null, 191),
                    'time_send_partner'       => $dt_auto($o['time_send_partner'] ?? null),

                    'tags'                    => $str($tags, 1024),
                    'customer_type'           => $str($customer_type, 32),
                    'province'                => $str($o['shipping_address']['province_name'] ?? null, 191),
                    'district'                => $str($o['shipping_address']['district_name'] ?? null, 191),
                    'ward'                    => $str($o['shipping_address']['commune_name'] ?? null, 191),

                    'page_id'                 => $str($o['page_id'] ?? null, 64),
                    'ad_id'                   => $str($o['ad_id'] ?? null, 64),
                    'ads_source'              => $str($o['ads_source'] ?? null, 191),
                    'detailed_source'         => $str($o['account_name'] ?? null, 191),
                    'chat_page'               => $str($o['page']['name'] ?? null, 191),

                    'creator_id'              => $o['creator']['id'] ?? null,
                    'creator_name'            => $str($o['creator']['name'] ?? 'Hệ thống', 191),

                    'warehouse_info'          => $str($o['warehouse_info']['name'] ?? null, 191),

                    'p_utm_source'            => $str($o['p_utm_source'] ?? null, 191),
                    'p_utm_medium'            => $str($o['p_utm_medium'] ?? null, 191),
                    'p_utm_campaign'          => $str($o['p_utm_campaign'] ?? null, 191),
                    'p_utm_term'              => $str($o['p_utm_term'] ?? null, 191),
                    'p_utm_content'           => $str($o['p_utm_content'] ?? null, 191),
                    'p_utm_id'                => $str($o['p_utm_id'] ?? null, 191),

                    'post_id'                 => $str($o['post_id'] ?? null, 64),

                    // created_at thống nhất theo dt_auto
                    'created_at'              => $dt_auto($createdRaw),

                    'data'                    => $js($o),

                    // Các mốc trạng thái (đã lấy MAX)
                    'time_status_new'         => $status_times['time_status_new'],
                    'time_status_submitted'   => $status_times['time_status_submitted'],
                    'time_status_shipped'     => $status_times['time_status_shipped'],
                    'time_status_delivered'   => $status_times['time_status_delivered'],
                    'time_status_returning'   => $status_times['time_status_returning'],
                    'time_status_returned'    => $status_times['time_status_returned'],
                    'time_status_canceled'    => $status_times['time_status_canceled'],
                ];

                if ($data['pancake_order_id'] === null) {
                    throw new Exception('missing pancake_order_id');
                }

                // Lấy items (tương thích nhiều schema)
                $items = $o['items'] ?? $o['order_items'] ?? $o['products'] ?? $o['line_items'] ?? [];
                if (empty($items) && isset($o['order']['items']) && is_array($o['order']['items'])) {
                    $items = $o['order']['items'];
                }

                $rows = [];
                foreach ($items as $it) {
                    $vi = $it['variation_info'] ?? $it['variationInfo'] ?? $it['product'] ?? $it;

                    $product_id   = $vi['display_id'] ?? ($it['variation_info']['display_id'] ?? ($vi['id'] ?? null));
                    $product_name = $str($vi['name'] ?? $it['product_name'] ?? $it['title'] ?? null, 191);
                    $quantity     = (int)($it['quantity'] ?? $vi['quantity'] ?? 0);

                    $img = null;
                    if (!empty($vi['images']) && is_array($vi['images'])) {
                        $img = is_array($vi['images'][0] ?? null) ? ($vi['images'][0]['url'] ?? null) : ($vi['images'][0] ?? null);
                    }
                    if (!$img) $img = $vi['image_url'] ?? $vi['image'] ?? $it['image_url'] ?? $it['image'] ?? null;
                    $image_url = $str($img, 255);

                    $unit_price     = $num($vi['retail_price'] ?? $vi['price'] ?? $it['unit_price'] ?? $it['price'] ?? 0);
                    $total_discount = $num($it['total_discount'] ?? $it['discount'] ?? $vi['discount'] ?? 0);

                    $is_combo = (!empty($it['is_composite']) || !empty($it['is_combo'])) ? 1 : 0;
                    $is_gift  = (!empty($it['is_bonus_product']) || !empty($it['is_gift']) || $unit_price <= 0 || $total_discount >= $unit_price) ? 1 : 0;

                    $rows[] = [
                        'pancake_order_id' => $data['pancake_order_id'],
                        'product_id'       => $product_id,
                        'product_name'     => $product_name,
                        'quantity'         => $quantity,
                        'image_url'        => $image_url,
                        'unit_price'       => $unit_price,
                        'total_discount'   => $total_discount,
                        'is_combo'         => $is_combo,
                        'is_gift'          => $is_gift,
                    ];
                }

                // ===== DB write (upsert master + replace details) =====
                $this->db->db_debug = FALSE;
                $this->db->trans_strict(TRUE);
                $this->db->trans_begin();

                // Upsert orders
                $this->db->set($data);
                $sql = $this->db->get_compiled_insert($tableOrders);
                $updateFields = array_diff(array_keys($data), ['pancake_order_id']);
                $assignments = implode(',', array_map(function ($k) {
                    return "`{$k}`=VALUES(`{$k}`)";
                }, $updateFields));
                $sql .= " ON DUPLICATE KEY UPDATE {$assignments}";

                if (!$this->db->simple_query($sql)) {
                    $e = $this->db->error();
                    $this->db->trans_rollback();
                    throw new Exception("Upsert orders failed [{$e['code']}]: {$e['message']}");
                }

                // Replace details
                if (!empty($rows)) {
                    $this->db->where('pancake_order_id', $data['pancake_order_id'])->delete($tableDetails);
                    if (!$this->db->insert_batch($tableDetails, $rows)) {
                        $e = $this->db->error();
                        $this->db->trans_rollback();
                        throw new Exception("Insert details failed [{$e['code']}]: {$e['message']}");
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $e = $this->db->error();
                    $this->db->trans_rollback();
                    throw new Exception("DB transaction failed [{$e['code']}]: {$e['message']}");
                }

                $this->db->trans_commit();
                $ok++;
            } catch (Throwable $e) {
                $err++;
                $errors[] = ['order_id' => $o['id'] ?? null, 'error' => $e->getMessage()];
                continue;
            }
        }

        return ['ok' => $ok, 'err' => $err, 'errors' => array_slice($errors, 0, 10)];
    }


    /* ==================== SELLERS ==================== */
    public function get_sellers_from_orders()
    {
        $this->db->select("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) as seller_id");
        $this->db->select("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.name')) as seller_name");
        $this->db->from(db_prefix() . 'pancake_orders');
        $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) IS NOT NULL");
        $this->db->group_by(['seller_id', 'seller_name']);
        $this->db->order_by('seller_name', 'ASC');
        return $this->db->get()->result_array();
    }

    /* ==================== LIST ORDERS ==================== */
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
                $sanitized_ids = array_map('intval', $seller_ids);
                $this->db->where("JSON_UNQUOTE(JSON_EXTRACT(data, '$.assigning_seller.id')) IN (" . implode(',', $sanitized_ids) . ")");
            }
        }
        $total_rows = $this->db->count_all_results(db_prefix() . 'pancake_orders', false);
        $page = $filters['page_number'] ?? 1;
        $pageSize = $filters['page_size'] ?? 30;
        $offset = ($page - 1) * $pageSize;
        $this->db->limit($pageSize, $offset);
        $this->db->order_by('created_at', 'DESC');
        $orders_from_db = $this->db->get()->result_array();
        $orders_formatted = [];
        foreach ($orders_from_db as $order_row) {
            $orders_formatted[] = json_decode($order_row['data'], true);
        }

        return [
            'data'  => $orders_formatted,
            'total' => $total_rows
        ];
    }
}
