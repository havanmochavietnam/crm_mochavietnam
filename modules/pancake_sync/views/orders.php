<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    /* ===== Light Clean UI (Pure CSS) ===== */
    :root {
        --bg: #f6f8fb;
        --card: #ffffff;
        --text: #1f2937;
        --muted: #6b7280;
        --primary: #2563eb;
        --primary-600: #1d4ed8;
        --border: #e5e7eb;
        --ring: #dbeafe;
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #d97706;
    }

    * {
        box-sizing: border-box
    }

    html,
    body {
        margin: 0;
        padding: 0
    }

    body {
        font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
        background: var(--bg);
        color: var(--text);
        line-height: 1.5;
    }

    /* Layout wrapper if exists */
    .container,
    .wrapper,
    .page,
    .content,
    main {
        max-width: 1200px;
        margin: 24px auto;
        padding: 0 16px;
    }

    /* Headings */
    h1,
    h2,
    h3 {
        margin: 0 0 14px
    }

    h1 {
        font-size: 28px;
        font-weight: 700;
        letter-spacing: .2px
    }

    h2 {
        font-size: 20px;
        font-weight: 600;
        color: var(--muted)
    }

    /* Card blocks */
    .card,
    .panel,
    .box,
    .table-wrapper,
    .filters,
    .toolbar {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 6px 16px rgba(17, 24, 39, .06);
    }

    /* Filters / toolbar */
    .filters,
    .toolbar,
    form.filters,
    form.toolbar {
        padding: 14px 16px;
        margin-bottom: 16px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }

    .filters label {
        font-size: 13px;
        color: var(--muted)
    }

    .filters .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 180px
    }

    .filters input[type="date"],
    .filters input[type="text"],
    .filters select {
        appearance: none;
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        outline: none;
        transition: border .15s, box-shadow .15s;
    }

    .filters input[type="date"]:focus,
    .filters input[type="text"]:focus,
    .filters select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--ring);
    }

    button,
    .btn,
    input[type="submit"],
    input[type="button"] {
        appearance: none;
        border: none;
        background: var(--primary);
        color: #fff;
        padding: 10px 14px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .05s;
    }

    button:hover,
    .btn:hover,
    input[type="submit"]:hover,
    input[type="button"]:hover {
        background: var(--primary-600);
    }

    button:active,
    .btn:active {
        transform: translateY(1px);
    }

    button.secondary,
    .btn.secondary {
        background: #eef2ff;
        color: var(--primary);
    }

    /* Tables */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
    }

    table thead th {
        background: linear-gradient(0deg, #f3f6ff, #f3f6ff);
        color: #111827;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: .4px;
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
    }

    table tbody td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        font-size: 14px;
    }

    table tbody tr:hover {
        background: #f9fbff;
    }

    table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status pills (auto-detect common status text) */
    td.status,
    .status {
        font-weight: 600;
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 999px;
        display: inline-block;
        border: 1px solid var(--border);
        background: #f9fafb;
    }

    .status.success,
    .status.approved,
    .status.completed,
    .status[data-status="Hoàn thành"] {
        color: var(--success);
        border-color: #bbf7d0;
        background: #f0fdf4;
    }

    .status.pending,
    .status[data-status="Chờ xử lý"] {
        color: var(--warning);
        border-color: #fde68a;
        background: #fffbeb;
    }

    .status.cancel,
    .status.canceled,
    .status.failed {
        color: var(--danger);
        border-color: #fecaca;
        background: #fef2f2;
    }

    /* Actions */
    .actions a,
    .table a.action,
    a.btn {
        text-decoration: none;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: #fff;
        font-weight: 600;
    }

    .actions a:hover,
    .table a.action:hover,
    a.btn:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    /* Pagination styles (works for .pagination, ul.pagination, nav[aria-label="pagination"]) */
    .pagination,
    nav.pagination,
    .pager,
    .paging {
        margin: 16px 0;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .pagination a,
    .pagination span,
    .pager a,
    .paging a,
    ul.pagination li a,
    ul.pagination li span {
        display: inline-block;
        min-width: 38px;
        height: 38px;
        line-height: 38px;
        padding: 0 12px;
        text-align: center;
        text-decoration: none;
        color: var(--text);
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        font-weight: 600;
        transition: background .15s, border .15s, color .15s, transform .05s;
    }

    .pagination a:hover,
    .pager a:hover,
    .paging a:hover,
    ul.pagination li a:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: #f8fafc;
    }

    .pagination .active,
    ul.pagination li.active span,
    ul.pagination li.active a {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff !important;
    }

    .pagination .disabled,
    ul.pagination li.disabled span {
        opacity: .5;
        cursor: not-allowed;
    }

    /* Badges & labels */
    .badge {
        display: inline-block;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 999px;
        border: 1px solid var(--border);
        background: #fff;
    }

    /* Search inputs appearing outside filters */
    input[type="search"],
    .search input[type="text"] {
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        outline: none;
    }

    input[type="search"]:focus,
    .search input[type="text"]:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--ring);
    }

    /* Tables responsive wrapper if present */
    .table-wrapper {
        padding: 8px;
        overflow: auto;
    }

    .table-wrapper table {
        min-width: 800px;
    }

    /* Utility overrides for common Tailwind-ish classnames in markup (without relying on Tailwind) */
    .w-full {
        width: 100% !important;
    }

    .text-center {
        text-align: center !important;
    }

    .text-right {
        text-align: right !important;
    }

    .font-bold {
        font-weight: 700 !important;
    }

    .rounded {
        border-radius: 12px !important;
    }

    .shadow {
        box-shadow: 0 6px 16px rgba(17, 24, 39, .08) !important;
    }

    .p-2 {
        padding: 8px !important;
    }

    .p-3 {
        padding: 12px !important;
    }

    .p-4 {
        padding: 16px !important;
    }

    .m-2 {
        margin: 8px !important;
    }

    .m-3 {
        margin: 12px !important;
    }

    .m-4 {
        margin: 16px !important;
    }

    /* Links */
    a {
        color: var(--primary);
    }

    a:hover {
        text-decoration: underline;
    }

    /* Forms general */
    form input[type="date"],
    form input[type="text"],
    form select {
        padding: 10px 12px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        outline: none;
    }

    form input[type="date"]:focus,
    form input[type="text"]:focus,
    form select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px var(--ring);
    }

    /* Buttons variations if classes exist */
    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline:hover {
        background: #eef2ff;
    }
</style>

<?php init_head(); ?>

