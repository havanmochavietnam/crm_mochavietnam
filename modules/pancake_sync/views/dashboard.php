<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<!-- THEME & LAYOUT: GIỮ LIB CŨ, CHỈ CSS TÙY BIẾN -->
<style>
    :root {
        --bg: #f5f7fb;
        --card: #ffffff;
        --muted: #6b7280;
        --ink: #1f2937;
        --ink-2: #111827;
        --brand: #0ea5e9;
        /* xanh dương nhạt */
        --brand-ink: #0369a1;
        --ok: #22c55e;
        --warn: #f59e0b;
        --danger: #ef4444;
        --violet: #8b5cf6;
        --cyan: #06b6d4;
        --border: #e5e7eb;
        --soft: #f9fafb;
        --shadow: 0 8px 24px rgba(2, 6, 23, .06);
        --radius: 14px;
        --pad: 18px;
        --gap: 14px;
    }

    * {
        box-sizing: border-box;
    }

    html,
    body {
        overflow-x: hidden;
        background: var(--bg);
    }

    /* ====== WRAPPERS ====== */
    #wrapper {
        padding-bottom: 24px;
    }

    .section {
        margin-bottom: 28px;
    }

    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 8px 0 14px 0;
    }

    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--ink-2);
        letter-spacing: .2px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title .icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #eaf6ff;
        color: var(--brand-ink);
    }

    .section-sub {
        color: var(--muted);
        font-size: 13px;
    }

    /* ====== CARD ====== */
    .card {
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
    }

    .card.pad {
        padding: var(--pad);
    }

    .card+.card {
        margin-top: var(--gap);
    }

    /* ====== FORM FILTER ====== */
    .filter-row .form-group {
        margin-bottom: 10px;
    }

    .btn-primary {
        border-radius: 10px;
        padding: 9px 16px;
    }

    /* ====== METRIC ====== */
    .metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--gap);
    }

    .metric {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--soft);
        border: 1px solid var(--border);
        padding: 14px;
        border-radius: 12px;
    }

    .metric .ico {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .ico.green {
        background: #ecfdf5;
        color: #059669;
    }

    .ico.orange {
        background: #fff7ed;
        color: #c2410c;
    }

    .ico.blue {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .ico.purple {
        background: #f5f3ff;
        color: #6d28d9;
    }

    .metric .label {
        font-size: 12px;
        color: var(--muted);
        margin-bottom: 2px;
    }

    .metric .value {
        font-size: 22px;
        font-weight: 800;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum' 1, 'lnum' 1;
        line-height: 1.1;
    }

    /* ====== 3-COLUMN SUMMARY ====== */
    .triple {
        display: grid;
        gap: var(--gap);
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    @media (max-width: 992px) {
        .triple {
            grid-template-columns: 1fr;
        }
    }

    .summary {
        padding: var(--pad);
        border-radius: 12px;
        border: 1px solid var(--border);
        background: var(--card);
    }

    .summary .heading {
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--ink);
    }

    .summary .row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed var(--border);
    }

    .summary .row:last-child {
        border-bottom: 0;
    }

    .summary .label {
        color: var(--muted);
    }

    .summary .num {
        font-weight: 800;
        color: var(--ink-2);
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum' 1, 'lnum' 1;
    }

    /* ====== OVERVIEW GRID ====== */
    .overview {
        display: grid;
        gap: var(--gap);
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    }

    .kpi {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
    }

    .kpi h6 {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
        font-weight: 600;
    }

    .kpi .val {
        margin-top: 6px;
        font-size: 24px;
        font-weight: 800;
        color: var(--ink-2);
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum' 1, 'lnum' 1;
    }

    /* ====== SMALL KPIs ====== */
    .chips {
        display: grid;
        gap: var(--gap);
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }

    .chip {
        text-align: center;
        border: 1px solid var(--border);
        background: var(--soft);
        padding: 14px 10px;
        border-radius: 12px;
    }

    .chip p {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
    }

    .chip h4 {
        margin: 6px 0 0;
        font-size: 22px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum' 1, 'lnum' 1;
    }

    /* ====== BADGE ====== */
    .badge-clean {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #eef2ff;
        color: #3730a3;
        border: 1px solid #e0e7ff;
    }

    /* ====== TABLE ====== */
    .table-wrap {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    table.perf {
        margin: 0;
        background: var(--card);
    }

    .perf thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
        text-align: center;
        font-weight: 700;
        color: var(--ink);
    }

    .perf tbody td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 12px 10px;
        text-align: right;
    }

    .perf tbody td:first-child {
        text-align: left;
        font-weight: 700;
        color: var(--ink);
    }

    .perf tfoot td {
        background: #fcfcfd;
        font-weight: 800;
    }

    /* ICON ảnh nguồn đơn: đồng bộ, không đẩy layout */
    img.source-icon {
        width: 26px !important;
        height: 26px !important;
        object-fit: contain;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* ====== UTIL ====== */
    .money,
    .num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: 'tnum' 1, 'lnum' 1;
    }

    .text-ok {
        color: var(--ok);
    }

    .text-warn {
        color: var(--warn);
    }

    .text-danger {
        color: var(--danger);
    }
