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
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Trạng thái VC</th>
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
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền chuyển khoản trả khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền cần thu</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Giảm giá trước khi hoàn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí hoàn đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tổng số tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Xác thực CK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Hình ảnh xác thực CK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Chênh lệch phí vận chuyển (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Sàn trợ giá</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí cố định, giao dịch (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí hoa hồng nền tảng (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí hoa hồng (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí dịch vụ SFP (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí thanh toán (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phí dịch vụ (Sàn TMĐT)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền thanh toán thực tế</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Lợi nhuận đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số tiền khách hàng đã chi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Phương thức thanh toán</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Vùng miền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ngày đối soát</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Bán tại quầy</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Kho hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Vị trí lô</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Vị trí kệ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Dự kiến nhận hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ghi chú để in</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ghi chú nội bộ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ghi trú trao đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ghi chú trao đổi (Theo đơn hàng)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Affiliate</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Tiền hàng đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Sinh nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Mã khuyến mãi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Mã khuyến mãi (Tóm tắt)</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Số đơn của khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Delay giao</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Cấp độ khách hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thẻ Pancake</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ghi chú DVVC</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Dòng thời gian cập nhật trạng thái</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Mã bài viết</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Lịch sử đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Lịch sử NV xử lý đơn hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Liên kết theo dõi đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Thông tin chuyển hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300">Ngày hoàn đơn</th>
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
                                        <!-- Tổng số lượng SP -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center">
                                            <?= $firstItem['quantity'] ?? 0 ?>
                                        </td>
                                        <!-- Số lượng đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center">
                                            <?= html_escape($firstItem['exchange_count'] ?? 0) ?>
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
                                            <?= number_format($total_discount) ?>
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
                                        <td></td>
                                        <!-- Lợi nhuận đơn hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($cod) ?>
                                        </td>
                                        <!-- Số tiền khách hàng đã chi -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= number_format($cod) ?>
                                        </td>
                                        <!-- Vùng miền -->
                                        <td></td>
                                        <!-- Ngày đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center" rowspan="<?= $itemsCount ?>">
                                            <?= isset($reconciliationTime) ? date('d/m/Y', strtotime($reconciliationTime)) : '' ?>
                                        </td>
                                        <!-- Bán tại quầy -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>">Online</td>
                                        <!-- Kho hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle" rowspan="<?= $itemsCount ?>"><?= html_escape($order['warehouse_info']['name'] ?? '') ?></td>
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





                                    </tr>

                                    <?php if ($itemsCount > 1) : ?>
                                        <?php for ($i = 1; $i < $itemsCount; $i++) : ?>
                                            <?php $item = $items[$i]; ?>
                                            <tr class="hover:tw-bg-gray-50">
                                                <td class="tw-px-4 tw-py-3 tw-max-w-xs tw-truncate tw-border tw-border-gray-300 align-middle text-center" title="<?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>">
                                                    <?= html_escape($item['variation_info']['name'] ?? 'N/A') ?>
                                                </td>
                                                <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle text-center"><?= $item['quantity'] ?? 0 ?></td>
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