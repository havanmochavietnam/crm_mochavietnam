<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    /* CSS giữ nguyên từ trước */
    .dashboard-panel {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, .05);
        padding: 20px;
        margin-bottom: 20px;
    }

    .dashboard-panel h4 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .dashboard-panel .text-muted {
        font-size: 13px;
        color: #666;
    }

    .big-number {
        font-size: 28px;
        font-weight: 700;
        margin: 5px 0;
    }

    .small-percent {
        font-size: 13px;
        font-weight: 500;
        margin-top: 5px;
    }

    .mright5 {
        margin-right: 5px;
    }

    .text-success-light {
        color: #28a745;
    }

    .text-danger-light {
        color: #dc3545;
    }

    .text-success-percent {
        color: #28a745;
    }

    .text-danger-percent {
        color: #dc3545;
    }

    .top-stat-panel {
        display: flex;
        align-items: flex-start;
        padding: 25px 20px;
    }

    .top-stat-panel .icon-wrapper {
        font-size: 28px;
        margin-right: 15px;
        padding: 10px;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .top-stat-panel .icon-wrapper.green {
        background-color: #e6f7ed;
        color: #28a745;
    }

    .top-stat-panel .icon-wrapper.orange {
        background-color: #fff4e6;
        color: #fd7e14;
    }

    .top-stat-panel .stat-content {
        flex-grow: 1;
    }

    .top-stat-panel .stat-header {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .top-stat-panel .stat-metrics {
        display: flex;
        justify-content: space-between;
    }

    .top-stat-panel .metric-item {
        flex-basis: 48%;
    }

    .top-stat-panel .metric-item+.metric-item {
        border-left: 1px solid #eee;
        padding-left: 20px;
    }

    .bottom-stat-panel {
        padding: 20px;
    }

    .bottom-stat-panel .panel-title-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .bottom-stat-panel .panel-title-group .percent {
        font-size: 14px;
        font-weight: 500;
    }

    .bottom-stat-panel .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 15px;
    }

    .bottom-stat-panel .stat-row:last-child {
        margin-bottom: 0;
    }

    .bottom-stat-panel .stat-label {
        color: #555;
    }

    .bottom-stat-panel .stat-value {
        font-weight: 600;
    }

    .bottom-stat-panel .stat-value.currency {
        font-size: 17px;
    }

    .full-width-chart-panel {
        padding: 25px;
    }

    .full-width-chart-panel .panel-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 20px;
    }

    .full-width-chart-panel .chart-top-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .full-width-chart-panel .chart-stat-box {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 15px 20px;
        flex: 1;
    }

    .full-width-chart-panel .chart-stat-box p {
        margin: 0;
        font-size: 14px;
        color: #555;
    }

    .full-width-chart-panel .chart-stat-box h3 {
        margin: 5px 0 0 0;
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    #hourly-revenue-chart {
        height: 250px;
        width: 100%;
        margin-bottom: 25px;
    }

    .full-width-chart-panel .chart-bottom-summaries {
        display: flex;
        gap: 20px;
        margin-bottom: 25px;
    }

    .full-width-chart-panel .summary-box {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 20px;
        flex: 1;
    }

    .full-width-chart-panel .summary-box .summary-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
    }

    .summary-box .summary-title .dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .summary-box .summary-title .dot.blue {
        background-color: #007bff;
    }

    .summary-box .summary-title .dot.green {
        background-color: #28a745;
    }

    .summary-box p {
        margin: 0;
        font-size: 14px;
    }

    .summary-box p strong {
        font-size: 18px;
    }

    .small-stats-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .small-stats-grid .small-stat-panel {
        background-color: #f9f9f9;
        text-align: center;
        padding: 20px 15px;
        border-radius: 8px;
        flex: 1 1 15%;
        min-width: 120px;
    }

    .small-stats-grid .small-stat-panel p {
        margin: 0 0 5px 0;
        color: #555;
        font-size: 13px;
    }

    .small-stats-grid .small-stat-panel h3 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
    }

    .overview-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .overview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .overview-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
    }

    .overview-date-filter {
        display: flex;
        align-items: center;
    }

    .overview-date-filter select {
        margin-left: 10px;
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .overview-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .overview-stat-box {
        background-color: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .overview-stat-box h4 {
        font-size: 16px;
        color: #555;
        margin: 0 0 10px 0;
    }

    .overview-stat-box .value {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .overview-stat-box .trend {
        font-size: 14px;
    }

    .trend.up {
        color: #28a745;
    }

    .trend.down {
        color: #dc3545;
    }

    .eight-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
    }

    .eight-stat-item {
        background-color: white;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .eight-stat-item .label {
        font-size: 14px;
        color: #555;
        margin-bottom: 8px;
    }

    .eight-stat-item .value {
        font-size: 20px;
        font-weight: 700;
    }

    .info-section {
        margin-bottom: 30px;
        border-bottom: 2px solid #eee;
        padding-bottom: 15px;
    }

    .info-section h3 {
        font-size: 22px;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
        display: inline-block;
    }

    /* CSS CHO BẢNG HIỆU SUẤT MỚI */
    .performance-table {
        margin-bottom: 0;
        font-size: 14px;
    }

    .performance-table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .performance-table tbody td {
        vertical-align: middle;
        text-align: right;
        padding: 12px 8px;
    }

    .performance-table tbody td:first-child {
        text-align: left;
        font-weight: 500;
        white-space: nowrap;
    }

    .performance-table .trend-indicator {
        font-size: 12px;
        display: block;
        margin-top: 2px;
    }

    .performance-table .trend-up {
        color: #28a745;
    }

    .performance-table .trend-down {
        color: #dc3545;
    }

    .performance-table .source-icon {
        margin-right: 8px;
        font-size: 18px;
        width: 20px;
        text-align: center;
        display: inline-block;
    }
</style>

<div id="wrapper">
    <div class="content">
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        <?php
        $statusMap = [
            'new'           => ['text' => 'Mới', 'class' => 'tw-bg-blue-100 tw-text-blue-800'],
            'wait_submit'   => ['text' => 'Chờ xác nhận', 'class' => 'tw-bg-yellow-100 tw-text-yellow-800'],
            'submitted'     => ['text' => 'Đã xác nhận', 'class' => 'tw-bg-indigo-100 tw-text-indigo-800'],
            'packing'       => ['text' => 'Đang đóng hàng', 'class' => 'tw-bg-purple-100 tw-text-purple-800'],
            'shipped'       => ['text' => 'Đã gửi hàng', 'class' => 'tw-bg-cyan-100 tw-text-cyan-800'],
            'delivered'     => ['text' => 'Đã nhận', 'class' => 'tw-bg-green-100 tw-text-green-800'],
            'returning'     => ['text' => 'Đang hoàn', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
            'returned'      => ['text' => 'Đã hoàn', 'class' => 'tw-bg-lime-100 tw-text-lime-800'],
            'canceled'      => ['text' => 'Đã huỷ', 'class' => 'tw-bg-red-100 tw-text-red-800'],
            'pending'       => ['text' => 'Đang chuyển hàng', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
            'removed'       => ['text' => 'Đã xoá', 'class' => 'tw-bg-gray-100 tw-text-gray-800'],
        ];
        $items = $order['items'] ?? ($order['products'] ?? []);
        $itemsCount = !empty($items) ? count($items) : 1;
        $firstItem = $items[0] ?? null;
        $secondItem = $items[1] ?? null;
        $statusKey = $order['status_name'] ?? 'new';
        $statusInfo = $statusMap[$statusKey] ?? ['text' => 'Không xác định', 'class' => 'tw-bg-gray-100 tw-text-gray-800'];
        $totalPrice = $order['total_price'] ?? 0;
        $cod = $order['cod'] ?? 0;
        $partner_fee = $order['partner_fee'] ?? 0;
        $codDoiSoat = $order['partner']['cod'] ?? 0;
        $totalPrice = $order['total_price'] ?? 0;
        $shipping_fee = $order['shipping_fee'] ?? 0;
        $total_price_after_sub_discount = $order['total_price_after_sub_discount'] ?? 0;
        $surcharge = $order['surcharge'] ?? 0;
        $fee_marketplace = $order['fee_marketplace'] ?? 0;
        $total_discount = $order['total_discount'] ?? 0;
        $money_to_collect = $order['money_to_collect'] ?? 0;
        $total_fee_partner = $order['partner']['total_fee'] ?? 0;
        $total_fee_marketplace_voucher = $order['advanced_platform_fee']['marketplace_voucher'] ?? 0;
        $total_fee_paymentFee = $order['advanced_platform_fee']['payment_fee'] ?? 0;
        $total_fee_platform_commission = $order['advanced_platform_fee']['platform_commission'] ?? 0;
        $total_fee_platform_affiliate_commission = $order['advanced_platform_fee']['affiliate_commission'] ?? 0;
        $total_fee_sfp_service_fee = $order['advanced_platform_fee']['sfp_service_fee'] ?? 0;
        $total_fee_seller_transaction_fee = $order['advanced_platform_fee']['seller_transaction_fee'] ?? 0;
        $total_fee_service_fee = $order['advanced_platform_fee']['service_fee'] ?? 0;
        $buyer_total_amount =  $order['buyer_total_amount'] ?? 0;
        $extendCode = $order['histories'][2]['extend_code']['new'] ?? null;
        $firststaffconfirm = $order['status_history'][0]['editor']['name'] ?? null;
        $staffconfirm = $order['status_history'][1]['editor']['name'] ?? null;
        $reconciliationTime = null; // Biến để lưu kết quả
        $extendUpdateHistory = $order['partner']['extend_update'] ?? [];
        $tagPancake = $order['customer']['conversation_tags'] ?? [];
        foreach ($extendUpdateHistory as $update) {
            if (isset($update['status']) && $update['status'] === 'Đã đối soát') {
                // Lấy thời gian và dừng vòng lặp
                $reconciliationTime = $update['update_at'] ?? null;
                break;
            }
        }
        $promotionName = $order['activated_promotion_advances'][0]['promotion_advance_info']['name'] ?? '';
        $totalOrders = $order['customer']['order_count'] ?? 0;
        $extendCodeVCLink = $order['histories'][1]['extend_code']['new'] ?? null;
        $p_utm_source = $order['p_utm_source'] ?? null;
        $p_utm_medium = $order['p_utm_medium'] ?? null;
        $p_utm_campaign = $order['p_utm_campaign'] ?? null;
        $p_utm_term = $order['p_utm_term'] ?? null;
        $p_utm_content = $order['p_utm_content'] ?? null;
        $p_utm_id = $order['p_utm_id'] ?? null;
        $tracking_id = $order['partner']['extend_code'] ?? null;
        $products_to_display = [];
        if (
            !empty($order['items']) &&
            isset($order['items'][0]['is_composite']) &&
            $order['items'][0]['is_composite'] === true &&
            !empty($order['items'][0]['components'])
        ) {
            // Nếu là combo, ta sẽ lặp qua các 'components'
            $products_to_display = $order['items'][0]['components'];
        } else {
            // Nếu là sản phẩm thường, ta lặp qua 'items' như bình thường
            $products_to_display = $order['items'] ?? []; // Dùng ?? [] để đảm bảo đây luôn là một mảng
        }
        $productsCount = count($products_to_display);
        ?>
        <form method="GET" action="<?= admin_url('pancake_sync') ?>" class="search-form">
            <div class="form-group">
                <label class="form-label">Ngày bắt đầu</label>
                <input type="date" name="startDateTime" value="<?= html_escape($this->input->get('startDateTime')) ?>" class="form-input">
            </div>

            <div class="form-group">
                <label class="form-label">Ngày kết thúc</label>
                <input type="date" name="endDateTime" value="<?= html_escape($this->input->get('endDateTime')) ?>" class="form-input">
            </div>
            </div>


            <div class="info-section">
                <h3><i class="fa-solid fa-chart-pie"></i> Tổng quan</h3>

                <div class="overview-section">
                    <div class="overview-header">
                        <div class="overview-title">Thống kê tổng quan</div>
                        <div class="overview-date-filter">
                            <span>Xem theo:</span>
                            <select>
                                <option value="today">Hôm nay</option>
                                <option value="week">Tuần này</option>
                                <option value="month">Tháng này</option>
                                <option value="quarter">Quý này</option>
                                <option value="year">Năm nay</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="dashboard-panel top-stat-panel">
                                <div class="icon-wrapper green"><i class="fa-solid fa-coins"></i></div>
                                <div class="stat-content">
                                    <div class="stat-header">Tổng hàng chốt</div>
                                    <div class="stat-metrics">
                                        <div class="metric-item">
                                            <div class="text-muted">Tổng tiền</div>
                                            <div class="big-number text-success-light">
                                                <?php
                                                $net_revenue = $summary['price'] ?? 0;
                                                $total_returned_value = 0;
                                                if (isset($daily_data) && is_array($daily_data)) {
                                                    foreach ($daily_data as $day) {
                                                        if (isset($day['returned']) && is_array($day['returned'])) {
                                                            $total_returned_value += $day['returned']['price'] ?? 0;
                                                        }
                                                    }
                                                }
                                                $gross_revenue = $net_revenue + $total_returned_value;
                                                echo number_format($gross_revenue, 0, ',', '.') . '';
                                                ?>
                                            </div>
                                            <div class="small-percent text-success-percent"><i class="fa-solid fa-arrow-up"></i> <?= $tong_hang_chot_percent ?? '0.00%'; ?></div>
                                        </div>
                                        <div class="metric-item">
                                            <div class="text-muted">Số lượng</div>
                                            <div class="big-number">
                                                <?php
                                                $gross_product_count = 0;
                                                if (isset($daily_data) && is_array($daily_data)) {
                                                    foreach ($daily_data as $day) {
                                                        if (isset($day['success']) && is_array($day['success'])) {
                                                            $gross_product_count += $day['success']['product_count'] ?? 0;
                                                        }
                                                    }
                                                }
                                                echo number_format($gross_product_count, 0, ',', '.');
                                                ?>
                                            </div>
                                            <div class="small-percent text-success-percent"><i class="fa-solid fa-arrow-up"></i> <?= $tong_hang_chot_soluong_percent ?? '0.00%'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-panel top-stat-panel">
                                <div class="icon-wrapper orange"><i class="fa-solid fa-right-left"></i></div>
                                <div class="stat-content">
                                    <div class="stat-header">Tổng hàng hoàn</div>
                                    <div class="stat-metrics">
                                        <div class="metric-item">
                                            <div class="text-muted">Tổng tiền</div>
                                            <div class="big-number text-danger-light"><?= number_format($tong_hang_hoan_tien ?? 0, 0, ',', '.'); ?> đ</div>
                                            <div class="small-percent text-danger-percent"><i class="fa-solid fa-arrow-down"></i> <?= $tong_hang_hoan_percent ?? '0.00%'; ?></div>
                                        </div>
                                        <div class="metric-item">
                                            <div class="text-muted">Số lượng</div>
                                            <div class="big-number"><?= number_format($tong_hang_hoan_soluong ?? 0); ?></div>
                                            <div class="small-percent text-danger-percent"><i class="fa-solid fa-arrow-down"></i> <?= $tong_hang_hoan_soluong_percent ?? '0.00%'; ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="dashboard-panel bottom-stat-panel">
                                <div class="panel-title-group">
                                    <h4 class="no-margin"><i class="fa-solid fa-dollar-sign mright5"></i> Tổng cộng</h4>
                                    <span class="percent text-success-percent"><i class="fa-solid fa-arrow-up mright5"></i><?= $tong_cong_percent ?? '0.00%'; ?></span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Doanh thu:</span>
                                    <span class="stat-value currency text-success-light">
                                        <?php
                                        $tong_tien_thu_ho_gop = 0;
                                        if (isset($daily_data) && is_array($daily_data)) {
                                            foreach ($daily_data as $day) {
                                                if (isset($day['success']) && is_array($day['success'])) {
                                                    $tong_tien_thu_ho_gop += $day['success']['cod'] ?? 0;
                                                }
                                            }
                                        }
                                        echo number_format($tong_tien_thu_ho_gop, 0, ',', '.') . ' VND';
                                        ?>
                                    </span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Đơn chốt:</span>
                                    <span class="stat-value text-info"><?= number_format($tong_cong_don_chot ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dashboard-panel bottom-stat-panel">
                                <div class="panel-title-group">
                                    <h4 class="no-margin"><i class="fa-solid fa-desktop mright5"></i> Online</h4>
                                    <span class="percent text-success-percent"><i class="fa-solid fa-arrow-up mright5"></i><?= $online_percent ?? '0.00%'; ?></span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Doanh thu:</span>
                                    <span class="stat-value currency text-success-light"><?= number_format($online_doanh_thu ?? 0, 0, ',', '.'); ?> đ</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Đơn chốt:</span>
                                    <span class="stat-value text-info"><?= number_format($online_don_chot ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="dashboard-panel bottom-stat-panel">
                                <div class="panel-title-group">
                                    <h4 class="no-margin"><i class="fa-solid fa-store mright5"></i> Bán tại quầy</h4>
                                    <span class="percent text-muted">-</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Doanh thu:</span>
                                    <span class="stat-value currency text-muted"><?= number_format($tai_quay_doanh_thu ?? 0, 0, ',', '.'); ?> đ</span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Đơn chốt:</span>
                                    <span class="stat-value text-muted"><?= number_format($tai_quay_don_chot ?? 0); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overview-stats-grid">
                        <div class="overview-stat-box">
                            <h4>Doanh số</h4>
                            <div class="value text-success-light">
                                <?= number_format($overviewTotalPrice ?? 0) ?> VND
                            </div>
                            <div class="trend up">+12.5% <i class="fa-solid fa-arrow-up"></i></div>
                        </div>

                        <div class="overview-stat-box">
                            <h4>Doanh thu</h4>
                            <div class="value"><?= number_format($tong_don_hang ?? 0); ?></div>
                            <div class="trend up">+8.3% <i class="fa-solid fa-arrow-up"></i></div>
                        </div>

                        <div class="overview-stat-box">
                            <h4>Số đơn chốt</h4>
                            <div class="value"><?= number_format($soDonChot ?? 0); ?></div>
                            <div class="trend up">+5.7% <i class="fa-solid fa-arrow-up"></i></div>
                        </div>

                        <div class="overview-stat-box">
                            <h4>GTTB</h4>
                            <div class="value"><?= number_format($gia_tri_trung_binh ?? 0, 0, ',', '.'); ?> đ</div>
                            <div class="trend up">+3.2% <i class="fa-solid fa-arrow-up"></i></div>
                        </div>
                    </div>

                    <div class="eight-stats-grid">
                        <div class="eight-stat-item">
                            <div class="label">SL Sản phẩm</div>
                            <div class="value"><?= number_format($tong_san_pham ?? 0); ?></div>
                        </div>

                        <div class="eight-stat-item">
                            <div class="label">SL SPTB</div>
                            <div class="value text-success"><?= number_format($don_hang_thanh_cong ?? 0); ?></div>
                        </div>

                        <div class="eight-stat-item">
                            <div class="label">Chi phí quảng cáo</div>
                            <div class="value text-danger"><?= number_format($don_hang_huy ?? 0); ?></div>
                        </div>

                        <div class="eight-stat-item">
                            <div class="label">Tỷ lệ chốt</div>
                            <div class="value"><?= $ty_le_chuyen_doi ?? '0'; ?>%</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3><i class="fa-solid fa-chart-line"></i> Phân tích kinh doanh trong ngày</h3>

                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboard-panel full-width-chart-panel">
                            <h4 class="panel-title"><i class="fa-solid fa-chart-line mright5"></i> Thông tin kinh doanh hôm nay</h4>
                            <div class="chart-top-stats">
                                <div class="chart-stat-box">
                                    <p>Doanh thu</p>
                                    <h3><?= number_format($overviewTotalPriceToday ?? 0, 0, ',', '.'); ?> VND</h3>
                                </div>
                                <div class="chart-stat-box">
                                    <p>Đơn chốt</p>
                                    <h3><?= number_format($soDonCapNhatHomNay ?? 0); ?></h3>
                                </div>
                            </div>
                            <div id="hourly-revenue-chart">
                            </div>
                            <div class="chart-bottom-summaries">
                                <div class="summary-box">
                                    <span class="summary-title"><span class="dot blue"></span>Bán tại quầy</span>
                                    <p><strong><?= number_format($tai_quay_doanh_thu ?? 0, 0, ',', '.'); ?> đ</strong></p>
                                    <p><?= number_format($tai_quay_don_chot ?? 0); ?> Đơn chốt</p>
                                </div>
                                <div class="summary-box">
                                    <span class="summary-title"><span class="dot green"></span>Online</span>
                                    <p><strong><?= number_format($overviewTotalPriceToday ?? 0, 0, ',', '.'); ?> đ</strong></p>
                                    <p><?= number_format($soDonChotToday ?? 0); ?> Đơn chốt</p>
                                </div>
                            </div>
                            <div class="small-stats-grid">
                                <div class="small-stat-panel">
                                    <p>Đơn tạo mới</p>
                                    <h3 class="text-info"><?= number_format($don_tao_moi ?? 0); ?></h3>
                                </div>
                                <div class="small-stat-panel">
                                    <p>Đơn hủy</p>
                                    <h3 class="text-danger"><?= number_format($don_huy ?? 0); ?></h3>
                                </div>
                                <div class="small-stat-panel">
                                    <p>Đơn chốt</p>
                                    <h3 class="text-success"><?= number_format($don_chot ?? 0); ?></h3>
                                </div>
                                <div class="small-stat-panel">
                                    <p>Đơn xoá</p>
                                    <h3><?= number_format($don_xoa ?? 0); ?></h3>
                                </div>
                                <div class="small-stat-panel">
                                    <p>Hàng bán ra</p>
                                    <h3><?= number_format($hang_ban_ra ?? 0); ?></h3>
                                </div>
                                <div class="small-stat-panel">
                                    <p>Khách hàng</p>
                                    <h3><?= number_format($khach_hang ?? 0); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h3><i class="fa-solid fa-bullseye"></i> Hiệu suất theo nguồn đơn</h3>
                <div class="dashboard-panel" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table table-hover performance-table">
                            <thead>
                                <tr>
                                    <th>Nguồn đơn</th>
                                    <th>Doanh thu</th>
                                    <th>Doanh số</th>
                                    <th>Đơn chốt</th>
                                    <th>SL hàng chốt</th>
                                    <th>GTTB</th>
                                    <th>SL TB / đơn</th>
                                    <th>Đơn hoàn</th>
                                    <th>Tỷ lệ hoàn</th>
                                    <th>Chi phí QC</th>
                                    <th>ROAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="fa-brands fa-facebook source-icon" style="color:#1877F2;"></i> Facebook
                                    </td>
                                    <td>
                                        25.159.000 đ
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 28.53%</span>
                                    </td>
                                    <td>
                                        25.533.000 đ
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 30.24%</span>
                                    </td>
                                    <td>
                                        27
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 25%</span>
                                    </td>
                                    <td>43</td>
                                    <td>931.815 đ</td>
                                    <td>1.6</td>
                                    <td>4</td>
                                    <td>14.8%</td>
                                    <td>5.000.000 đ</td>
                                    <td class="text-success"><strong>5.03</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa-brands fa-tiktok source-icon" style="color:#000000;"></i> Tiktok
                                    </td>
                                    <td>
                                        12.525.025 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 41.76%</span>
                                    </td>
                                    <td>
                                        14.590.500 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 50.76%</span>
                                    </td>
                                    <td>
                                        15
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 16.67%</span>
                                    </td>
                                    <td>35</td>
                                    <td>835.000 đ</td>
                                    <td>2.3</td>
                                    <td>1</td>
                                    <td>6.7%</td>
                                    <td>3.500.000 đ</td>
                                    <td class="text-success"><strong>3.57</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa-solid fa-users source-icon" style="color:#28a745;"></i> CTV
                                    </td>
                                    <td>
                                        8.834.000 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 124.94%</span>
                                    </td>
                                    <td>
                                        9.450.000 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 122.89%</span>
                                    </td>
                                    <td>
                                        12
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 131.25%</span>
                                    </td>
                                    <td>53</td>
                                    <td>736.166 đ</td>
                                    <td>4.4</td>
                                    <td>1</td>
                                    <td>8.3%</td>
                                    <td>0 đ</td>
                                    <td class="text-success"><strong>N/A</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa-solid fa-shop source-icon" style="color:#EE4D2D;"></i> Shopee
                                    </td>
                                    <td>
                                        8.914.128 đ
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 25.24%</span>
                                    </td>
                                    <td>
                                        10.308.000 đ
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 17.21%</span>
                                    </td>
                                    <td>
                                        18
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 5.88%</span>
                                    </td>
                                    <td>40</td>
                                    <td>495.229 đ</td>
                                    <td>2.2</td>
                                    <td>1</td>
                                    <td>5.6%</td>
                                    <td>1.200.000 đ</td>
                                    <td class="text-success"><strong>7.42</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa-brands fa-wordpress-simple source-icon" style="color:#21759B;"></i> Woocommerce
                                    </td>
                                    <td>
                                        1.050.000 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 5.2%</span>
                                    </td>
                                    <td>
                                        1.050.000 đ
                                    </td>
                                    <td>
                                        1
                                    </td>
                                    <td>1</td>
                                    <td>1.050.000 đ</td>
                                    <td>1.0</td>
                                    <td>0</td>
                                    <td>0.0%</td>
                                    <td>150.000 đ</td>
                                    <td class="text-success"><strong>7.00</strong></td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa-solid fa-circle-question source-icon" style="color:#6c757d;"></i> Khác
                                    </td>
                                    <td>
                                        2.045.000 đ
                                        <span class="trend-indicator trend-down"><i class="fa-solid fa-arrow-down"></i> 10.5%</span>
                                    </td>
                                    <td>
                                        2.045.000 đ
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 144.91%</span>
                                    </td>
                                    <td>
                                        5
                                        <span class="trend-indicator trend-up"><i class="fa-solid fa-arrow-up"></i> 150%</span>
                                    </td>
                                    <td>8</td>
                                    <td>409.000 đ</td>
                                    <td>1.6</td>
                                    <td>0</td>
                                    <td>0.0%</td>
                                    <td>0 đ</td>
                                    <td class="text-success"><strong>N/A</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

    </div>
</div>
<?php init_tail(); ?>
</body>

</html>