</style>

<style id="dashboard-spacing-fixes">
    /* Tạo khoảng hở 10px với sidebar + đều 2 bên */
    .dashboard-shell {
        margin-left: 10px;
        margin-right: 10px;
    }

    /* Khoảng hở phía trên nhẹ cho phần tiêu đề */
    .dashboard-shell .section:first-of-type {
        margin-top: 6px;
    }

    /* Canh đều “bộ lọc ngày” và nút Xem theo hàng ngang */
    .filter-row .row {
        display: flex;
        align-items: flex-end;
        /* kéo nút Xem xuống cùng baseline input */
        gap: 10px;
        /* tạo khe giữa các cột (bootstrap 3 không có gutter util) */
    }

    .filter-row .row>[class^="col-"] {
        float: none;
        /* để flex lo phần hàng, vẫn giữ width col- qua CSS sẵn có */
    }

    /* Giữ button không bị cao hơn/ lệch */
    .filter-row .btn-primary {
        margin: 9px;
        /* bỏ margin-top cũ nếu có */
        line-height: 1.2;
        /* tránh “đội” chiều cao */
    }

    /* Các ô KPI đồng chiều cao + không xô lệch */
    .metrics {
        align-items: stretch;
    }

    .metric {
        height: 100%;
    }

    /* Card/summary cao đều nhau */
    .triple {
        align-items: stretch;
    }

    .summary {
        height: 100%;
    }

    /* Bo đều khoảng cách cạnh trái/phải cho toàn bộ table */
    .table-wrap {
        margin-left: 2px;
        margin-right: 2px;
    }

    /* Mobile: khoảng hở nhỏ hơn để không “kẹt” màn hình bé */
    @media (max-width: 991px) {
        .dashboard-shell {
            margin-left: 6px;
            margin-right: 6px;
        }

        .filter-row .row {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>


<div id="wrapper">
    <?php if (!empty($error_message)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php
    /* ==== PHP PREP ==== */
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
    $statusKey = $order['status_name'] ?? 'new';
    $statusInfo = $statusMap[$statusKey] ?? ['text' => 'Không xác định', 'class' => 'tw-bg-gray-100 tw-text-gray-800'];

    // Các biến số liệu
    $sales_volume_confirmed = $sales_volume_confirmed ?? 0;
    $revenue_confirmed = $revenue_confirmed ?? 0;
    $count_confirmed = $count_confirmed ?? 0;
    $product_quantity_confirmed = $product_quantity_confirmed ?? 0;
    $avg_products_per_order = $avg_products_per_order ?? 0;
    $closing_rate = $closing_rate ?? 0;

    // Hôm nay
    $revenue_confirmed_today = $revenue_confirmed_today ?? 0;
    $count_confirmed_today = $count_confirmed_today ?? 0;
    $count_orders_new_today = $count_orders_new_today ?? 0;
    $count_orders_canceled_today = $count_orders_canceled_today ?? 0;
    $count_orders_removed_today = $count_orders_removed_today ?? 0;
    $get_product_quantity_confirmed_today = $get_product_quantity_confirmed_today ?? 0;
    $count_unique_customers_today = $count_unique_customers_today ?? 0;

    // Bán tại quầy
    $tai_quay_doanh_thu = $tai_quay_doanh_thu ?? 0;
    $tai_quay_don_chot = $tai_quay_don_chot ?? 0;

    // Gom tổng số lượng sản phẩm theo $daily_data
    $gross_product_count = 0;
    if (isset($daily_data) && is_array($daily_data)) {
        foreach ($daily_data as $day) {
            if (isset($day['success']) && is_array($day['success'])) {
                $gross_product_count += $day['success']['product_count'] ?? 0;
            }
        }
    }
    ?>

    <!-- ========== TỔNG QUAN ==========
       Header + Bộ lọc ngày + Badge trạng thái dự án -->
    <div class="dashboard-shell">
        <div class="section">
            <div class="section-head">
                <div>
                    <div class="section-title">
                        <span class="icon"><i class="fa-solid fa-chart-pie"></i></span>
                        Tổng quan
                    </div>
                    <div class="section-sub">Ảnh nhìn nhanh về doanh số, doanh thu và hiệu suất chốt.</div>
                </div>
                <span class="badge-clean">
                    <i class="fa-solid fa-circle-check"></i> Trạng thái đơn: <?= htmlspecialchars($statusInfo['text']); ?>
                </span>
            </div>


            <div class="card pad">
                <form class="filter-row" action="<?= admin_url('pancake_sync/pancake_dashboard'); ?>" method="get">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date"><strong>Từ ngày</strong></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date"><strong>Đến ngày</strong></label>
                                <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-2" style="display:flex;align-items:flex-end;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-filter mright5"></i> Xem
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Dòng metrics lớn -->
                <div class="metrics" style="margin-top:12px;">
                    <!-- Tổng hàng chốt -->
                    <div class="metric">
                        <div class="ico green"><i class="fa-solid fa-coins"></i></div>
                        <div>
                            <div class="label">Tổng hàng chốt (Tổng tiền)</div>
                            <div class="value money"><?= number_format($sales_volume_confirmed, 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="ico blue"><i class="fa-solid fa-boxes-stacked"></i></div>
                        <div>
                            <div class="label">Số lượng SP chốt</div>
                            <div class="value num"><?= number_format($gross_product_count, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="ico purple"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div>
                            <div class="label">Doanh thu</div>
                            <div class="value money"><?= number_format($revenue_confirmed, 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="ico orange"><i class="fa-solid fa-right-left"></i></div>
                        <div>
                            <div class="label">Tổng hàng hoàn (tiền)</div>
                            <div class="value money"><?= number_format($tong_hang_hoan_tien ?? 0, 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                </div>

                <!-- 3 cột tóm tắt -->
                <div class="triple" style="margin-top: var(--gap);">
                    <div class="summary">
                        <div class="heading"><i class="fa-solid fa-chart-line mright5"></i> Tổng cộng</div>
                        <div class="row"><span class="label">Doanh thu</span><span class="num money"><?= number_format($revenue_confirmed, 0, ',', '.'); ?> đ</span></div>
                        <div class="row"><span class="label" style="margin: -70px;">Đơn chốt</span><span class="num"><?= number_format($count_confirmed, 0, ',', '.'); ?></span></div>
                    </div>
                    <div class="summary">
                        <div class="heading"><i class="fa-solid fa-desktop mright5"></i> Online</div>
                        <div class="row"><span class="label">Doanh thu</span><span class="num money"><?= number_format($revenue_confirmed, 0, ',', '.'); ?> đ</span></div>
                        <div class="row"><span class="label" style="margin: -70px;">Đơn chốt</span><span class="num"><?= number_format($count_confirmed, 0, ',', '.'); ?></span></div>
                    </div>
                    <div class="summary">
                        <div class="heading"><i class="fa-solid fa-store mright5"></i> Bán tại quầy</div>
                        <div class="row"><span class="label">Doanh thu</span><span class="num money"><?= number_format($tai_quay_doanh_thu ?? 0, 0, ',', '.'); ?> đ</span></div>
                        <div class="row"><span class="label">Đơn chốt</span><span class="num"><?= number_format($tai_quay_don_chot ?? 0, 0, ',', '.'); ?></span></div>
                    </div>
                </div>

                <!-- KPIs tổng hợp -->
                <div class="overview" style="margin-top: var(--gap);">
                    <div class="kpi">
                        <h6>Doanh số</h6>
                        <div class="val money"><?= number_format($sales_volume_confirmed ?? 0, 0, ',', '.'); ?> đ</div>
                    </div>
                    <div class="kpi">
                        <h6>Doanh thu</h6>
                        <div class="val money"><?= number_format($revenue_confirmed ?? 0, 0, ',', '.'); ?> đ</div>
                    </div>
                    <div class="kpi">
                        <h6>Số đơn chốt</h6>
                        <div class="val num"><?= number_format($count_confirmed ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="kpi">
                        <h6>GTTB</h6>
                        <div class="val money"><?= number_format($aov_confirmed ?? 0, 0, ',', '.'); ?> đ</div>
                    </div>
                </div>

                <div class="overview" style="margin-top: var(--gap);">
                    <div class="kpi">
                        <h6>SL Sản phẩm</h6>
                        <div class="val num"><?= number_format($product_quantity_confirmed ?? 0, 0, ',', '.'); ?></div>
                    </div>
                    <div class="kpi">
                        <h6>SL SPTB</h6>
                        <div class="val num"><?= number_format($avg_products_per_order ?? 0, 2, ',', '.'); ?></div>
                    </div>
                    <div class="kpi">
                        <h6>Chi phí quảng cáo</h6>
                        <div class="val money"><?= number_format($don_hang_huy ?? 0, 0, ',', '.'); ?> đ</div>
                    </div>
                    <div class="kpi">
                        <h6>Tỷ lệ chốt</h6>
                        <div class="val num"><?= number_format($closing_rate ?? 0, 2, ',', '.'); ?>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== PHÂN TÍCH TRONG NGÀY ========== -->
    <div class="dashboard-shell">
        <div class="section">
            <div class="section-head">
                <div class="section-title">
                    <span class="icon"><i class="fa-solid fa-bolt"></i></span>
                    Phân tích trong ngày
                </div>
                <div class="section-sub">Cập nhật realtime theo giờ (giữ nguyên hook chart cũ của bạn).</div>
            </div>

            <div class="card pad">
                <div class="metrics">
                    <div class="metric">
                        <div class="ico green"><i class="fa-solid fa-sack-dollar"></i></div>
                        <div>
                            <div class="label">Doanh thu hôm nay</div>
                            <div class="value money"><?= number_format($revenue_confirmed_today ?? 0, 0, ',', '.'); ?> đ</div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="ico blue"><i class="fa-solid fa-clipboard-check"></i></div>
                        <div>
                            <div class="label">Đơn chốt hôm nay</div>
                            <div class="value num"><?= number_format($count_confirmed_today ?? 0, 0, ',', '.'); ?></div>
                        </div>
                    </div>
                </div>

                <div id="hourly-revenue-chart" style="margin-top: 14px;"></div>

                <div class="metrics" style="margin-top: 10px;">
                    <div class="metric">
                        <div class="ico purple"><i class="fa-solid fa-store"></i></div>
                        <div>
                            <div class="label">Bán tại quầy · Doanh thu</div>
                            <div class="value money"><?= number_format($tai_quay_doanh_thu ?? 0, 0, ',', '.'); ?> đ</div>
                            <div class="section-sub">Đơn chốt: <b class="num"><?= number_format($tai_quay_don_chot ?? 0, 0, ',', '.'); ?></b></div>
                        </div>
                    </div>
                    <div class="metric">
                        <div class="ico cyan"><i class="fa-solid fa-globe"></i></div>
                        <div>
                            <div class="label">Online · Doanh thu</div>
                            <div class="value money"><?= number_format($revenue_confirmed_today ?? 0, 0, ',', '.'); ?> đ</div>
                            <div class="section-sub">Đơn chốt: <b class="num"><?= number_format($count_confirmed_today ?? 0, 0, ',', '.'); ?></b></div>
                        </div>
                    </div>
                </div>

                <div class="chips" style="margin-top: 10px;">
                    <div class="chip">
                        <p>Đơn tạo mới</p>
                        <h4 class="text-ok"><?= number_format($count_orders_new_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="chip">
                        <p>Đơn hủy</p>
                        <h4 class="text-danger"><?= number_format($count_orders_canceled_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="chip">
                        <p>Đơn chốt</p>
                        <h4 class="text-ok"><?= number_format($count_confirmed_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="chip">
                        <p>Đơn xoá</p>
                        <h4><?= number_format($count_orders_removed_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="chip">
                        <p>Hàng bán ra</p>
                        <h4><?= number_format($get_product_quantity_confirmed_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                    <div class="chip">
                        <p>Khách hàng</p>
                        <h4><?= number_format($count_unique_customers_today ?? 0, 0, ',', '.'); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== NGUỒN ĐƠN ========== -->
    <div class="dashboard-shell">
        <div class="section">
            <div class="section-head">
                <div class="section-title">
                    <span class="icon"><i class="fa-solid fa-bullseye"></i></span>
                    Hiệu suất theo nguồn đơn
                </div>
                <div class="section-sub">So sánh doanh thu, doanh số, chiết khấu, đơn chốt, GTTB…</div>
            </div>

            <div class="card pad" style="padding:0;">
                <div class="table-wrap">
                    <table class="table table-hover perf">
                        <thead>
                            <tr>
                                <th style="min-width:220px; text-align:left;">Nguồn đơn</th>
                                <th>Doanh thu</th>
                                <th>Doanh số</th>
                                <th>Chiết khấu</th>
                                <th>Đơn chốt</th>
                                <th>SL hàng chốt</th>
                                <th>GTTB</th>
                                <th>SL KH mới</th>
                                <th>SL KH cũ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- CTV -->
                            <tr>
                                <td><i class="fa-solid fa-users" style="color:#16a34a; margin-right:8px;"></i> CTV</td>
                                <td><?= number_format($get_revenue_of_affiliate_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_ctv_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_ctv_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_ctv_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_ctv_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_ctv_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_affiliate ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_affiliate ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- Facebook -->
                            <tr>
                                <td>
                                    <img src="https://www.freeiconspng.com/uploads/facebook-png-icon-follow-us-facebook-1.png" alt="Facebook" class="source-icon">
                                    Facebook
                                </td>
                                <td><?= number_format($get_revenue_of_facebook_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_facebook_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_facebook_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_facebook_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_facebook_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_facebook_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_facebook ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_facebook ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- Shopee -->
                            <tr>
                                <td>
                                    <img src="https://classic.vn/wp-content/uploads/2022/04/logo-shopee-764x800.png" alt="Shopee" class="source-icon">
                                    Shopee
                                </td>
                                <td><?= number_format($get_revenue_of_shopee_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_shopee_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_shopee_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_shopee_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_Shopee_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_shopee_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_shopee ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_shopee ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- Zalo -->
                            <tr>
                                <td>
                                    <img src="https://hidosport.vn/wp-content/uploads/2023/09/zalo-icon.png" alt="Zalo" class="source-icon">
                                    Zalo
                                </td>
                                <td><?= number_format($get_revenue_of_zalo_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_zalo_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_zalo_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_zalo_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_zalo_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_zalo_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_zalo ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_zalo ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- TikTok -->
                            <tr>
                                <td>
                                    <img src="https://cdn-icons-png.flaticon.com/512/3116/3116491.png" alt="TikTok" class="source-icon">
                                    Tiktok
                                </td>
                                <td><?= number_format($get_revenue_of_tiktok_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_tiktok_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_tiktok_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_tiktok_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_tiktok_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_tiktok_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_tiktok ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_tiktok ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- WooCommerce -->
                            <tr>
                                <td>
                                    <img src="https://retailexpress.com.au/hs-fs/hubfs/Retail%20Express/Integrations/60eef77f10da6dae6d5793a8_Group%201998.png?width=286&name=60eef77f10da6dae6d5793a8_Group%201998.png" alt="WooCommerce" class="source-icon">
                                    Woocommerce
                                </td>
                                <td><?= number_format($get_revenue_of_woocommerce_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_woocommerce_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_woocommerce_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_woocommerce_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_woocommerce_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_woocommerce_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_woocommerce ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_woocommerce ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- Hotline -->
                            <tr>
                                <td>
                                    <img src="https://static.vecteezy.com/system/resources/previews/036/269/966/non_2x/phone-call-icon-answer-accept-call-icon-with-green-button-contact-us-telephone-sign-yes-button-incoming-call-icon-vector.jpg" alt="Hotline" class="source-icon">
                                    Hotline
                                </td>
                                <td><?= number_format($get_revenue_of_hotline_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_hotline_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_hotline_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_hotline_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_hotline_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_hotline_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_hotline ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_hotline ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- LadiPage -->
                            <tr>
                                <td>
                                    <img src="https://helpv3.ladipage.vn/~gitbook/image?url=https%3A%2F%2F4163873660-files.gitbook.io%2F%7E%2Ffiles%2Fv0%2Fb%2Fgitbook-x-prod.appspot.com%2Fo%2Fspaces%252FVlMUbaIjYt7SY2R8v2az%252Ficon%252Fhdr8lmBqrumyQd8I249v%252Flogo-white.svg%3Falt%3Dmedia%26token%3Df2464a07-be53-4fb1-bf06-a0c2c6b6a9a7&width=32&dpr=4&quality=100&sign=407bcf19&sv=2" alt="LadiPage" class="source-icon">
                                    LadiPage
                                </td>
                                <td><?= number_format($get_revenue_of_ladipage_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_ladipage_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_ladipage_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_hotline_ladipage_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_ladipage_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_ladipage_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_ladipage ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_ladipage ?? 0, 0, ',', '.'); ?></td>
                            </tr>

                            <!-- Khác -->
                            <tr>
                                <td><i class="fa-solid fa-circle-question" style="color:#64748b; margin-right:8px;"></i> Khác</td>
                                <td><?= number_format($get_revenue_of_others_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_sale_of_others_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($get_discount_of_others_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($count_others_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_product_others_quantity_of_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($get_aov_of_others_orders_confirmed_in_range ?? 0, 0, ',', '.'); ?> đ</td>
                                <td><?= number_format($cust_new_others ?? 0, 0, ',', '.'); ?></td>
                                <td><?= number_format($cust_returning_others ?? 0, 0, ',', '.'); ?></td>
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