<style>
    /* Tailwind CSS v3.4.1 | MIT License | [https://tailwindcss.com](https://tailwindcss.com) */
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

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .form-input:focus,
    .form-select:focus {
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

    table th,
    table td {
        vertical-align: middle;
        white-space: nowrap;
    }

    /* --- START: REFINED PAGINATION CSS --- */
    /* Use high-specificity selectors to override existing styles */
    .content .pagination {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 0.5rem !important;
        /* 8px */
        padding: 1rem 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }

    .content .pagination .page-item {
        margin: 0 !important;
    }

    .content .pagination .page-item .page-link {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 40px !important;
        /* 2.5rem */
        height: 40px !important;
        /* 2.5rem */
        padding: 0 0.5rem !important;
        border: 1px solid #d1d5db !important;
        /* gray-300 */
        border-radius: 0.5rem !important;
        /* 8px */
        background-color: #ffffff !important;
        color: #374151 !important;
        /* gray-700 */
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.2s ease-in-out !important;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    }

    .content .pagination .page-item .page-link:hover {
        border-color: #3b82f6 !important;
        /* blue-500 */
        background-color: #eff6ff !important;
        /* blue-50 */
        color: #1d4ed8 !important;
        /* blue-700 */
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 10px rgb(59 130 246 / 0.2) !important;
    }

    .content .pagination .page-item.active .page-link {
        border-color: #2563eb !important;
        /* blue-600 */
        background-color: #2563eb !important;
        /* blue-600 */
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgb(37 99 235 / 0.3) !important;
        transform: translateY(-1px) !important;
    }

    .content .pagination .page-item.disabled .page-link {
        background-color: #f9fafb !important;
        /* gray-50 */
        border-color: #e5e7eb !important;
        /* gray-200 */
        color: #9ca3af !important;
        /* gray-400 */
        cursor: not-allowed !important;
        box-shadow: none !important;
        transform: none !important;
    }

    .content .pagination .page-link svg {
        width: 1rem !important;
        /* 16px */
        height: 1rem !important;
        /* 16px */
    }

    /* Reset potential conflicting styles */
    .pagination>li {
        display: inline-block !important;
        float: none !important;
    }

    .pagination>li>a {
        position: relative !important;
        float: none !important;
    }

    /* Văn bảng */
    .truncate-text {
        max-width: 200px;
        /* Đặt độ rộng tối đa cho cột */
        white-space: nowrap;
        /* Ngăn không cho văn bản xuống dòng */
        overflow: hidden;
        /* Ẩn phần văn bản bị thừa */
        text-overflow: ellipsis;
        /* Hiển thị dấu "..." */
    }

    /* --- END: REFINED PAGINATION CSS --- */
</style>
<div id="wrapper">
    <div class="content">
        <div class="tw-p-4 sm:tw-p-6 lg:tw-p-8">
            <div class="search-filter-container">
                <h4 class="tw-text-lg tw-font-semibold tw-text-gray-800 tw-mb-4">Tìm kiếm Đơn hàng</h4>

                <form method="GET" action="<?= admin_url('pancake_sync') ?>" class="search-form">
                    <div class="form-group">
                        <label class="form-label">Tìm kiếm (SĐT, tên KH, ghi chú)</label>
                        <input type="text" name="search" value="<?= html_escape($this->input->get('search')) ?>" class="form-input" placeholder="Nhập SĐT, tên KH hoặc ghi chú...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="startDateTime" value="<?= html_escape($this->input->get('startDateTime')) ?>" class="form-input">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Ngày kết thúc</label>
                        <input type="date" name="endDateTime" value="<?= html_escape($this->input->get('endDateTime')) ?>" class="form-input">
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

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="<?= admin_url('pancake_sync') ?>" class="btn btn-outline">Đặt lại</a>
                    </div>
                </form>

                <?php if (isset($total)) : ?>
                    <div class="results-count">
                        Tìm thấy <?= $total ?> kết quả
                        <?php if ($this->input->get('search')) : ?>
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
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">STT</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã Đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày Tạo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã vận đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thẻ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Khách Hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số điện thoại</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nhà mạng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Khách Mới / Cũ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Gồm Sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Sản phẩm chi tiết</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Hình ảnh sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã mẫu mã</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Barcode</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số lượng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Đơn giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Đơn giá sau giảm giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giá nhập từng SP</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số lượng đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">SL đơn hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phường/Xã</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Quận/Huyện</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tỉnh/TP</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Người xử lý</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nhân viên CSKH</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Marketer</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Trạng thái</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Page Id</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ad Id</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nguồn quảng cáo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nguồn đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nguồn chi tiết</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Chat page</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời gian khách nhắn tin đầu tiên đến page</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Người tạo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">NV xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nhân viên đầu tiên xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nhân viên cập nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời gian CSKH</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Trạng thái VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày đẩy đơn sang đvvc (Ngày tháng)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Lý do hoàn/hủy đơn hàng từ ĐVVC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Lý do hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">COD</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí trả cho đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">COD đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng tiền đơn hàng (trừ chiết khấu)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng tiền đơn hàng (trừ phí ship)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Doanh số</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Doanh số trước hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Doanh thu đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Doanh thu chưa trừ phí sàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phụ thu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giảm giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí sàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giảm giá trực tiếp trên đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Trị giá đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Trị giá đơn hàng đã chiết khấu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí VC thu của khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng tiền đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thực thu từ đơn vị VC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng phí đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền trả trước</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Chuyển trước</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền khách đưa</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền mặt</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Quẹt thẻ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">MoMo</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">VNPAY</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">ONEPAY</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">QRPay</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền chuyển khoản trả khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền cần thu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giảm giá trước khi hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí hoàn đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng số tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Xác thực CK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Hình ảnh xác thực CK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Chênh lệch phí vận chuyển (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Sàn trợ giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí cố định, giao dịch (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí hoa hồng nền tảng (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí hoa hồng (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí dịch vụ SFP (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí thanh toán (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phí dịch vụ (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền thanh toán thực tế</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Lợi nhuận đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số tiền khách hàng đã chi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Phương thức thanh toán</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Vùng miền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Bán tại quầy</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Kho hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Vị trí lô</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Vị trí kệ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Dự kiến nhận hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú để in</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú nội bộ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi trú trao đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú trao đổi (Theo đơn hàng)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Affiliate</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền hàng đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Sinh nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã khuyến mãi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã khuyến mãi (Tóm tắt)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số đơn của khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Delay giao</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Cấp độ khách hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thẻ Pancake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú DVVC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Dòng thời gian cập nhật trạng thái</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã bài viết</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Lịch sử đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Lịch sử NV xử lý đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Liên kết theo dõi đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thông tin chuyển hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày hoàn đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM source</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM medium</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM campaign</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM term</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM content</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">UTM ID</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thẻ khách hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Link</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">FFM ID</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã đơn hàng đầy đủ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tài khoản đẩy đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">NV gửi hàng đi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm tạo đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tổng giá nhập SP</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giá nhập đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Giá nhập đơn hàng hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Miễn phí ship</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã tuỳ chỉnh đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật sang đã nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm xác nhận đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm đầu tiên xác nhận đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm chờ chuyển hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Nhu cầu khách hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã CTV</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm gắn/xóa thẻ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời gian phân công NV</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">NV đầu tiên cập nhật chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">NV xử lý sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Dịch vụ vận chuyển</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Mới</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Mới</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã gửi hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã gửi hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đang hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đang hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã huỷ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngảy cập nhật trạng thái Đã huỷ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Chờ chuyển hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Chờ chuyển hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đơn Webcake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đơn Webcake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Hoàn một phần</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Hoàn một phần</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã thu tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã thu tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Chờ xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Chờ xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đang đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đang đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã đặt hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã đặt hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đơn Storecake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đơn Storecake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tình trạng kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã rút gọn GHTK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Người nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Địa chỉ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">TT Phường/Xã, Quận/Huyện</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số nhà, ngõ/ngách, hẻm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã tỉnh</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">ZIP CODE</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Đơn vị tiền tệ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Địa chỉ kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">SĐT kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền từ đơn gốc</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền trả lại khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số lại sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Username</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Email</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã đối tác</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Remarks</th>


                            </tr>
                        </thead>
                        <?php if (!empty($orders)) : ?>
                            <?php
                            $statusMap = [
                                'new'           => ['text' => 'Mới', 'class' => 'tw-bg-blue-100 tw-text-blue-800'],
                                'wait_submit' => ['text' => 'Chờ xác nhận', 'class' => 'tw-bg-yellow-100 tw-text-yellow-800'],
                                'submitted'     => ['text' => 'Đã xác nhận', 'class' => 'tw-bg-indigo-100 tw-text-indigo-800'],
                                'packing'       => ['text' => 'Đang đóng hàng', 'class' => 'tw-bg-purple-100 tw-text-purple-800'],
                                'shipped'       => ['text' => 'Đã gửi hàng', 'class' => 'tw-bg-cyan-100 tw-text-cyan-800'],
                                'delivered'     => ['text' => 'Đã nhận', 'class' => 'tw-bg-green-100 tw-text-green-800'],
                                'returning'     => ['text' => 'Đang hoàn', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
                                'returned'      => ['text' => 'Đã hoàn', 'class' => 'tw-bg-lime-100 tw-text-lime-800'],
                                'canceled'      => ['text' => 'Đã huỷ', 'class' => 'tw-bg-red-100 tw-text-red-800'],
                                'removed'       => ['text' => 'Đã xoá', 'class' => 'tw-bg-gray-100 tw-text-gray-800'],
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
                            <?php foreach ($orders as $index => $order) : ?>
                                <?php
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
                                $p_utm_source = $order['histories'][0]['p_utm_source']['new'] ?? null;
                                $p_utm_medium = $order['histories'][0]['p_utm_medium']['new'] ?? null;
                                $p_utm_campaign = $order['histories'][0]['p_utm_campaign']['new'] ?? null;
                                $p_utm_term = $order['histories'][0]['p_utm_term']['new'] ?? null;
                                $p_utm_content = $order['histories'][0]['p_utm_content']['new'] ?? null;
                                $p_utm_id = $order['histories'][0]['p_utm_id']['new'] ?? null;
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
                                <tbody x-data="{ open: false }">
                                    <tr class="hover:tw-bg-gray-50">
                                        <!-- stt -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= $index + 1 ?></td>
                                        <!-- Mã đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['id'] ?? '') ?></td>
                                        <!-- Ngày tạo đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['inserted_at']) ? date('d/m/Y', strtotime($order['inserted_at'])) : 'N/A' ?></td>
                                        <!-- Mã vận đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($extendCode) ?></td>
                                        <!-- Thẻ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= !empty($order['tags']) ? html_escape(implode(', ', array_column($order['tags'], 'name'))) : '' ?>
                                        </td>
                                        <!-- Khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['name'] ?? '') ?></td>
                                        <!-- SĐT -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <div><?php $phone = $order['customer']['phone_numbers'][0] ?? '';
                                                    echo html_escape($phone); ?> </div>
                                        </td>
                                        <!-- Nhà mạng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $phone = $order['customer']['phone_numbers'][0] ?? '';
                                            $carrier = get_mobile_network($phone);
                                            echo html_escape($carrier);
                                            ?>
                                        </td>
                                        <!-- Khách mới/cũ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= '' ?> Cũ </td>
                                        <!-- Gồm các SP -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($firstItem['variation_info']['name'] ?? 'N/A') ?>">
                                            <?= html_escape($firstItem['variation_info']['name'] ?? '') ?>
                                        </td>
                                        <!-- Sản phẩm chi tiết -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($firstItem['variation_info']['detail'] ?? 'N/A') ?>">
                                            <?= html_escape($firstItem['variation_info']['detail'] ?? '') ?>
                                        </td>
                                        <!-- Hình ảnh sản phẩm-->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            // Giữ nguyên logic lấy link của bạn
                                            $imageUrlString = implode($firstItem['variation_info']['images'] ?? []);

                                            // Chỉ hiển thị ảnh nếu link không rỗng
                                            if (!empty($imageUrlString)) :
                                            ?>
                                                <img src="<?= html_escape($imageUrlString) ?>"
                                                    alt="Ảnh sản phẩm"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin: auto;">
                                            <?php
                                            endif;
                                            ?>
                                        </td>
                                        <!-- Mã sản phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($firstItem['variation_info']['display_id'] ?? '') ?>">
                                            <?= html_escape($firstItem['variation_info']['display_id'] ?? '') ?>
                                        </td>
                                        <!-- Mã mẫu mã -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($firstItem['variation_info']['display_id'] ?? '') ?>">
                                            <?= html_escape($firstItem['variation_info']['display_id'] ?? '') ?>
                                        </td>
                                        <!-- Barcode -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($firstItem['variation_info']['barcode'] ?? '') ?>">
                                            <?= html_escape($firstItem['variation_info']['barcode'] ?? '') ?>
                                        </td>
                                        <!-- Tổng số lượng SP -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center">
                                            <?= $firstItem['quantity'] ?? null ?>
                                        </td>
                                        <!-- Đơn giá -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= number_format($firstItem['variation_info']['retail_price'] ?? 0) ?>">
                                            <?= number_format($firstItem['variation_info']['retail_price'] ?? 0) ?>
                                        </td>
                                        <!-- Đơn giá sau giảm giá -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= number_format(($firstItem['variation_info']['retail_price'] ?? 0) - ($firstItem['total_discount'] ?? 0)) ?>">
                                            <?= number_format(($firstItem['variation_info']['retail_price'] ?? 0) - ($firstItem['total_discount'] ?? 0)) ?>
                                        </td>
                                        <!-- Giá nhập từng SP -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= number_format($firstItem['variation_info']['last_imported_price'] ?? 0) ?>">
                                            <?= number_format($firstItem['variation_info']['last_imported_price'] ?? 0) ?>
                                        </td>
                                        <!-- Số lượng đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center">
                                            <?= $firstItem['exchange_count'] ?? null ?>
                                        </td>
                                        <!-- Số lượng đơn hàng hoàn của khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['returned_order_count'] ?? 0) ?></td>
                                        <!-- Phường/Xã -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['commune_name'] ?? '') ?></td>
                                        <!-- Quận/Huyện -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['district_name'] ?? '') ?></td>
                                        <!-- Tỉnh thành phố -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['province_name'] ?? '') ?></td>
                                        <!-- Người xử lý -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></td>
                                        <!-- Nhân viên CSKH -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['assigning_care']['name'] ?? '') ?></td>
                                        <!-- Marketer -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['marketer']['name'] ?? '') ?></td>
                                        <!-- Trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <span class="tw-inline-block tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-rounded-fu    ll <?= $statusInfo['class'] ?>">
                                                <?= $statusInfo['text'] ?>
                                            </span>
                                        </td>
                                        <!-- Page ID -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['page_id'] ?? '') ?></td>
                                        <!-- Ad Id -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['ad_id'] ?? '') ?></td>
                                        <!-- Nguồn quảng cáo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['ads_source'] ?? '') ?></td>
                                        <!-- Nguồn đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['order_sources_name'] ?? '') ?></td>
                                        <!-- Nguồn chi tiết -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['account_name'] ?? '') ?></td>
                                        <!-- Thời gian khách nhắn tin đầu tiên đến page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['page']['name'] ?? '') ?></td>
                                        <!-- Chat page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape('') ?></td>
                                        <!-- Người tạo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['creator']['name'] ?? 'Hệ thống') ?></td>
                                        <!-- NV xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($staffconfirm) ?></td>
                                        <!-- NV đầu tiên xác nhận đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($firststaffconfirm ?? '') ?></td>
                                        <!-- Nhân viên cập nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['last_editor']['name'] ?? 'Hệ thống') ?></td>
                                        <!-- Thời gian CSKH -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['time_assign_care']) ? date('d/m/Y', strtotime($order['time_assign_care'])) : '' ?></td>
                                        <!-- Đơn vị VC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['partner']['partner_name'] ?? '') ?></td>
                                        <!-- Trạng thái VC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['partner']['extend_update'][0]['status'] ?? '') ?>
                                        </td>
                                        <!-- Ngày đẩy đơn sang đvvc (Ngày tháng) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['time_send_partner']) ? date('d/m/Y', strtotime($order['time_send_partner'])) : '' ?></td>
                                        <!-- Lý do hoàn/hủy đơn hàng từ ĐVVC -->
                                        <td class=" tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['partner']['extend_update'][0]['note'] ?? '') ?></td>
                                        <!-- Lý do hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['returnedreason'] ?? '') ?></td>
                                        <!-- COD -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($cod) ?></td>
                                        <!-- Phí trả cho đơn vị VC -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($partner_fee) ?></td>
                                        <!-- COD đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= number_format($codDoiSoat) ?></td>
                                        <!-- Tổng tiền đơn hàng (trừ chiết khấu) -->
                                        <td class="tw-px-6 tw-py-4 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
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
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
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
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
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
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($fee_marketplace) ?>
                                        </td>
                                        <!-- Giảm giá trực tiếp trên đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_discount) ?>
                                        </td>
                                        <!-- Trị giá đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Trị giá đơn hàng đã chiết khấu -->
                                        <td class="tw-px-6 tw-py-4 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
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
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($shipping_fee) ?>
                                        </td>
                                        <!-- Tổng tiền đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Thực thu từ ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($money_to_collect) ?>
                                        </td>
                                        <!-- Tổng phí đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_partner) ?>
                                        </td>
                                        <!-- Tiền trả trước -->
                                        <td></td>
                                        <!-- Chuyển khoản -->
                                        <td></td>
                                        <!-- Tiền khách đưa -->
                                        <td></td>
                                        <!-- Tiền mặt -->
                                        <td></td>
                                        <!-- Quẹt thẻ -->
                                        <td></td>
                                        <!-- MoMo -->
                                        <td></td>
                                        <!-- VNPAY -->
                                        <td></td>
                                        <!-- ONEPAY -->
                                        <td></td>
                                        <!-- QRPay -->
                                        <td></td>
                                        <!-- Tiền chuyển khoản trả khách -->
                                        <td></td>
                                        <!-- Tiền cần thu -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($cod) ?>
                                        </td>
                                        <!-- Giảm giá trước khi hoàn -->
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
                                        <!-- Phí hoàn đơn hàng -->
                                        <td></td>
                                        <!-- Tổng số tiền -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Xác thực CK -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalPrice) ?>
                                        </td>
                                        <!-- Hình ảnh xác thực CK -->
                                        <td></td>
                                        <!-- Chênh lệch phí vận chuyển (Sàn TMĐT) -->
                                        <td></td>
                                        <!-- Sàn trợ giá -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_marketplace_voucher) ?? 0 ?>
                                        </td>
                                        <!-- Phí cố định, giao dịch (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_paymentFee) ?? 0 ?>
                                        </td>
                                        <!-- Phí hoa hồng nền tảng (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_platform_commission) ?? 0 ?>
                                        </td>
                                        <!-- Phí hoa hồng (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_platform_affiliate_commission) ?? 0 ?>
                                        </td>
                                        <!-- Phí dịch vụ SFP (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_sfp_service_fee) ?? 0 ?>
                                        </td>
                                        <!-- Phí thanh toán (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_seller_transaction_fee) ?? 0 ?>
                                        </td>
                                        <!-- Phí dịch vụ (Sàn TMĐT) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($total_fee_service_fee) ?? 0 ?>
                                        </td>
                                        <!-- Tiền thanh toán thực tế -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($buyer_total_amount) ?? 0 ?>
                                        </td>
                                        <!-- Lợi nhuận đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($cod) ?>
                                        </td>
                                        <!-- Số tiền khách hàng đã chi -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($cod) ?>
                                        </td>
                                        <!-- Phương thức thanh toán -->
                                        <td></td>
                                        <!-- Vùng miền -->
                                        <td></td>
                                        <!-- Ngày đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= isset($reconciliationTime) ? date('d/m/Y', strtotime($reconciliationTime)) : '' ?>
                                        </td>
                                        <!-- Bán tại quầy -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">Online</td>
                                        <!-- Kho hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['warehouse_info']['name'] ?? '') ?></td>
                                        <!-- Vị trí lô  -->
                                        <td></td>
                                        <!-- Vị trí kệ  -->
                                        <td></td>
                                        <!-- Dự kiến nhận hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= isset($order['estimate_delivery_date']) ? date('d/m/Y', strtotime($order['estimate_delivery_date'])) : '' ?></td>
                                        <!-- Ghi chú để in -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['note_print'] ?? '') ?>
                                        </td>
                                        <!-- Ghi chú nội bộ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['note'] ?? '') ?>
                                        </td>
                                        <!-- Ghi trú trao đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['notes']['messager'] ?? '') ?>
                                        </td>
                                        <!-- Ghi chú trao đổi (Theo đơn hàng) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['notes']['messager'] ?? '') ?>
                                        </td>
                                        <!-- Affiliate -->
                                        <td></td>
                                        <!-- Tiền hàng đổi -->
                                        <td></td>
                                        <!-- Sinh nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['customer']['date_of_birth']) ? date('d/m', strtotime($order['customer']['date_of_birth'])) : '' ?></td>
                                        <!-- Mã khuyến mãi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php if (!empty($promotionName) && $promotionName !== ''): ?>
                                                Mã khuyến mãi nâng cao: <?= html_escape($promotionName) ?>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Mã khuyến mãi(rút gọn) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($promotionName) ?></td>
                                        <!-- Số đơn của khách  -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($totalOrders) ?>
                                        </td>
                                        <!-- Delay giao -->
                                        <td></td>
                                        <!-- Cấp độ khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['customer']['level']['name'] ?? '') ?>
                                        </td>
                                        <!-- Thẻ Pancake -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape(implode(', ', $tagPancake)) ?>
                                        </td>
                                        <!-- Ghi chú DVVC -->
                                        <td></td>
                                        <!-- Dòng thời gian cập nhật trạng thái -->
                                        <td></td>
                                        <!-- Mã bài viết -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['post_id']) ?>
                                        </td>
                                        <!-- Lịch sử đơn hàng -->
                                        <td></td>
                                        <!-- Lịch sử NV xử lý đơn hàng -->
                                        <td></td>
                                        <!-- Liên kết theo dõi đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($extendCodeVCLink) ?>
                                        </td>
                                        <!-- Thông tin chuyển hàng -->
                                        <td></td>
                                        <!-- Ngày hoàn Đơn -->
                                        <td></td>
                                        <!-- Ngày nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php // Kiểm tra trực tiếp sự tồn tại của key 'updated_at' lồng bên trong 
                                            ?>
                                            <?= isset($order['partner']['extend_update'][0]['updated_at']) ? date('d/m/Y', strtotime($order['partner']['extend_update'][0]['updated_at'])) : '' ?>
                                        </td>
                                        <!-- UTM Source -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_source) ?>
                                        </td>
                                        <!-- UTM medium -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_medium) ?>
                                        </td>
                                        <!-- UTM campaign -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_campaign) ?>
                                        </td>
                                        <!-- UTM term -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_term) ?>
                                        </td>
                                        <!-- UTM content -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_content) ?>
                                        </td>
                                        <!-- UTM ID -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($p_utm_id) ?>
                                        </td>
                                        <!-- Thẻ khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= implode($order['customer']['tags'] ?? []) ?>
                                        </td>
                                        <!-- Link -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['link']) ?>
                                        </td>
                                        <!-- FFM ID -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($order['warehouse_info']['ffm_id'] ?? '') ?>
                                        </td>
                                        <!-- Mã đơn hàng đầy đủ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                            <?= html_escape($tracking_id) ?>
                                        </td>
                                        <!-- Tài khoản đẩy đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                        </td>
                                        <!-- NV gửi hàng đi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text" rowspan="<?= $itemsCount ?>">
                                        </td>
                                        <!-- Thời điểm tạo đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['inserted_at']) ? date('d/m/Y', strtotime($order['inserted_at'])) : 'N/A' ?></td>
                                        <!-- Ngày cập nhập -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['updated_at']) ? date('d/m/Y', strtotime($order['updated_at'])) : 'N/A' ?></td>
                                        <!-- Thời điểm cập nhật trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $deliveryDate = null; // Tạo một biến để lưu ngày giao hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 3 (tức là 'delivered')
                                                    if (isset($history['status']) && $history['status'] == 3) {
                                                        $deliveryDate = $history['updated_at']; // Lấy ngày và
                                                        break; // thoát khỏi vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc 'N/A' nếu không có
                                            echo isset($deliveryDate) ? date('d/m/Y', strtotime($deliveryDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Tổng giá nhập SP -->
                                        <td></td>
                                        <!-- Giá nhập đơn hàng -->
                                        <td></td>
                                        <!-- Giá nhập đơn hàng hoàn -->
                                        <td></td>
                                        <!-- Miễn phí ship -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php if ($order['is_free_shipping'] ?? false) : ?>
                                                <span title="Đơn hàng này được miễn phí vận chuyển">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#22c55e" width="24" height="24">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Mã tuỳ chỉnh đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['id'] ?? '') ?></td>
                                        <!-- Thời điểm cập nhật sang đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $deliveryDate = null; // Tạo một biến để lưu ngày giao hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 3 (tức là 'delivered')
                                                    if (isset($history['status']) && $history['status'] == 3) {
                                                        $deliveryDate = $history['updated_at']; // Lấy ngày và
                                                        break; // thoát khỏi vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc 'N/A' nếu không có
                                            echo isset($deliveryDate) ? date('d/m/Y', strtotime($deliveryDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm xác nhận đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $confirmationDate = null; // Tạo biến để lưu ngày xác nhận đơn

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 1 (tức là 'đã xác nhận' / 'đang xử lý')
                                                    if (isset($history['status']) && $history['status'] == 1) {
                                                        $confirmationDate = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Thoát khỏi vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc để trống nếu không có
                                            echo isset($confirmationDate) ? date('d/m/Y', strtotime($confirmationDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm đầu tiên xác nhận đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $firstConfirmationDate = null; // Tạo biến để lưu ngày xác nhận đầu tiên

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử theo thứ tự thời gian
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 1 (tức là 'đã xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 1) {
                                                        $firstConfirmationDate = $history['updated_at']; // Lấy ngày và giờ cập nhật

                                                        // Dừng vòng lặp ngay khi tìm thấy lần xác nhận đầu tiên
                                                        break;
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc để trống nếu không có
                                            echo isset($firstConfirmationDate) ? date('d/m/Y', strtotime($firstConfirmationDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $shippingDate = null; // Tạo biến để lưu ngày bắt đầu chuyển hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 2 (tức là 'đang giao hàng')
                                                    if (isset($history['status']) && $history['status'] == 2) {
                                                        $shippingDate = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc để trống nếu không có
                                            echo isset($shippingDate) ? date('d/m/Y', strtotime($shippingDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật chờ hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $waitingDate = null; // Tạo biến để lưu ngày chờ hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 0 (tức là 'đơn hàng mới' / 'chờ xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 0) {
                                                        $waitingDate = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc để trống nếu không có
                                            echo isset($waitingDate) ? date('d/m/Y', strtotime($waitingDate)) : '';
                                            ?>
                                        </td>
                                        <!-- Nhu cầu khách hàng -->
                                        <td></td>
                                        <!-- Mã CTV -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $affiliateId = null; // Tạo biến để lưu ngày chờ hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['histories'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['histories'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 0 (tức là 'đơn hàng mới' / 'chờ xác nhận')
                                                    if (isset($history['third_party_infomation']['new']['affiliate_display_id'])) {
                                                        $affiliateId = $history['third_party_infomation']['new']['affiliate_display_id'];
                                                        break; // Dừng lại khi đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị ngày đã tìm thấy hoặc để trống nếu không có
                                            echo $affiliateId;
                                            ?>
                                        </td>
                                        <!-- Thời điểm gắn/xóa thẻ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            // Mảng để lưu trữ các dòng lịch sử đã định dạng
                                            $tagHistoryLines = [];

                                            // Kiểm tra xem có lịch sử đơn hàng không
                                            if (!empty($order['histories'])) {

                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['histories'] as $history) {

                                                    // Chỉ xử lý những mục có sự thay đổi về 'tags'
                                                    if (isset($history['tags'])) {

                                                        // Lấy ra mảng các tên tag cũ và mới
                                                        $oldTags = !empty($history['tags']['old']) ? array_column($history['tags']['old'], 'name') : [];
                                                        $newTags = !empty($history['tags']['new']) ? array_column($history['tags']['new'], 'name') : [];

                                                        // Chuyển mảng tên tag thành chuỗi, ngăn cách bởi dấu phẩy
                                                        $oldTagsString = implode(', ', $oldTags);
                                                        $newTagsString = implode(', ', $newTags);

                                                        // Lấy và định dạng ngày cập nhật
                                                        $date = date('d/m/Y', strtotime($history['updated_at']));

                                                        $line = '';

                                                        // Trường hợp 1: Thêm thẻ mới (từ trạng thái không có thẻ)
                                                        if (empty($oldTagsString) && !empty($newTagsString)) {
                                                            $line = "Thêm thẻ {$newTagsString} - {$date}";
                                                        }
                                                        // Trường hợp 2: Có sự thay đổi từ thẻ cũ sang thẻ mới
                                                        else if (!empty($oldTagsString) && !empty($newTagsString) && $oldTagsString !== $newTagsString) {
                                                            $line = "{$oldTagsString} -> {$newTagsString} - {$date}";
                                                        }

                                                        // Nếu có dòng lịch sử được tạo, thêm vào mảng
                                                        if ($line) {
                                                            $tagHistoryLines[] = $line;
                                                        }
                                                    }
                                                }
                                            }

                                            // In kết quả ra màn hình, mỗi mục trên một dòng
                                            foreach ($tagHistoryLines as $historyLine) {
                                                echo htmlspecialchars($historyLine) . "<br>";
                                            }

                                            ?>
                                        </td>
                                        <!-- Thời gian phân công NV -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= isset($order['time_assign_seller']) ? date('d/m/Y', strtotime($order['time_assign_seller'])) : '' ?></td>
                                        <!-- Nhân viên cập nhập chờ hàng -->
                                        <td></td>
                                        <!-- NV xử lý SP -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></td>
                                        <!-- Dịch vụ vận chuyển -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $serviceType = null;
                                            $displayText = ''; // Biến để hiển thị ra màn hình

                                            // Kiểm tra đường dẫn tới service_type để tránh lỗi
                                            if (!empty($order['partner']['service_partner']['orders'][0]['base_info']['service_type'])) {

                                                // Lấy ra loại dịch vụ gốc
                                                $serviceType = $order['partner']['service_partner']['orders'][0]['base_info']['service_type'];

                                                // Kiểm tra nếu giá trị là 'Instant' thì đổi thành 'Hỏa tốc'
                                                if ($serviceType === 'Instant') {
                                                    $displayText = 'Hỏa tốc';
                                                } else {
                                                    // Nếu là giá trị khác, hiển thị giá trị gốc
                                                    $displayText = $serviceType;
                                                }
                                            }

                                            // In ra kết quả đã được chuyển đổi
                                            echo $displayText; // Kết quả sẽ là "Hỏa tốc"
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Mới -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $newStatusTime = null; // Tạo biến để lưu thời gian

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 0 (tức là 'Mới')
                                                    if (isset($history['status']) && $history['status'] == 0) {
                                                        $newStatusTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy hoặc để trống nếu không có
                                            // Định dạng lại ngày/tháng/năm và giờ:phút
                                            echo isset($newStatusTime) ? date('d/m/Y', strtotime($newStatusTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Mới -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $newStatusTime = null; // Tạo biến để lưu thời gian

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 0 (tức là 'Mới')
                                                    if (isset($history['status']) && $history['status'] == 0) {
                                                        $newStatusTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy hoặc để trống nếu không có
                                            // Định dạng lại ngày/tháng/năm và giờ:phút
                                            echo isset($newStatusTime) ? date('d/m/Y', strtotime($newStatusTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $confirmedTime = null; // Tạo biến để lưu thời gian xác nhận

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 1 (tức là 'Đã xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 1) {
                                                        $confirmedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy lần xác nhận đầu tiên
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($confirmedTime) ? date('d/m/Y', strtotime($confirmedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $confirmedTime = null; // Tạo biến để lưu thời gian xác nhận

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 1 (tức là 'Đã xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 1) {
                                                        $confirmedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy lần xác nhận đầu tiên
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($confirmedTime) ? date('d/m/Y', strtotime($confirmedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã gửi hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $shippedTime = null; // Tạo biến để lưu thời gian gửi hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 2 (tức là 'Đã gửi hàng' / 'Đang giao')
                                                    if (isset($history['status']) && $history['status'] == 2) {
                                                        $shippedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($shippedTime) ? date('d/m/Y', strtotime($shippedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã gửi hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $shippedTime = null; // Tạo biến để lưu thời gian gửi hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 2 (tức là 'Đã gửi hàng' / 'Đang giao')
                                                    if (isset($history['status']) && $history['status'] == 2) {
                                                        $shippedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($shippedTime) ? date('d/m/Y', strtotime($shippedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $deliveredTime = null; // Tạo biến để lưu thời gian đã nhận

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 3 (tức là 'Đã nhận')
                                                    if (isset($history['status']) && $history['status'] == 3) {
                                                        $deliveredTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($deliveredTime) ? date('d/m/Y', strtotime($deliveredTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $deliveredTime = null; // Tạo biến để lưu thời gian đã nhận

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 3 (tức là 'Đã nhận')
                                                    if (isset($history['status']) && $history['status'] == 3) {
                                                        $deliveredTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($deliveredTime) ? date('d/m/Y', strtotime($deliveredTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đang hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $returningTime = null; // Tạo biến để lưu thời gian đang hoàn

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 4 (tức là 'Đang hoàn')
                                                    if (isset($history['status']) && $history['status'] == 4) {
                                                        $returningTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($returningTime) ? date('d/m/Y', strtotime($returningTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đang hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $returningTime = null; // Tạo biến để lưu thời gian đang hoàn

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 4 (tức là 'Đang hoàn')
                                                    if (isset($history['status']) && $history['status'] == 4) {
                                                        $returningTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($returningTime) ? date('d/m/Y', strtotime($returningTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $returnedTime = null; // Tạo biến để lưu thời gian đã hoàn

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 5 (tức là 'Đã hoàn')
                                                    if (isset($history['status']) && $history['status'] == 5) {
                                                        $returnedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($returnedTime) ? date('d/m/Y', strtotime($returnedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $returnedTime = null; // Tạo biến để lưu thời gian đã hoàn

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 5 (tức là 'Đã hoàn')
                                                    if (isset($history['status']) && $history['status'] == 5) {
                                                        $returnedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($returnedTime) ? date('d/m/Y', strtotime($returnedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã huỷ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $canceledTime = null; // Tạo biến để lưu thời gian đã huỷ

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 6 (tức là 'Đã huỷ')
                                                    if (isset($history['status']) && $history['status'] == 6) {
                                                        $canceledTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($canceledTime) ? date('d/m/Y', strtotime($canceledTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã huỷ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $canceledTime = null; // Tạo biến để lưu thời gian đã huỷ

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 6 (tức là 'Đã huỷ')
                                                    if (isset($history['status']) && $history['status'] == 6) {
                                                        $canceledTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($canceledTime) ? date('d/m/Y', strtotime($canceledTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $pendingTime = null; // Tạo biến để lưu thời gian chờ chuyển hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 9 (tức là 'Chờ xử lý/Chờ chuyển hàng')
                                                    if (isset($history['status']) && $history['status'] == 9) {
                                                        $pendingTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($pendingTime) ? date('d/m/Y', strtotime($pendingTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $pendingTime = null; // Tạo biến để lưu thời gian chờ chuyển hàng

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 9 (tức là 'Chờ xử lý/Chờ chuyển hàng')
                                                    if (isset($history['status']) && $history['status'] == 9) {
                                                        $pendingTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($pendingTime) ? date('d/m/Y', strtotime($pendingTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đơn Webcake -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Đơn Webcake -->
                                        <td></td>
                                        <!-- Thời điểm cập nhật trạng thái Chờ hàng -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Chờ hàng -->
                                        <td></td>
                                        <!-- Thời điểm cập nhật trạng thái Hoàn một phần -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $partReturnedTime = null; // Tạo biến để lưu thời gian hoàn một phần

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 15 (tức là 'Hoàn một phần')
                                                    if (isset($history['status']) && $history['status'] == 15) {
                                                        $partReturnedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($partReturnedTime) ? date('d/m/Y', strtotime($partReturnedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Hoàn một phần -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $partReturnedTime = null; // Tạo biến để lưu thời gian hoàn một phần

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 15 (tức là 'Hoàn một phần')
                                                    if (isset($history['status']) && $history['status'] == 15) {
                                                        $partReturnedTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($partReturnedTime) ? date('d/m/Y', strtotime($partReturnedTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã thu tiền -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Đã thu tiền -->
                                        <td></td>
                                        <!-- Thời điểm cập nhật trạng thái Chờ xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $waitingConfirmationTime = null; // Tạo biến để lưu thời gian

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 17 (tức là 'Chờ xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 17) {
                                                        $waitingConfirmationTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($waitingConfirmationTime) ? date('d/m/Y', strtotime($waitingConfirmationTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Chờ xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $waitingConfirmationTime = null; // Tạo biến để lưu thời gian

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 17 (tức là 'Chờ xác nhận')
                                                    if (isset($history['status']) && $history['status'] == 17) {
                                                        $waitingConfirmationTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($waitingConfirmationTime) ? date('d/m/Y', strtotime($waitingConfirmationTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đang đổi -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Đang đổi -->
                                        <td></td>
                                        <!-- Thời điểm cập nhật trạng thái Đã đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $exchangeTime = null; // Tạo biến để lưu thời gian đã đổi

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 5 (tức là 'Đã hoàn'/'Đã đổi')
                                                    if (isset($history['status']) && $history['status'] == 5) {
                                                        $exchangeTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($exchangeTime) ? date('d/m/Y', strtotime($exchangeTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Ngày cập nhật trạng thái Đã đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $exchangeTime = null; // Tạo biến để lưu thời gian đã đổi

                                            // Kiểm tra xem có lịch sử trạng thái không
                                            if (!empty($order['status_history'])) {
                                                // Lặp qua từng mục trong lịch sử
                                                foreach ($order['status_history'] as $history) {
                                                    // Kiểm tra nếu trạng thái là 5 (tức là 'Đã hoàn'/'Đã đổi')
                                                    if (isset($history['status']) && $history['status'] == 5) {
                                                        $exchangeTime = $history['updated_at']; // Lấy ngày và giờ cập nhật
                                                        break; // Dừng vòng lặp vì đã tìm thấy
                                                    }
                                                }
                                            }

                                            // Hiển thị thời gian đã tìm thấy và định dạng lại
                                            echo isset($exchangeTime) ? date('d/m/Y', strtotime($exchangeTime)) : '';
                                            ?>
                                        </td>
                                        <!-- Thời điểm cập nhật trạng thái Đã đặt hàng -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Đã đặt hàng -->
                                        <td></td>
                                        <!-- Thời điểm cập nhật trạng thái Đơn Storecake -->
                                        <td></td>
                                        <!-- Ngày cập nhật trạng thái Đơn Storecake -->
                                        <td></td>
                                        <!-- Tình trạng kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= '' ?> Đủ hàng </td>
                                        <!-- Ghi chú sản phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $productNotes = []; // Tạo mảng để lưu các ghi chú

                                            // Kiểm tra xem đơn hàng có sản phẩm không
                                            if (!empty($order['items'])) {
                                                // Lặp qua từng sản phẩm trong mảng 'items'
                                                foreach ($order['items'] as $item) {
                                                    // Kiểm tra xem sản phẩm có ghi chú (note) không và ghi chú đó không rỗng
                                                    if (isset($item['note']) && !empty(trim($item['note']))) {
                                                        // Thêm ghi chú vào mảng
                                                        $productNotes[] = $item['note'];
                                                    }
                                                }
                                            }

                                            // In tất cả các ghi chú đã tìm thấy, mỗi ghi chú trên một dòng
                                            if (!empty($productNotes)) {
                                                echo implode('<br>', $productNotes);
                                            }
                                            ?>
                                        </td>
                                        <!-- Mã rút gọn GHTK -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $shortCode = null; // Tạo biến để lưu mã rút gọn

                                            // Kiểm tra xem có thông tin đối tác vận chuyển và extend_code không
                                            if (!empty($order['partner']['extend_code'])) {

                                                // Lấy ra chuỗi mã đầy đủ
                                                $fullCode = $order['partner']['extend_code'];

                                                // Tách chuỗi bằng dấu chấm '.'
                                                $parts = explode('.', $fullCode);

                                                // Lấy phần tử cuối cùng của mảng kết quả
                                                $shortCode = end($parts);
                                            }

                                            // In ra mã rút gọn đã tìm thấy
                                            echo $shortCode;
                                            ?>
                                        </td>
                                        <!-- Người nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['name'] ?? '') ?></td>
                                        <!-- Địa chỉ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $fullAddress = null; // Tạo biến để lưu địa chỉ

                                            // Kiểm tra xem có thông tin địa chỉ giao hàng và trường full_address không
                                            if (!empty($order['shipping_address']['full_address'])) {

                                                // Lấy ra chuỗi địa chỉ đầy đủ
                                                $fullAddress = $order['shipping_address']['full_address'];
                                            }

                                            // In ra địa chỉ đã tìm thấy
                                            echo $fullAddress;
                                            ?>
                                        </td>
                                        <!-- TT Phường/Xã, Quận/Huyện -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $addressString = ''; // Tạo chuỗi rỗng để chứa kết quả

                                            // Lấy ra các thành phần của địa chỉ
                                            $commune = $order['data']['shipping_address']['commune_name'] ?? null;
                                            $district = $order['data']['shipping_address']['district_name'] ?? null;
                                            $province = $order['data']['shipping_address']['province_name'] ?? null;

                                            // Kiểm tra xem các thành phần có tồn tại không
                                            if ($commune && $district && $province) {
                                                // Ghép các thành phần lại với nhau theo định dạng mong muốn
                                                $addressString = "{$commune}, {$district}, {$province}";
                                            }

                                            // In ra chuỗi địa chỉ đã ghép
                                            echo $addressString;
                                            ?>
                                        </td>
                                        <!-- Số nhà, ngõ/ngách, hẻm -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $streetAddress = null; // Tạo biến để lưu địa chỉ chi tiết

                                            // Kiểm tra xem có trường address trong shipping_address không
                                            if (!empty($order['data']['shipping_address']['address'])) {

                                                // Lấy ra chuỗi địa chỉ chi tiết
                                                $streetAddress = $order['data']['shipping_address']['address'];
                                            }

                                            // In ra kết quả
                                            echo $streetAddress;
                                            ?>
                                        </td>
                                        <!-- Mã Tỉnh -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['province_name'] ?? '') ?></td>
                                        <!-- ZIP CODE -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['shipping_address']['commune_id'] ?? '') ?></td>
                                        <!-- Đơn vị tiền tệ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= '' ?> VND</td>
                                        <!-- Địa chỉ kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['warehouse_info']['full_address'] ?? '') ?></td>
                                        <!-- SĐT kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['warehouse_info']['phone_number'] ?? '') ?></td>
                                        <!-- Tiền từ đơn gốc -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= '' ?> 0</td>
                                        <!-- Tiền trả lại khách -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= '' ?> 0</td>
                                        <!-- Số loại sản phẩm -->
                                        <td></td>
                                        <!-- Username -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($order['customer']['username'] ?? '') ?></td>
                                        <!-- Email -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $customerEmails = ''; // Tạo chuỗi rỗng để chứa email

                                            // Kiểm tra xem có thông tin khách hàng và mảng emails không
                                            if (!empty($order['data']['customer']['emails']) && is_array($order['data']['customer']['emails'])) {

                                                // Lấy ra mảng emails
                                                $emailsArray = $order['data']['customer']['emails'];

                                                // Nối các email trong mảng thành một chuỗi duy nhất
                                                $customerEmails = implode(', ', $emailsArray);
                                            }

                                            // In ra chuỗi email đã tìm thấy
                                            echo $customerEmails;
                                            ?>
                                        </td>
                                        <!-- Mã đối tác -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>"><?= html_escape($extendCode) ?></td>
                                        <!-- Remarks -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?php
                                            $productList = []; // Tạo mảng để chứa danh sách sản phẩm

                                            // Sửa đổi chính: Bỏ ['data'] để truy cập trực tiếp vào mảng 'items'
                                            if (!empty($order['items']) && is_array($order['items'])) {

                                                // Lặp qua từng sản phẩm trong mảng 'items'
                                                foreach ($order['items'] as $item) {

                                                    // Lấy các thông tin cần thiết từ mỗi sản phẩm
                                                    $quantity = $item['quantity'] ?? 0;
                                                    // Ưu tiên dùng display_id, nếu không có thì dùng barcode
                                                    $code = $item['variation_info']['display_id'] ?? $item['variation_info']['barcode'] ?? 'N/A';
                                                    $name = $item['variation_info']['name'] ?? 'Không có tên';

                                                    // Tạo chuỗi theo định dạng bạn yêu cầu
                                                    $productLine = "{$quantity} x {$code}-{$name}";

                                                    // Thêm chuỗi đã định dạng vào mảng
                                                    $productList[] = $productLine;
                                                }
                                            }

                                            // In ra danh sách, mỗi sản phẩm trên một dòng và kết thúc bằng dấu chấm phẩy
                                            if (!empty($productList)) {
                                                echo implode(';<br>', $productList);
                                            }
                                            ?>
                                        </td>


















                                        <?php if ($itemsCount > 1) : ?>
                                            <?php for ($i = 1; $i < $itemsCount; $i++) : ?>
                                                <?php $item = $items[$i]; ?>
                                    <tr class="hover:tw-bg-gray-50">

                                        <!-- Cột Tên Sản Phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>">
                                            <?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>
                                        </td>

                                        <!-- Cột Chi Tiết Sản Phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['detail'] ?? '') ?>">
                                            <?= html_escape($item['variation_info']['detail'] ?? $item['variation_info']['name'] ?? '') ?>
                                        </td>

                                        <!-- === CỘT HÌNH ẢNH (ĐÃ SỬA LẠI) === -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                                // Lấy ra link ảnh ĐẦU TIÊN [0] từ trong mảng 'images'
                                                $imageUrl = $item['variation_info']['images'][0] ?? '';

                                                // Chỉ hiển thị thẻ <img> nếu thực sự có link ảnh
                                                if (!empty($imageUrl)) :
                                            ?>
                                                <img src="<?= html_escape($imageUrl) ?>"
                                                    alt="Ảnh sản phẩm"
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin: auto;">
                                            <?php
                                                endif; // Kết thúc câu lệnh if
                                            ?>
                                        </td>
                                        <!-- Mã sản phẩm  -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['display_id'] ?? '') ?>">
                                            <?= html_escape($item['variation_info']['display_id'] ?? '') ?>
                                        </td>
                                        <!-- Mã mẫu mã  -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['display_id'] ?? '') ?>">
                                            <?= html_escape($item['variation_info']['display_id'] ?? '') ?>
                                        </td>
                                        <!-- barcode -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['barcode'] ?? '') ?>">
                                            <?= html_escape($item['variation_info']['barcode'] ?? '') ?>
                                        </td>
                                        <!-- Cột Số Lượng -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center"><?= $item['quantity'] ?? 0 ?></td>
                                        <!-- Đơn giá  -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= number_format($item['variation_info']['retail_price'] ?? 0) ?>">
                                            <?= number_format($item['variation_info']['retail_price'] ?? 0) ?>
                                        </td>
                                        <!-- Đơn giá sau giảm giá -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center"
                                            title="<?= number_format(($item['variation_info']['retail_price'] ?? 0) - ($item['total_discount'] ?? 0)) ?>">

                                            <?= number_format(($item['variation_info']['retail_price'] ?? 0) - ($item['total_discount'] ?? 0)) ?>

                                        </td>
                                        <!-- Giá nhập hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= number_format($item['variation_info']['last_imported_price'] ?? 0) ?>">
                                            <?= number_format($item['variation_info']['last_imported_price'] ?? 0) ?>
                                        </td>
                                        <!-- Cột Số Lượng Đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center"><?= $item['exchange_count'] ?? 0 ?></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php endif; ?>


                            <tr x-show="open" class="tw-bg-gray-100" style="display: none;">
                                <td colspan="9" class="tw-p-4 tw-border-t tw-border-gray-300">
                                    <div class="tw-space-y-4">
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
                        <?php else : ?>
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
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
            startDateInput.value = sevenDaysAgo.toISOString().slice(0, 16);
        }

        if (!endDateInput.value) {
            endDateInput.value = new Date().toISOString().slice(0, 16);
        }

        // --- Improved function to style pagination and add icons ---
        function stylePagination() {
            const paginationContainer = document.querySelector('.pagination');
            if (!paginationContainer) return;

            const paginationLinks = paginationContainer.querySelectorAll('.page-link');
            if (paginationLinks.length === 0) return;

            // SVG icons for previous and next arrows
            const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>`;
            const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>`;

            paginationLinks.forEach(link => {
                const content = link.innerHTML.trim();
                // Replace text symbols with SVG icons
                if (content.includes('&lt;') || content.includes('«')) {
                    link.innerHTML = prevIcon;
                    link.setAttribute('aria-label', 'Previous');
                } else if (content.includes('&gt;') || content.includes('»')) {
                    link.innerHTML = nextIcon;
                    link.setAttribute('aria-label', 'Next');
                }
            });
        }

        // Call the styling function once the DOM is ready
        stylePagination();
    });
</script>

<?php init_tail(); ?>