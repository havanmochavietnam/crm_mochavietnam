<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    /* Tailwind CSS v3.4.1 | MIT License | https://tailwindcss.com */
    *,
    ::before,
    ::after {
        box-sizing: border-box;
        border-width: 0;
        border-style: solid;
        border-color: #e5e7eb
    }

    html {
        line-height: 1.5;
        -webkit-text-size-adjust: 100%;
        -moz-tab-size: 4;
        tab-size: 4;
        font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
        font-feature-settings: normal;
        font-variation-settings: normal;
        -webkit-tap-highlight-color: transparent
    }

    body {
        margin: 0;
        line-height: inherit
    }

    h4,
    p,
    table,
    thead,
    tbody,
    tr,
    th,
    td,
    button,
    pre {
        margin: 0;
        padding: 0
    }

    table {
        text-indent: 0;
        border-color: inherit;
        border-collapse: collapse
    }

    button {
        -webkit-appearance: button;
        background-color: transparent;
        background-image: none;
        cursor: pointer
    }

    .tw-p-4 {
        padding: 1rem
    }

    .tw-p-8 {
        padding: 2rem
    }

    .tw-px-2 {
        padding-left: .5rem;
        padding-right: .5rem
    }

    .tw-px-4 {
        padding-left: 1rem;
        padding-right: 1rem
    }

    .tw-px-6 {
        padding-left: 1.5rem;
        padding-right: 1.5rem
    }

    .tw-py-1 {
        padding-top: .25rem;
        padding-bottom: .25rem
    }

    .tw-py-3 {
        padding-top: .75rem;
        padding-bottom: .75rem
    }

    .tw-py-4 {
        padding-top: 1rem;
        padding-bottom: 1rem
    }

    .tw-pb-2 {
        padding-bottom: .5rem
    }

    .tw-pt-2 {
        padding-top: .5rem
    }

    .tw-space-y-4> :not([hidden])~ :not([hidden]) {
        --tw-space-y-reverse: 0;
        margin-top: calc(1rem * calc(1 - var(--tw-space-y-reverse)));
        margin-bottom: calc(1rem * var(--tw-space-y-reverse))
    }

    .tw-space-y-6> :not([hidden])~ :not([hidden]) {
        --tw-space-y-reverse: 0;
        margin-top: calc(1.5rem * calc(1 - var(--tw-space-y-reverse)));
        margin-bottom: calc(1.5rem * var(--tw-space-y-reverse))
    }

    .tw-overflow-hidden {
        overflow: hidden
    }

    .tw-overflow-x-auto {
        overflow-x: auto
    }

    .tw-rounded-lg {
        border-radius: .5rem
    }

    .tw-rounded-md {
        border-radius: .375rem
    }

    .tw-rounded-full {
        border-radius: 9999px
    }

    /* Các lớp border chính, sử dụng border-width: 1px và màu gray-300 cho rõ nét */
    .tw-border {
        border-width: 1px;
        border-color: #d1d5db;
    }

    /* gray-300 */
    .tw-border-b {
        border-bottom-width: 1px;
        border-color: #d1d5db;
    }

    .tw-border-t {
        border-top-width: 1px;
        border-color: #d1d5db;
    }

    .tw-border-r {
        border-right-width: 1px;
        border-color: #d1d5db;
    }

    .tw-border-l {
        border-left-width: 1px;
        border-color: #d1d5db;
    }

    .tw-border-gray-200 {
        border-color: #e5e7eb
    }

    /* Giữ lại cho các trường hợp đặc biệt */
    .tw-bg-white {
        background-color: #fff
    }

    .tw-bg-gray-50 {
        background-color: #f9fafb
    }

    .tw-bg-gray-100 {
        background-color: #f3f4f6
    }

    .tw-bg-gray-200 {
        background-color: #e5e7eb
    }

    .tw-bg-gray-800 {
        background-color: #1f2937
    }

    .tw-bg-blue-100 {
        background-color: #dbeafe
    }

    .tw-bg-yellow-100 {
        background-color: #fef9c3
    }

    .tw-bg-indigo-100 {
        background-color: #e0e7ff
    }

    .tw-bg-purple-100 {
        background-color: #f3e8ff
    }

    .tw-bg-cyan-100 {
        background-color: #cffafe
    }

    .tw-bg-green-100 {
        background-color: #dcfce7
    }

    .tw-bg-orange-100 {
        background-color: #ffedd5
    }

    .tw-bg-lime-100 {
        background-color: #ecfccb
    }

    .tw-bg-red-100 {
        background-color: #fee2e2
    }

    .tw-shadow-md {
        --tw-shadow: 0 4px 6px -1px rgb(0 0 0 / .1), 0 2px 4px -2px rgb(0 0 0 / .1);
        --tw-shadow-colored: 0 4px 6px -1px var(--tw-shadow-color), 0 2px 4px -2px var(--tw-shadow-color);
        box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow)
    }

    .tw-text-left {
        text-align: left
    }

    .tw-text-center {
        text-align: center
    }

    .tw-text-right {
        text-align: right
    }

    .tw-text-xs {
        font-size: .75rem;
        line-height: 1rem
    }

    .tw-text-sm {
        font-size: .875rem;
        line-height: 1.25rem
    }

    .tw-text-lg {
        font-size: 1.125rem;
        line-height: 1.75rem
    }

    .tw-text-2xl {
        font-size: 1.5rem;
        line-height: 2rem
    }

    .tw-font-bold {
        font-weight: 700
    }

    .tw-font-medium {
        font-weight: 500
    }

    .tw-font-semibold {
        font-weight: 600
    }

    .tw-uppercase {
        text-transform: uppercase
    }

    .tw-text-white {
        color: #fff
    }

    .tw-text-gray-500 {
        color: #6b7280
    }

    .tw-text-gray-600 {
        color: #4b5563
    }

    .tw-text-gray-700 {
        color: #374151
    }

    .tw-text-gray-800 {
        color: #1f2937
    }

    .tw-text-black {
        color: #000
    }

    .tw-text-blue-600 {
        color: #2563eb
    }

    .tw-text-blue-800 {
        color: #1e40af
    }

    .tw-text-yellow-800 {
        color: #854d0e
    }

    .tw-text-indigo-800 {
        color: #3730a3
    }

    .tw-text-purple-800 {
        color: #6b21a8
    }

    .tw-text-cyan-800 {
        color: #155e75
    }

    .tw-text-green-800 {
        color: #166534
    }

    .tw-text-orange-800 {
        color: #9a3412
    }

    .tw-text-lime-800 {
        color: #3f6212
    }

    .tw-text-red-800 {
        color: #991b1b
    }

    .hover\:tw-bg-gray-50:hover {
        background-color: #f9fafb
    }

    .hover\:tw-text-black:hover {
        color: #000
    }

    .hover\:tw-text-blue-800:hover {
        color: #1e3a8a
    }

    .tw-mb-2 {
        margin-bottom: .5rem
    }

    .tw-mb-6 {
        margin-bottom: 1.5rem
    }

    .tw-mt-1 {
        margin-top: .25rem
    }

    .tw-mt-2 {
        margin-top: .5rem
    }

    .tw-ml-2 {
        margin-left: .5rem
    }

    .tw-w-full {
        width: 100%
    }

    .tw-min-w-max {
        min-width: max-content
    }

    .tw-max-w-xs {
        max-width: 20rem
    }

    .tw-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap
    }

    .tw-whitespace-nowrap {
        white-space: nowrap
    }

    .tw-grid {
        display: grid
    }

    .md\:tw-grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr))
    }

    .tw-gap-6 {
        gap: 1.5rem
    }

    .tw-inline-block {
        display: inline-block
    }

    .align-middle {
        vertical-align: middle
    }

    pre {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        white-space: pre-wrap
    }

    /* Thêm style mới cho thanh tìm kiếm và phân trang */
    .search-filter-container {
        background-color: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .search-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .form-group {
        margin-bottom: 0;
    }
    
    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #374151;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-checkbox {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
        margin-top: 1.5rem;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background-color: #2563eb;
        color: white;
        border: none;
    }
    
    .btn-primary:hover {
        background-color: #1d4ed8;
    }
    
    .btn-outline {
        background-color: transparent;
        border: 1px solid #d1d5db;
        color: #374151;
    }
    
    .btn-outline:hover {
        background-color: #f9fafb;
    }
    
    .filter-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        grid-column: 1 / -1;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 1.5rem;
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .page-item {
        margin: 0 0.25rem;
    }
    
    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        color: #374151;
        text-decoration: none;
        font-size: 0.875rem;
    }
    
    .page-link:hover {
        background-color: #f3f4f6;
    }
    
    .page-item.active .page-link {
        background-color: #2563eb;
        color: white;
        border-color: #2563eb;
    }
    
    .page-item.disabled .page-link {
        opacity: 0.5;
        pointer-events: none;
    }
    
    .results-count {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 1rem;
        grid-column: 1 / -1;
    }
    
    /* Bảng đẹp hơn: header dính, zebra rows, hover và cuộn mềm mại */
    .table-container {
        max-height: 70vh;
        overflow: auto;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }

    table thead th {
        position: sticky;
        top: 0;
        background-color: #f9fafb;
        z-index: 2;
    }

    table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    table tbody tr:hover {
        background-color: #f3f4f6;
    }

    table th, table td {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>
<div id="wrapper">
    <div class="content">
        <div class="tw-p-4 sm:tw-p-6 lg:tw-p-8">
            <!-- Thanh tìm kiếm -->
            <div class="search-filter-container">
                <h4 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Tìm kiếm Đơn hàng</h4>
                
                <form method="GET" action="<?= admin_url('pancake_sync') ?>" class="search-form">
                    <div class="form-group">
                        <label class="form-label">Tìm kiếm (SĐT, tên KH, ghi chú)</label>
                        <input type="text" name="search" value="<?= html_escape($this->input->get('search')) ?>" 
                               class="form-input" placeholder="Nhập SĐT, tên KH hoặc ghi chú...">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Trạng thái đơn hàng</label>
                        <select name="filter_status[]" multiple class="form-select" style="height: 120px">
                            <?php
                            $statusOptions = [
                                'new' => 'Mới',
                                'wait_submit' => 'Chờ xác nhận',
                                'submitted' => 'Đã xác nhận',
                                'packing' => 'Đang đóng hàng',
                                'shipped' => 'Đã gửi hàng',
                                'delivered' => 'Đã nhận',
                                'returning' => 'Đang hoàn',
                                'returned' => 'Đã hoàn',
                                'canceled' => 'Đã huỷ',
                                'removed' => 'Đã xoá'
                            ];
                            
                            $selectedStatuses = $this->input->get('filter_status') ?: [];
                            foreach ($statusOptions as $value => $label) {
                                $selected = in_array($value, $selectedStatuses) ? 'selected' : '';
                                echo "<option value=\"$value\" $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="datetime-local" name="startDateTime" 
                               value="<?= html_escape($this->input->get('startDateTime')) ?>" 
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="datetime-local" name="endDateTime" 
                               value="<?= html_escape($this->input->get('endDateTime')) ?>" 
                               class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Sắp xếp theo</label>
                        <select name="updateStatus" class="form-select">
                            <option value="inserted_at" <?= ($this->input->get('updateStatus') === 'inserted_at') ? 'selected' : '' ?>>Ngày tạo</option>
                            <option value="updated_at" <?= ($this->input->get('updateStatus') === 'updated_at') ? 'selected' : '' ?>>Ngày cập nhật</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Số lượng / trang</label>
                        <select name="page_size" class="form-select">
                            <option value="10" <?= ($this->input->get('page_size') == 10) ? 'selected' : '' ?>>10</option>
                            <option value="30" <?= ($this->input->get('page_size') == 30 || !$this->input->get('page_size')) ? 'selected' : '' ?>>30</option>
                            <option value="50" <?= ($this->input->get('page_size') == 50) ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= ($this->input->get('page_size') == 100) ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" id="include_removed" name="include_removed" value="1" 
                               class="form-checkbox" <?= $this->input->get('include_removed') ? 'checked' : '' ?>>
                        <label for="include_removed" class="form-label">Bao gồm đơn đã xóa</label>
                    </div>
                    
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="<?= admin_url('pancake_sync') ?>" class="btn btn-outline">Đặt lại</a>
                    </div>
                </form>
                
                <?php if (isset($total)): ?>
                <div class="results-count">
                    Tìm thấy <?= $total ?> kết quả
                    <?php if ($this->input->get('search')): ?>
                    cho từ khóa "<strong><?= html_escape($this->input->get('search')) ?></strong>"
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="tw-mb-6">
                <h4 class="tw-text-2xl tw-font-bold tw-text-gray-800">
                    Danh sách Đơn hàng
                    <span class="tw-ml-2 tw-text-lg tw-font-medium tw-text-gray-500">(Tổng cộng: <?= $total ?? 0 ?>)</span>
                </h4>
                <p class="tw-text-gray-600 tw-mt-1">Quản lý và theo dõi tất cả các đơn hàng tại đây.</p>
            </div>

            <div class="tw-bg-white tw-shadow-md tw-rounded-lg tw-overflow-hidden">
                <div class="tw-overflow-x-auto table-container">
                    <table class="tw-w-full tw-min-w-max tw-text-sm tw-text-left tw-text-gray-700 tw-border tw-border-gray-300">
                        <thead class="tw-text-xs tw-text-gray-800 tw-uppercase tw-bg-gray-50">
                            <tr>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">STT</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Mã Đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ngày Tạo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Mã vận đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thẻ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Khách Hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số điện thoại</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nhà mạng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Khách Mới / Cũ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Gồm Sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số lượng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số lượng đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">SL đơn hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phường/Xã</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Quận/Huyện</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tỉnh/TP</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Người xử lý</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nhân viên CSKH</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Marketer</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Trạng thái</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Page Id</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ad Id</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nguồn quảng cáo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nguồn đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nguồn chi tiết</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Chat page</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thời gian khách nhắn tin đầu tiên đến page</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Người tạo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">NV xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nhân viên đầu tiên xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Nhân viên cập nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thời gian CSKH</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ngày đẩy đơn sang đvvc (Ngày tháng)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Lý do hoàn/hủy đơn hàng từ ĐVVC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Lý do hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">COD</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí trả cho đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">COD đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng tiền đơn hàng (trừ chiết khấu)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng tiền đơn hàng (trừ phí ship)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Doanh số</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Doanh số trước hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Doanh thu đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Doanh thu chưa trừ phí sàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phụ thu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Giảm giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí sàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Giảm giá trực tiếp trên đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Trị giá đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Trị giá đơn hàng đã chiết khấu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí VC thu của khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng tiền đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thực thu từ đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng phí đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền trả trước</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Chuyển trước</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền khách đưa</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền mặt</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Quẹt thẻ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">MoMo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">VNPAY</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">ONEPAY</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">QRPay</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số lượng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng Tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">Hành động</th>
                            </tr>
                        </thead>
                        <?php if (!empty($orders)): ?>
                            <?php
                            $statusMap = [
                                'new'         => ['text' => 'Mới', 'class' => 'tw-bg-blue-100 tw-text-blue-800'],
                                'wait_submit' => ['text' => 'Chờ xác nhận', 'class' => 'tw-bg-yellow-100 tw-text-yellow-800'],
                                'submitted'   => ['text' => 'Đã xác nhận', 'class' => 'tw-bg-indigo-100 tw-text-indigo-800'],
                                'packing'     => ['text' => 'Đang đóng hàng', 'class' => 'tw-bg-purple-100 tw-text-purple-800'],
                                'shipped'     => ['text' => 'Đã gửi hàng', 'class' => 'tw-bg-cyan-100 tw-text-cyan-800'],
                                'delivered'   => ['text' => 'Đã nhận', 'class' => 'tw-bg-green-100 tw-text-green-800'],
                                'returning'   => ['text' => 'Đang hoàn', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
                                'returned'    => ['text' => 'Đã hoàn', 'class' => 'tw-bg-lime-100 tw-text-lime-800'],
                                'canceled'    => ['text' => 'Đã huỷ', 'class' => 'tw-bg-red-100 tw-text-red-800'],
                                'removed'     => ['text' => 'Đã xoá', 'class' => 'tw-bg-gray-100 tw-text-gray-800'],
                            ];
                            ?>
                            <?php
                            if (!function_exists('get_mobile_network')) {
                                /**
                                 * Xác định nhà mạng dựa trên đầu số điện thoại.
                                 * @param string $phoneNumber Số điện thoại cần kiểm tra.
                                 * @return string Tên nhà mạng hoặc "Không xác định".
                                 */
                                function get_mobile_network($phoneNumber)
                                {
                                    if (empty($phoneNumber)) {
                                        return '';
                                    }

                                    // Chuẩn hóa số điện thoại về dạng 10 số, bắt đầu bằng 0
                                    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                                    if (substr($phoneNumber, 0, 2) == '84') {
                                        $phoneNumber = '0' . substr($phoneNumber, 2);
                                    }
                                    if (strlen($phoneNumber) != 10) {
                                        return '';
                                    }

                                    // Lấy 3 chữ số đầu tiên làm đầu số
                                    $prefix = substr($phoneNumber, 0, 3);

                                    // Danh sách các đầu số của nhà mạng tại Việt Nam
                                    $networks = [
                                        'Viettel' => ['086', '096', '097', '098', '032', '033', '034', '035', '036', '037', '038', '039'],
                                        'MobiFone' => ['089', '090', '093', '070', '079', '077', '076', '078'],
                                        'VinaPhone' => ['088', '091', '094', '083', '084', '085', '081', '082'],
                                        'Vietnamobile' => ['092', '056', '058'],
                                        'Gmobile' => ['099', '059'],
                                        'Itelecom' => ['087']
                                    ];

                                    // Lặp qua danh sách để tìm nhà mạng
                                    foreach ($networks as $networkName => $prefixes) {
                                        if (in_array($prefix, $prefixes)) {
                                            return $networkName;
                                        }
                                    }

                                    return '';
                                }
                            }
                            ?>
                            <?php foreach ($orders as $index => $order): ?>
                                <?php
                                $items = $order['items'] ?? ($order['products'] ?? []);
                                $itemsCount = !empty($items) ? count($items) : 1;
                                $firstItem = $items[0] ?? null;
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
                                ?>
                                <tbody x-data="{ open: false }">
                                    <tr class="hover:tw-bg-gray-50">
                                        <!-- STT  -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= $index + 1 ?></td>
                                        <!-- Mã đơn  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['id'] ?? '') ?></td>
                                        <!-- Ngày tạo  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= isset($order['inserted_at']) ? date('d/m/Y H:i', strtotime($order['inserted_at'])) : 'N/A' ?></td>
                                        <!-- Mã vận đơn  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['partner']['extend_code'] ?? '') ?></td>
                                        <!-- Thẻ  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">
                                            <?= !empty($order['tags']) ? html_escape(implode(', ', array_column($order['tags'], 'name'))) : '' ?>
                                        </td>
                                        <!-- Khách hàng  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['name'] ?? '') ?></td>
                                        <!-- Số điện thoại  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">
                                            <div><?php $phone = $order['customer']['phone_numbers'][0] ?? '';
                                                    echo html_escape($phone); ?> </div>
                                            <!-- Nhà mạng  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $phone = $order['customer']['phone_numbers'][0] ?? '';
                                            $carrier = get_mobile_network($phone);
                                            echo html_escape($carrier);
                                            ?>
                                        </td>
                                        <!-- Khách Mới / Cũ  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= '' ?></td>
                                        <!-- Gồm sản phẩm  -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle" title="<?= html_escape($firstItem['variation_info']['name'] ?? 'N/A') ?>">
                                            <?= html_escape($firstItem['variation_info']['name'] ?? '') ?>
                                        </td>
                                        <!-- Số lượng  -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle">
                                            <?= $firstItem['quantity'] ?? 0 ?>
                                        </td>
                                        <!-- Số lượng đổi  -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle">
                                            <?= html_escape($firstItem['exchange_count'] ?? 0) ?>
                                        </td>
                                        <!-- SL đơn hoàn  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['returned_order_count'] ?? 0) ?></td>
                                        <!-- Phường Xã  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['commune_name'] ?? '') ?></td>
                                        <!-- Quận Huyện  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['district_name'] ?? '') ?></td>
                                        <!-- Tỉnh / Thành Phố  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['province_name'] ?? '') ?></td>
                                        <!-- Người xử lý  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></td>
                                        <!-- Nhân viên CSKH  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['assigning_care']['name'] ?? '') ?></td>
                                        <!-- Marketer  -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['marketer']['name'] ?? '') ?></td>
                                        <!-- Trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">
                                            <span class="tw-inline-block tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-rounded-full <?= $statusInfo['class'] ?>">
                                                <?= $statusInfo['text'] ?>
                                            </span>
                                        </td>
                                        <!-- Page ID -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['page_id'] ?? '') ?></td>
                                        <!-- Ad ID -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['ad_id'] ?? '') ?></td>
                                        <!-- Nguồn quảng cáo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['ads_source'] ?? '') ?></td>
                                        <!-- Nguồn Đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['order_sources_name'] ?? '') ?></td>
                                        <!-- Nguồn chi tiết -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['account_name'] ?? '') ?></td>
                                        <!-- Chat Page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['page']['name'] ?? '') ?></td>
                                        <!-- Thời gian khách nhắn tin đầu tiên đến page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape('') ?></td>
                                        <!-- Người tạo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['creator']['name'] ?? 'Hệ thống') ?></td>
                                        <!-- NV xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['last_editor']['name'] ?? '') ?></td>
                                        <!-- Nhân viên đầu tiên xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['last_editor']['name'] ?? '') ?></td>
                                        <!-- Nhân viên cập nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['creator']['name'] ?? 'Hệ thống') ?></td>
                                        <!-- Thời gian CSKH -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= isset($order['time_assign_care']) ? date('d/m/Y H:i', strtotime($order['time_assign_care'])) : '' ?></td>
                                        <!-- Đơn vị VC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['partner']['partner_name'] ?? '') ?></td>
                                        <!-- Ngày đẩy đơn sang đvvc (Ngày tháng) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= isset($order['time_send_partner']) ? date('d/m', strtotime($order['time_send_partner'])) : '' ?></td>
                                        <!-- Lý do hoàn/hủy đơn hàng từ ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['extend_update']['note'] ?? '') ?></td>
                                        <!-- Lý do hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['returnedreason'] ?? '') ?></td>
                                        <!-- COD -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($cod) ?></td>
                                        <!-- Phí trả cho đơn vị VC -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($partner_fee) ?></td>
                                        <!-- COD đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($codDoiSoat) ?></td>
                                        <!-- Tổng tiền đơn hàng (trừ chiết khấu) -->
                                        <td class="tw-px-6 tw-py-4 text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $total = 0;
                                            foreach ($order['items'] as $item) {
                                                $price = ($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0);
                                                $discount = $item['total_discount'] ?? 0;
                                                $total += $price - $discount;
                                            }
                                            echo number_format($total, 0, ',', '.');
                                            ?>
                                        </td>
                                        <!-- Tổng tiền đơn hàng (trừ phí ship) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice - $shipping_fee) ?>
                                        </td>
                                        <!-- Doanh số -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($totalPrice ?? 0)) ?>
                                        </td>
                                        <!-- Doanh số trước hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($totalPrice ?? 0)) ?>
                                        </td>
                                        <!-- Doanh thu đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($total_price_after_sub_discount ?? 0)) ?>
                                        </td>
                                        <!-- Doanh thu chưa trừ phí sàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?=
                                            number_format(
                                                array_reduce($order['items'] ?? [], function ($total, $item) {
                                                    $price = ($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0);
                                                    $discount = $item['total_discount'] ?? 0;
                                                    return $total + ($price - $discount);
                                                }, 0),
                                                0,
                                                ',',
                                                '.'
                                            )
                                            ?>
                                        </td>
                                        <!-- Phụ thu -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($surcharge) ?>
                                        </td>
                                        <!-- Giảm giá -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?=
                                            number_format(
                                                // Dùng array_reduce để cộng dồn giá trị 'total_discount' của mỗi sản phẩm
                                                array_reduce($order['items'] ?? [], fn($total, $item) => $total + ($item['total_discount'] ?? 0), 0),
                                                0,
                                                ',',
                                                '.'
                                            )
                                            ?>
                                        </td>
                                        <!-- Phí sàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($fee_marketplace) ?>
                                        </td>
                                        <!-- Giảm giá trực tiếp trên đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_discount) ?>
                                        </td>
                                        <!-- Trị giá đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Trị giá đơn hàng đã chiết khấu -->
                                        <td class="tw-px-6 tw-py-4 text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $total = 0;
                                            foreach ($order['items'] as $item) {
                                                $price = ($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0);
                                                $discount = $item['total_discount'] ?? 0;
                                                $total += $price - $discount;
                                            }
                                            echo number_format($total, 0, ',', '.');
                                            ?>
                                        </td>
                                        <!-- Phí VC thu của khách -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($shipping_fee) ?>
                                        </td>
                                        <!-- Tổng tiền đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Thực thu từ đơn vị vận chuyển -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($money_to_collect) ?>
                                        </td>
                                        <!-- Tổng phí đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"
                                            rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_partner) ?>
                                        </td>

                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle"><?= $firstItem['quantity'] ?? 0 ?></td>
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['creator']['name'] ?? '') ?></td>
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= number_format($totalPrice) ?> đ</td>
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">
                                            <button @click="open = !open" class="tw-text-blue-600 hover:tw-text-blue-800 tw-font-medium">
                                                <span x-show="!open">Xem chi tiết</span>
                                                <span x-show="open">Thu gọn</span>
                                            </button>
                                        </td>
                                    </tr>

                                    <?php if ($itemsCount > 1): ?>
                                        <?php for ($i = 1; $i < $itemsCount; $i++): ?>
                                            <?php $item = $items[$i]; ?>
                                            <tr class="hover:tw-bg-gray-50">
                                                <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle" title="<?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>">
                                                    <?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>
                                                </td>
                                                <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle"><?= $item['quantity'] ?? 0 ?></td>
                                            </tr>
                                        <?php endfor; ?>
                                    <?php endif; ?>

                                    <tr x-show="open" class="tw-bg-gray-100" style="display: none;">
                                        <td colspan="9" class="tw-p-4 tw-border-t tw-border-gray-300">
                                            <div class="tw-space-y-4">
                                                <div class="tw-grid md:tw-grid-cols-3 tw-gap-6">
                                                    <div class="tw-bg-white tw-p-4 tw-rounded-md tw-border tw-border-gray-300">
                                                        <h5 class="tw-font-bold tw-text-gray-700 tw-pb-2 tw-border-b tw-border-gray-300 tw-mb-2">Vận đơn & CSKH</h5>
                                                        <p class="tw-pt-2"><strong>Mã vận đơn:</strong> <?= html_escape($order['partner']['extend_code'] ?? 'N/A') ?></p>
                                                        <p><strong>Đơn vị VC:</strong> <?= html_escape($order['partner_name'] ?? 'N/A') ?></p>
                                                        <p><strong>Người xử lý:</strong> <?= html_escape($order['assigning_seller']['name'] ?? 'N/A') ?></p>
                                                        <p><strong>NV CSKH:</strong> <?= html_escape($order['assigning_care']['name'] ?? 'N/A') ?></p>
                                                        <p><strong>Marketer:</strong> <?= html_escape($order['marketer']['name'] ?? 'N/A') ?></p>
                                                    </div>
                                                    <div class="tw-bg-white tw-p-4 tw-rounded-md tw-border tw-border-gray-300">
                                                        <h5 class="tw-font-bold tw-text-gray-700 tw-pb-2 tw-border-b tw-border-gray-300 tw-mb-2">Địa chỉ & Ghi chú</h5>
                                                        <p class="tw-pt-2"><strong>Địa chỉ:</strong> <?= html_escape($order['shipping_address']['full_address'] ?? 'N/A') ?></p>
                                                        <p><strong>Phường/Xã:</strong> <?= html_escape($order['shipping_address']['commune_name'] ?? 'N/A') ?></p>
                                                        <p><strong>Quận/Huyện:</strong> <?= html_escape($order['shipping_address']['district_name'] ?? 'N/A') ?></p>
                                                        <p><strong>Tỉnh/TP:</strong> <?= html_escape($order['shipping_address']['province_name'] ?? 'N/A') ?></p>
                                                        <p><strong>Ghi chú:</strong> <?= html_escape($order['note'] ?? 'Không có') ?></p>
                                                    </div>
                                                    <div class="tw-bg-white tw-p-4 tw-rounded-md tw-border tw-border-gray-300">
                                                        <h5 class="tw-font-bold tw-text-gray-700 tw-pb-2 tw-border-b tw-border-gray-300 tw-mb-2">Thông tin thêm</h5>
                                                        <p class="tw-pt-2"><strong>Mã vận đơn:</strong> <?= html_escape($order['partner']['extend_code'] ?? 'N/A') ?></p>
                                                        <p><strong>Thẻ:</strong> <?= !empty($order['tags']) ? html_escape(implode(', ', array_column($order['tags'], 'name'))) : 'N/A' ?></p>
                                                        <p><strong>Khách cũ/mới:</strong> N/A</p>
                                                        <p><strong>SL đơn hoàn:</strong> <?= $order['customer']['returned_order_count'] ?? 0 ?></p>
                                                        <p><strong>Thời gian CSKH:</strong> <?= isset($order['time_assign_care']) ? date('d/m/Y H:i', strtotime($order['time_assign_care'])) : 'N/A' ?></p>
                                                    </div>
                                                </div>

                                                <div x-data="{ showAllData: false }">
                                                    <button @click="showAllData = !showAllData" class="tw-text-sm tw-text-gray-600 hover:tw-text-black tw-mt-4">
                                                        <span x-show="!showAllData">► Hiển thị toàn bộ dữ liệu gốc</span>
                                                        <span x-show="showAllData">▼ Ẩn toàn bộ dữ liệu gốc</span>
                                                    </button>
                                                    <div x-show="showAllData" class="tw-mt-2 tw-bg-gray-800 tw-text-white tw-p-4 tw-rounded-md tw-text-xs tw-overflow-x-auto tw-border tw-border-gray-300">
                                                        <pre><?php print_r($order); ?></pre>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tbody>
                                <tr>
                                    <td colspan="9" class="tw-text-center tw-p-6 tw-text-gray-500 tw-border tw-border-gray-300">Không có đơn hàng nào để hiển thị.</td>
                                </tr>
                            </tbody>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="tw-p-4 tw-border-t tw-border-gray-200">
                    <?= $pagination ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tự động focus vào ô tìm kiếm
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) {
            searchInput.focus();
        }
        
        // Đặt giá trị mặc định cho ngày nếu chưa có
        const startDateInput = document.querySelector('input[name="startDateTime"]');
        const endDateInput = document.querySelector('input[name="endDateTime"]');
        
        if (!startDateInput.value) {
            // Mặc định là 7 ngày trước
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
            startDateInput.value = sevenDaysAgo.toISOString().slice(0, 16);
        }
        
        if (!endDateInput.value) {
            // Mặc định là ngày hiện tại
            endDateInput.value = new Date().toISOString().slice(0, 16);
        }
    });
</script>

<?php init_tail(); ?>