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

    [x-cloak] {
        display: none !important;
    }

    .row-click {
        cursor: pointer;
    }

    .modal-mask {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .65);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 16px;
    }

    .modal-card {
        background: #fff;
        border-radius: 12px;
        width: min(1200px, 96vw);
        max-height: 84vh;
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border);
        box-shadow: 0 10px 30px rgba(17, 24, 39, .25);
    }

    .modal-header,
    .modal-footer {
        padding: 12px 16px
    }

    .modal-header {
        border-bottom: 1px solid var(--border)
    }

    .modal-footer {
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 8px
    }

    .modal-body {
        padding: 16px;
        overflow: auto
    }

    .linklike {
        color: var(--primary);
        text-decoration: underline;
        font-weight: 600;
        cursor: pointer;
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                            <option value="30" <?= ($this->input->get('page_size') == 30 || !$this->input->get('page_size')) ? 'selected' : '' ?>>30</option>
                            <option value="50" <?= ($this->input->get('page_size') == 50) ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= ($this->input->get('page_size') == 100) ? 'selected' : '' ?>>100</option>
                            <option value="150" <?= ($this->input->get('page_size') == 150) ? 'selected' : '' ?>>150</option>
                            <option value="200" <?= ($this->input->get('page_size') == 200) ? 'selected' : '' ?>>200</option>
                            <option value="500" <?= ($this->input->get('page_size') == 500) ? 'selected' : '' ?>>500</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        <a href="<?= admin_url('pancake_sync') ?>" class="btn btn-outline">Đặt lại</a>
                    </div>
                </form>
                <div>
                    <button id="sync-button" class="btn btn-primary">
                        <i class="fa fa-refresh"></i> Đồng bộ từ Pancake
                    </button>

                    <button id="recent-sync-button" class="btn btn-info" type="button">
                        Sync 1.000 đơn gần nhất
                    </button>
                </div>

                <?php if (isset($total)) : ?>
                    <div class="results-count">
                        Tìm thấy <?= $total ?> kết quả
                        <?php if ($this->input->get('search')) : ?>
                            cho từ khóa "<strong><?= html_escape($this->input->get('search')) ?></strong>"
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12" id="sync-progress-container" style="display: none; margin-top: 15px;">
                <p id="sync-status-text" class="mbot5">Đang chuẩn bị...</p>
                <div class="progress">
                    <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>
            </div>

            <div class="tw-mb-6">
                <h4 class="tw-text-2xl tw-font-bold tw-text-gray-800">
                    Danh sách Đơn hàng
                    <span class="tw-ml-2 tw-text-lg tw-font-medium tw-text-gray-500">(Tổng cộng: <?= $total ?? 0 ?>)</span>
                </h4>
                <p class="tw-text-gray-600 tw-mt-1">Quản lý và theo dõi tất cả các đơn hàng tại đây.</p>
            </div>


            <div class="tw-bg-white tw-shadow-md tw-rounded-lg">
                <div class="tw-overflow-x-auto table-container">
                    <table class="tw-w-full tw-min-w-max tw-text-sm tw-text-left tw-text-gray-700 tw-border tw-border-gray-300 tw-overflow-x-auto">
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
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tài khoản đẩy đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">NV gửi hàng đi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm tạo đơn</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái</th>
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
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Chờ hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Hoàn một phần</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Hoàn một phần</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã thu tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã thu tiền</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Chờ xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Chờ xác nhận</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Thời điểm cập nhật trạng thái Đã đặt hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ngày cập nhật trạng thái Đã đặt hàng</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tình trạng kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Ghi chú sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã rút gọn GHTK</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Đơn vị tiền tệ</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Địa chỉ kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">SĐT kho</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền từ đơn gốc</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tiền trả lại khách</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Số lại sản phẩm</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Username</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Email</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã đối tác</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã đơn hàng đổi</th>
                                <th scope="col" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Mã đơn hàng gốc</th>
                            </tr>
                        </thead>
                        <?php if (!empty($orders)) : ?>
                            <?php
                            if (!function_exists('get_mobile_network')) {
                                function get_mobile_network($phoneNumber)
                                {
                                    if (empty($phoneNumber)) return '';
                                    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                                    if (substr($phoneNumber, 0, 2) == '84') $phoneNumber = '0' . substr($phoneNumber, 2);
                                    if (strlen($phoneNumber) != 10) return '';
                                    $prefix = substr($phoneNumber, 0, 3);
                                    $networks = [
                                        'Viettel' => ['086', '096', '097', '098', '032', '033', '034', '035', '036', '037', '038', '039'],
                                        'MobiFone' => ['089', '090', '093', '070', '079', '077', '076', '078'],
                                        'VinaPhone' => ['088', '091', '094', '083', '084', '085', '081', '082'],
                                        'Vietnamobile' => ['092', '056', '058'],
                                        'Gmobile' => ['099', '059'],
                                        'Itelecom' => ['087']
                                    ];
                                    foreach ($networks as $networkName => $prefixes) {
                                        if (in_array($prefix, $prefixes)) return $networkName;
                                    }
                                    return '';
                                }
                            }
                            ?>

                            <?php foreach ($orders as $index => $order) : ?>
                                <?php
                                // ====== Chuẩn bị data nhanh cho row & modal ======
                                $statusMap = [
                                    'new' => ['text' => 'Mới', 'class' => 'tw-bg-blue-100 tw-text-blue-800'],
                                    'wait_submit' => ['text' => 'Chờ xác nhận', 'class' => 'tw-bg-yellow-100 tw-text-yellow-800'],
                                    'submitted' => ['text' => 'Đã xác nhận', 'class' => 'tw-bg-indigo-100 tw-text-indigo-800'],
                                    'packing' => ['text' => 'Đang đóng hàng', 'class' => 'tw-bg-purple-100 tw-text-purple-800'],
                                    'shipped' => ['text' => 'Đã gửi hàng', 'class' => 'tw-bg-cyan-100 tw-text-cyan-800'],
                                    'delivered' => ['text' => 'Đã nhận', 'class' => 'tw-bg-green-100 tw-text-green-800'],
                                    'returning' => ['text' => 'Đang hoàn', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
                                    'returned' => ['text' => 'Đã hoàn', 'class' => 'tw-bg-lime-100 tw-text-lime-800'],
                                    'canceled' => ['text' => 'Đã huỷ', 'class' => 'tw-bg-red-100 tw-text-red-800'],
                                    'pending' => ['text' => 'Đang chuyển hàng', 'class' => 'tw-bg-orange-100 tw-text-orange-800'],
                                    'removed' => ['text' => 'Đã xoá', 'class' => 'tw-bg-gray-100 tw-text-gray-800'],
                                ];
                                $statusKey   = $order['status_name'] ?? 'new';
                                $statusInfo  = $statusMap[$statusKey] ?? ['text' => 'Không xác định', 'class' => 'tw-bg-gray-100 tw-text-gray-800'];

                                // Các số liệu tiền
                                $cod                              = $order['cod'] ?? 0;
                                $partner_fee                      = $order['partner_fee'] ?? 0;
                                $codDoiSoat                       = $order['partner']['cod'] ?? 0;
                                $totalPrice                       = $order['total_price'] ?? 0;
                                $shipping_fee                     = $order['shipping_fee'] ?? 0;
                                $total_price_after_sub_discount   = $order['total_price_after_sub_discount'] ?? 0;
                                $surcharge                        = $order['surcharge'] ?? 0;
                                $fee_marketplace                  = $order['fee_marketplace'] ?? 0;
                                $total_discount                   = $order['total_discount'] ?? 0;
                                $money_to_collect                 = $order['money_to_collect'] ?? 0;
                                $total_fee_partner                = $order['partner']['total_fee'] ?? 0;
                                $buyer_total_amount               = $order['buyer_total_amount'] ?? 0;

                                $total_fee_marketplace_voucher    = $order['advanced_platform_fee']['marketplace_voucher'] ?? 0;
                                $total_fee_paymentFee             = $order['advanced_platform_fee']['payment_fee'] ?? 0;
                                $total_fee_platform_commission    = $order['advanced_platform_fee']['platform_commission'] ?? 0;
                                $total_fee_platform_affiliate_commission = $order['advanced_platform_fee']['affiliate_commission'] ?? 0;
                                $total_fee_sfp_service_fee        = $order['advanced_platform_fee']['sfp_service_fee'] ?? 0;
                                $total_fee_seller_transaction_fee = $order['advanced_platform_fee']['seller_transaction_fee'] ?? 0;
                                $total_fee_service_fee            = $order['advanced_platform_fee']['service_fee'] ?? 0;

                                $extendCode                       = $order['histories'][2]['extend_code']['new'] ?? null;
                                $tracking_id                      = $order['partner']['extend_code'] ?? null;
                                $staffconfirm                     = $order['status_history'][1]['editor']['name'] ?? null;
                                $promotionName                    = $order['activated_promotion_advances'][0]['promotion_advance_info']['name'] ?? '';
                                $totalOrders                      = $order['customer']['order_count'] ?? 0;
                                $tagPancake                       = $order['customer']['conversation_tags'] ?? [];

                                // Ngày đối soát từ extend_update
                                $reconciliationTime = null;
                                foreach (($order['partner']['extend_update'] ?? []) as $update) {
                                    if (($update['status'] ?? '') === 'Đã đối soát') {
                                        $reconciliationTime = $update['update_at'] ?? null;
                                        break;
                                    }
                                }

                                // UTM
                                $p_utm_source   = $order['p_utm_source'] ?? null;
                                $p_utm_medium   = $order['p_utm_medium'] ?? null;
                                $p_utm_campaign = $order['p_utm_campaign'] ?? null;
                                $p_utm_term     = $order['p_utm_term'] ?? null;
                                $p_utm_content  = $order['p_utm_content'] ?? null;
                                $p_utm_id       = $order['p_utm_id'] ?? null;

                                // Gộp list sản phẩm (support combo)
                                $products_to_display = [];
                                if (!empty($order['items']) && !empty($order['items'][0]['is_composite']) && !empty($order['items'][0]['components'])) {
                                    $products_to_display = $order['items'][0]['components'];
                                } else {
                                    $products_to_display = $order['items'] ?? [];
                                }

                                // Để không còn rowspan và hàng con => fix cứng 1 hàng/đơn
                                $itemsCount = 1;
                                ?>

                                <!-- Mỗi đơn là một tbody độc lập để modal không ảnh hưởng đơn khác -->
                                <tbody x-data="{ openProducts:false }">
                                    <!-- Hàng chính: click ANYWHERE mở modal; click Mã Đơn có @click.stop để không double-trigger -->
                                    <tr class="hover:tw-bg-gray-50 row-click" @click="openProducts = true">
                                        <!-- STT -->
                                        <td class="tw-px-4 tw-py-3 tw-text-center tw-border tw-border-gray-300 align-middle"><?= $index + 1 ?></td>

                                        <!-- Mã Đơn (clickable) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <span><?= html_escape($order['id'] ?? '') ?></span>
                                        </td>

                                        <!-- Ngày tạo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            if (isset($order['inserted_at'])) {
                                                $d = new DateTime($order['inserted_at'], new DateTimeZone('UTC'));
                                                $d->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                                echo $d->format('d/m/Y');
                                            } else echo 'Không có thông tin';
                                            ?>
                                        </td>

                                        <!-- Mã vận đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['partner']['order_number_vtp'] ?? '') ?></td>

                                        <!-- Thẻ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= !empty($order['tags']) ? html_escape(implode(', ', array_column($order['tags'], 'name'))) : '' ?>
                                        </td>

                                        <!-- Khách Hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['shipping_address']['full_name'] ?? '') ?></td>

                                        <!-- Số điện thoại -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= html_escape($order['shipping_address']['phone_number'] ?? '') ?>
                                        </td>

                                        <!-- Nhà mạng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= html_escape(get_mobile_network($order['shipping_address']['phone_number'] ?? '')) ?>
                                        </td>

                                        <!-- Khách Mới / Cũ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center">
                                            <?= (in_array($order['customer']['level']['name'] ?? null, [null, 'Mua lần 1'])) ? 'Mới' : 'Cũ' ?>
                                        </td>

                                        <!-- Phường/Xã -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['shipping_address']['commune_name'] ?? '') ?></td>

                                        <!-- Quận/Huyện -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['shipping_address']['district_name'] ?? '') ?></td>

                                        <!-- Tỉnh/TP -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['shipping_address']['province_name'] ?? '') ?></td>

                                        <!-- Người xử lý -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></td>

                                        <!-- Nhân viên CSKH -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['assigning_care']['name'] ?? '') ?></td>

                                        <!-- Marketer -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['marketer']['name'] ?? '') ?></td>

                                        <!-- Trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <span class="tw-inline-block tw-px-2 tw-py-1 tw-text-xs tw-font-medium tw-rounded-full <?= $statusInfo['class'] ?>">
                                                <?= html_escape($statusInfo['text']) ?>
                                            </span>
                                        </td>

                                        <!-- Page Id -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['page_id'] ?? '') ?></td>

                                        <!-- Ad Id -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['ad_id'] ?? '') ?></td>

                                        <!-- Nguồn quảng cáo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['ads_source'] ?? '') ?></td>

                                        <!-- Nguồn đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= (($order['order_sources_name'] ?? '') === 'Affiliate') ? 'CTV' : html_escape($order['order_sources_name'] ?? '') ?>
                                        </td>

                                        <!-- Nguồn chi tiết -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['account_name'] ?? '') ?></td>

                                        <!-- Chat page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['page']['name'] ?? '') ?></td>

                                        <!-- Thời gian khách nhắn tin đầu tiên đến page -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape('') ?></td>

                                        <!-- Người tạo -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['creator']['name'] ?? 'Hệ thống') ?></td>

                                        <!-- NV xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($staffconfirm) ?></td>

                                        <!-- NV đầu tiên xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $firstConf = '';
                                            if (!empty($order['status_history'])) {
                                                foreach ($order['status_history'] as $history) {
                                                    if (($history['status'] ?? null) == 1 && !empty($history['editor']['name'])) {
                                                        $firstConf = $history['editor']['name'];
                                                        break;
                                                    }
                                                }
                                            }
                                            echo html_escape($firstConf);
                                            ?>
                                        </td>

                                        <!-- Nhân viên cập nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['last_editor']['name'] ?? 'Hệ thống') ?></td>

                                        <!-- Thời gian CSKH -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= isset($order['time_assign_care']) ? date('d/m/Y', strtotime($order['time_assign_care'])) : '' ?></td>

                                        <!-- Đơn vị VC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['partner']['partner_name'] ?? '') ?></td>

                                        <!-- Trạng thái VC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['partner']['extend_update'][0]['status'] ?? '') ?></td>

                                        <!-- Ngày đẩy sang ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= isset($order['time_send_partner']) ? date('d/m/Y', strtotime($order['time_send_partner'] . ' +7 hours')) : '' ?>
                                        </td>

                                        <!-- Lý do hoàn/hủy từ ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['partner']['extend_update'][0]['note'] ?? '') ?></td>

                                        <!-- Lý do hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['returnedreason'] ?? '') ?></td>

                                        <!-- COD -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($cod) ?></td>

                                        <!-- Phí trả ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($partner_fee) ?></td>

                                        <!-- COD đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($codDoiSoat) ?></td>

                                        <!-- Tổng tiền đơn (trừ chiết khấu) -->
                                        <td class="tw-px-6 tw-py-4 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $total = 0;
                                            foreach (($order['items'] ?? []) as $item) {
                                                $price = (($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0)) - ($item['total_discount'] ?? 0);
                                                $total += $price;
                                            }
                                            $total -= $total_discount;
                                            echo number_format($total, 0, ',', '.');
                                            ?>
                                        </td>

                                        <!-- Tổng (trừ phí ship) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalPrice - $shipping_fee) ?></td>

                                        <!-- Doanh số -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($totalPrice ?? 0)) ?></td>

                                        <!-- Doanh số trước hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($totalPrice ?? 0)) ?></td>

                                        <!-- Doanh thu đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['status_name'] == 'returned' || $order['status_name'] == 'canceled') ? 0 : ($total_price_after_sub_discount ?? 0)) ?></td>

                                        <!-- Doanh thu chưa trừ phí sàn -->
                                        <td class="tw-px-6 tw-py-4 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $total2 = 0;
                                            foreach (($order['items'] ?? []) as $item) {
                                                $price = ($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0);
                                                $total2 += $price;
                                            }
                                            $total2 -= $total_discount;
                                            echo number_format($total2, 0, ',', '.');
                                            ?>
                                        </td>

                                        <!-- Phụ thu -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($surcharge) ?></td>

                                        <!-- Giảm giá -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_discount) ?></td>

                                        <!-- Phí sàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($fee_marketplace) ?></td>

                                        <!-- Giảm trực tiếp -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_discount) ?></td>

                                        <!-- Trị giá đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalPrice) ?></td>

                                        <!-- Trị giá đơn đã chiết khấu -->
                                        <td class="tw-px-6 tw-py-4 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $total3 = 0;
                                            foreach (($order['items'] ?? []) as $item) {
                                                $price = ($item['variation_info']['retail_price'] ?? 0) * ($item['quantity'] ?? 0);
                                                $total3 += $price;
                                            }
                                            $total3 -= $total_discount;
                                            echo number_format($total3, 0, ',', '.');
                                            ?>
                                        </td>

                                        <!-- Phí VC thu khách -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($shipping_fee) ?></td>

                                        <!-- Tổng tiền đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalPrice) ?></td>

                                        <!-- Thực thu từ ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($money_to_collect) ?></td>

                                        <!-- Tổng phí đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_partner) ?></td>

                                        <!-- Tiền trả trước -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Chuyển trước -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['transfer_money']) ?? 0) ?></td>

                                        <!-- Tiền khách đưa -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Tiền mặt -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Quẹt thẻ -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['charged_by_card']) ?? 0) ?></td>

                                        <!-- MoMo -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['charged_by_momo']) ?? 0) ?></td>

                                        <!-- VNPAY -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['charged_by_vnpay']) ?? 0) ?></td>

                                        <!-- ONEPAY -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['charged_by_onepay']) ?? 0) ?></td>

                                        <!-- QRPay -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format(($order['charged_by_qrpay']) ?? 0) ?></td>

                                        <!-- Tiền CK trả khách -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Tiền cần thu -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($cod) ?></td>

                                        <!-- Giảm giá trước khi hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_discount) ?></td>

                                        <!-- Phí hoàn đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Tổng số tiền -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalPrice) ?></td>

                                        <!-- Xác thực CK -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalPrice) ?></td>

                                        <!-- Chênh lệch phí VC (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Sàn trợ giá -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_marketplace_voucher) ?></td>

                                        <!-- Phí cố định, giao dịch (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_paymentFee) ?></td>

                                        <!-- Phí hoa hồng nền tảng (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_platform_commission) ?></td>

                                        <!-- Phí hoa hồng (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_platform_affiliate_commission) ?></td>

                                        <!-- Phí SFP (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_sfp_service_fee) ?></td>

                                        <!-- Phí thanh toán (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_seller_transaction_fee) ?></td>

                                        <!-- Phí dịch vụ (Sàn) -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($total_fee_service_fee) ?></td>

                                        <!-- Tiền thanh toán thực tế -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($buyer_total_amount) ?></td>

                                        <!-- Lợi nhuận đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($cod) ?></td>

                                        <!-- Số tiền KH đã chi -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= ($order['status'] == 3) ? number_format($cod) : 0 ?></td>

                                        <!-- Phương thức thanh toán -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php $isPrepaid = !empty($order['prepaid']) && $order['prepaid'] > 0;
                                            $class = $isPrepaid ? 'tw-text-green-600 tw-font-bold' : 'tw-text-orange-600 tw-font-bold';
                                            $paymentMethod = $isPrepaid ? 'Chuyển khoản' : 'COD';
                                            echo "<span class='{$class}'>" . $paymentMethod . "</span>";
                                            ?>
                                        </td>

                                        <!-- Vùng miền -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $mienBac = ['Hà Nội', 'Hải Phòng', 'Quảng Ninh', 'Hà Giang', 'Cao Bằng', 'Bắc Kạn', 'Lạng Sơn', 'Tuyên Quang', 'Thái Nguyên', 'Phú Thọ', 'Bắc Giang', 'Lào Cai', 'Yên Bái', 'Điện Biên', 'Hoà Bình', 'Lai Châu', 'Sơn La', 'Bắc Ninh', 'Hà Nam', 'Hải Dương', 'Hưng Yên', 'Nam Định', 'Ninh Bình', 'Thái Bình', 'Vĩnh Phúc'];
                                            $mienTrung = ['Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị', 'Thừa Thiên Huế', 'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa', 'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông', 'Lâm Đồng'];
                                            $mienNam = ['Hồ Chí Minh', 'Cần Thơ', 'Bình Phước', 'Bình Dương', 'Đồng Nai', 'Tây Ninh', 'Bà Rịa - Vũng Tàu', 'Long An', 'Đồng Tháp', 'Tiền Giang', 'An Giang', 'Bến Tre', 'Vĩnh Long', 'Trà Vinh', 'Hậu Giang', 'Kiên Giang', 'Sóc Trăng', 'Bạc Liêu', 'Cà Mau'];
                                            $province = $order['shipping_address']['province_name'] ?? '';
                                            $normalizedProvince = str_replace(['Tỉnh ', 'Thành phố '], '', $province);
                                            $region = in_array($normalizedProvince, $mienBac) ? 'Miền Bắc' : (in_array($normalizedProvince, $mienTrung) ? 'Miền Trung' : (in_array($normalizedProvince, $mienNam) ? 'Miền Nam' : ''));
                                            $class = $region === 'Miền Bắc' ? 'tw-text-blue-600 tw-font-bold' : ($region === 'Miền Trung' ? 'tw-text-green-600 tw-font-bold' : ($region === 'Miền Nam' ? 'tw-text-red-600 tw-font-bold' : ''));
                                            echo "<span class='{$class}'>" . html_escape($region) . "</span>";
                                            ?>
                                        </td>

                                        <!-- Ngày đối soát -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= isset($reconciliationTime) ? date('d/m/Y', strtotime($reconciliationTime)) : '' ?></td>

                                        <!-- Bán tại quầy -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">Online</td>

                                        <!-- Kho hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['warehouse_info']['name'] ?? '') ?></td>

                                        <!-- Vị trí lô -->
                                        <td></td>

                                        <!-- Vị trí kệ -->
                                        <td></td>

                                        <!-- Dự kiến nhận hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle">
                                            <?= isset($order['estimate_delivery_date']) ? date('d/m/Y', strtotime($order['estimate_delivery_date'])) : '' ?>
                                        </td>

                                        <!-- Ghi chú để in -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text"><?= html_escape($order['note_print'] ?? '') ?></td>

                                        <!-- Ghi chú nội bộ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text"><?= html_escape($order['note'] ?? '') ?></td>

                                        <!-- Affiliate -->
                                        <td></td>

                                        <!-- Tiền hàng đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Sinh nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= isset($order['customer']['date_of_birth']) ? date('d/m', strtotime($order['customer']['date_of_birth'])) : '' ?>
                                        </td>

                                        <!-- Mã khuyến mãi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php if (!empty($promotionName)): ?>Mã khuyến mãi nâng cao: <?= html_escape($promotionName) ?><?php endif; ?>
                                        </td>

                                        <!-- Mã khuyến mãi (tóm tắt) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($promotionName) ?></td>

                                        <!-- Số đơn của khách -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= number_format($totalOrders) ?></td>

                                        <!-- Delay giao -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= (isset($order['sub_status']) && $order['sub_status'] == 1) ? 'Cần xử lý' : '' ?></td>

                                        <!-- Cấp độ khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center"><?= html_escape($order['customer']['level']['name'] ?? '') ?></td>

                                        <!-- Thẻ Pancake -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center">
                                            <?php if (!empty($tagPancake)): ?>
                                                <div x-data="{ open:false }">
                                                    <button @click.stop="open = true" class="tw-bg-gray-200 hover:tw-bg-gray-300 tw-text-black tw-font-bold tw-py-2 tw-px-4 tw-rounded tw-text-xs">
                                                        Xem Tags (<?= count($tagPancake) ?>)
                                                    </button>
                                                    <div x-show="open" x-transition
                                                        class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black tw-bg-opacity-75"
                                                        style="display:none;" @click.self="open=false" @keydown.escape.window="open=false">
                                                        <div class="tw-bg-white tw-rounded-lg tw-shadow-xl tw-w-full tw-max-w-md">
                                                            <div class="tw-p-4 tw-border-b tw-flex tw-justify-between tw-items-center">
                                                                <h3 class="tw-text-lg tw-font-semibold">Tags Pancake của Đơn #<?= html_escape($order['id'] ?? '') ?></h3>
                                                                <button @click="open = false" class="tw-text-gray-500 hover:tw-text-gray-800">&times;</button>
                                                            </div>
                                                            <div class="tw-p-4 tw-text-left">
                                                                <div class="tw-flex tw-flex-wrap tw-gap-2">
                                                                    <?php foreach ($tagPancake as $tag): ?>
                                                                        <span class="tw-bg-green-100 tw-text-green-800 tw-text-sm tw-font-semibold tw-px-3 tw-py-1 tw-rounded-full"><?= html_escape($tag) ?></span>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Ghi chú ĐVVC -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 truncate-text align-middle text-center">
                                            <?= !empty($order['partner']['extend_update'][0]['note']) ? $order['partner']['extend_update'][0]['note'] : '' ?>
                                        </td>

                                        <!-- Dòng thời gian cập nhật trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center">
                                            <?php
                                            $statusHistoryMap = [0 => 'Mới', 1 => 'Đã xác nhận', 8 => 'Đang đóng hàng', 2 => 'Đã gửi hàng', 3 => 'Đã giao hàng', 4 => 'Hoàn thành', 5 => 'Đã hủy', 6 => 'Đang chuyển hoàn', 7 => 'Đã chuyển hoàn'];
                                            $historyDisplay = [];
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                $statusId = $history['status'] ?? null;
                                                $updatedAt = $history['updated_at'] ?? null;
                                                if ($updatedAt === null) continue;
                                                $statusName = $statusHistoryMap[$statusId] ?? 'Không xác định';
                                                $date = new DateTime($updatedAt, new DateTimeZone('UTC'));
                                                $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                                $historyDisplay[] = "{$statusName} - " . $date->format('d/m/Y');
                                            }
                                            echo implode(";<br>", $historyDisplay);
                                            ?>
                                        </td>

                                        <!-- Mã bài viết -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['post_id']) ?></td>

                                        <!-- Liên kết theo dõi đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center"><?= html_escape($order['link_confirm_order'] ?? '') ?></td>

                                        <!-- Thông tin chuyển hàng (modal riêng) -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <div x-data="{ open:false }">
                                                <button @click.stop="open = true" class="tw-bg-red-100 tw-text-black tw-font-bold tw-py-2 tw-px-4 tw-rounded tw-text-xs">Xem thông tin</button>
                                                <div x-show="open" x-transition
                                                    class="tw-fixed tw-inset-0 tw-z-50 tw-flex tw-items-center tw-justify-center tw-bg-black tw-bg-opacity-75"
                                                    style="display:none;" @click.self="open=false" @keydown.escape.window="open=false">
                                                    <div class="tw-bg-white tw-rounded-lg tw-shadow-xl tw-w-full tw-max-w-2xl tw-max-h-[80vh] tw-overflow-y-auto">
                                                        <div class="tw-p-4 tw-border-b tw-flex tw-justify-between tw-items-center">
                                                            <h3 class="tw-text-lg tw-font-semibold">Xem thông tin #<?= $order['id'] ?? '' ?></h3>
                                                            <button @click="open=false" class="tw-text-gray-500 hover:tw-text-gray-800">&times;</button>
                                                        </div>
                                                        <div class="tw-p-4 tw-text-left tw-text-sm">
                                                            <?php
                                                            $trackingLog = [];
                                                            foreach (($order['partner']['extend_update'] ?? []) as $update) {
                                                                $parts = [];
                                                                $status = htmlspecialchars($update['status'] ?? 'N/A');
                                                                $parts[] = "<b>{$status}</b>";
                                                                if (!empty($update['update_at'])) {
                                                                    $date = new DateTime($update['update_at']);
                                                                    $parts[] = $date->format('d/m/Y');
                                                                }
                                                                $note = htmlspecialchars($update['note'] ?? '');
                                                                if ($note) $parts[] = $note;
                                                                $trackingLog[] = implode("<br>", $parts) . ';';
                                                            }
                                                            echo implode("<hr class='tw-my-3 tw-border-dashed'>", $trackingLog);
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Ngày hoàn Đơn -->
                                        <td></td>

                                        <!-- Ngày nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?= isset($order['partner']['extend_update'][0]['updated_at']) ? date('d/m/Y', strtotime($order['partner']['extend_update'][0]['updated_at'])) : '' ?>
                                        </td>

                                        <!-- UTM source -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_source) ?></td>
                                        <!-- UTM medium -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_medium) ?></td>
                                        <!-- UTM campaign -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_campaign) ?></td>
                                        <!-- UTM term -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_term) ?></td>
                                        <!-- UTM content -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_content) ?></td>
                                        <!-- UTM ID -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($p_utm_id) ?></td>

                                        <!-- Thẻ khách hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= implode($order['customer']['tags'] ?? []) ?></td>

                                        <!-- Link -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text text-center"><?= html_escape($order['link']) ?></td>

                                        <!-- FFM ID -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($tracking_id ?? '') ?></td>

                                        <!-- Tài khoản đẩy đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text"></td>

                                        <!-- NV gửi hàng đi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle truncate-text"></td>

                                        <!-- Thời điểm tạo đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            if (isset($order['inserted_at'])) {
                                                $date = new DateTime($order['inserted_at'], new DateTimeZone('UTC'));
                                                $date->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                                echo $date->format('d/m/Y');
                                            } else {
                                                echo 'Không có thông tin';
                                            }
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= isset($order['updated_at']) ? date('d/m/Y', strtotime($order['updated_at'] . ' +7 hours')) : 'N/A' ?></td>

                                        <!-- Thời điểm cập nhật trạng thái -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $deliveryDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 3) {
                                                    $deliveryDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($deliveryDate) ? date('d/m/Y', strtotime($deliveryDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Miễn phí ship -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php if ($order['is_free_shipping'] ?? false) : ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#22c55e" width="24" height="24">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                                </svg>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Mã tuỳ chỉnh đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['id'] ?? '') ?></td>

                                        <!-- Thời điểm cập nhật sang Đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $deliveryDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 3) {
                                                    $deliveryDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($deliveryDate) ? date('d/m/Y', strtotime($deliveryDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm xác nhận đơn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $confirmationDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 1) {
                                                    $confirmationDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($confirmationDate) ? date('d/m/Y', strtotime($confirmationDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm đầu tiên xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $firstConfirmationDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 1) {
                                                    $firstConfirmationDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($firstConfirmationDate) ? date('d/m/Y', strtotime($firstConfirmationDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $shippingDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 9) {
                                                    $shippingDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($shippingDate) ? date('d/m/Y', strtotime($shippingDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật chờ hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $waitingDate = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 11) {
                                                    $waitingDate = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($waitingDate) ? date('d/m/Y', strtotime($waitingDate)) : '';
                                            ?>
                                        </td>

                                        <!-- Nhu cầu khách hàng -->
                                        <td></td>

                                        <!-- Mã CTV -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $affiliateId = null;
                                            foreach (($order['histories'] ?? []) as $history) {
                                                if (isset($history['third_party_infomation']['new']['affiliate_display_id'])) {
                                                    $affiliateId = $history['third_party_infomation']['new']['affiliate_display_id'];
                                                    break;
                                                }
                                            }
                                            echo $affiliateId;
                                            ?>
                                        </td>

                                        <!-- Thời điểm gắn/xóa thẻ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $tagHistoryLines = [];
                                            foreach (($order['histories'] ?? []) as $history) {
                                                if (isset($history['tags'])) {
                                                    $old = !empty($history['tags']['old']) ? array_column($history['tags']['old'], 'name') : [];
                                                    $new = !empty($history['tags']['new']) ? array_column($history['tags']['new'], 'name') : [];
                                                    $oldStr = implode(', ', $old);
                                                    $newStr = implode(', ', $new);
                                                    $date = date('d/m/Y', strtotime($history['updated_at']));
                                                    if (empty($oldStr) && !empty($newStr)) $tagHistoryLines[] = "Thêm thẻ {$newStr} - {$date}";
                                                    else if (!empty($oldStr) && !empty($newStr) && $oldStr !== $newStr) $tagHistoryLines[] = "{$oldStr} -> {$newStr} - {$date}";
                                                }
                                            }
                                            foreach ($tagHistoryLines as $line) echo htmlspecialchars($line) . "<br>";
                                            ?>
                                        </td>

                                        <!-- Thời gian phân công NV -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= isset($order['time_assign_seller']) ? date('d/m/Y', strtotime($order['time_assign_seller'])) : '' ?></td>

                                        <!-- NV đầu tiên cập nhật chờ hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $editorName = '';
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 11 && !empty($history['editor']['name'])) {
                                                    $editorName = $history['editor']['name'];
                                                    break;
                                                }
                                            }
                                            echo html_escape($editorName);
                                            ?>
                                        </td>

                                        <!-- NV xử lý sản phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></td>

                                        <!-- Dịch vụ vận chuyển -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $displayText = '';
                                            if (!empty($order['partner']['service_partner']['orders'][0]['base_info']['service_type'])) {
                                                $serviceType = $order['partner']['service_partner']['orders'][0]['base_info']['service_type'];
                                                $displayText = $serviceType === 'Instant' ? 'Hỏa tốc' : $serviceType;
                                            }
                                            echo $displayText;
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Mới -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $newStatusTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 0) {
                                                    $newStatusTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($newStatusTime) ? date('d/m/Y', strtotime($newStatusTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Mới -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $newStatusTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 0) {
                                                    $newStatusTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($newStatusTime) ? date('d/m/Y', strtotime($newStatusTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $confirmedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 1) {
                                                    $confirmedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($confirmedTime) ? date('d/m/Y', strtotime($confirmedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $confirmedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 1) {
                                                    $confirmedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($confirmedTime) ? date('d/m/Y', strtotime($confirmedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã gửi hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $shippedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 2) {
                                                    $shippedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($shippedTime) ? date('d/m/Y', strtotime($shippedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã gửi hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $shippedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 2) {
                                                    $shippedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($shippedTime) ? date('d/m/Y', strtotime($shippedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $deliveredTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 3) {
                                                    $deliveredTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($deliveredTime) ? date('d/m/Y', strtotime($deliveredTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $deliveredTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 3) {
                                                    $deliveredTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($deliveredTime) ? date('d/m/Y', strtotime($deliveredTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đang hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $returningTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 4) {
                                                    $returningTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($returningTime) ? date('d/m/Y', strtotime($returningTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đang hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $returningTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 4) {
                                                    $returningTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($returningTime) ? date('d/m/Y', strtotime($returningTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $returnedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 5) {
                                                    $returnedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($returnedTime) ? date('d/m/Y', strtotime($returnedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã hoàn -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $returnedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 5) {
                                                    $returnedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($returnedTime) ? date('d/m/Y', strtotime($returnedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã huỷ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $canceledTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 6) {
                                                    $canceledTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($canceledTime) ? date('d/m/Y', strtotime($canceledTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã huỷ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $canceledTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 6) {
                                                    $canceledTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($canceledTime) ? date('d/m/Y', strtotime($canceledTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $pendingTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 9) {
                                                    $pendingTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($pendingTime) ? date('d/m/Y', strtotime($pendingTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Chờ chuyển hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $pendingTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 9) {
                                                    $pendingTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($pendingTime) ? date('d/m/Y', strtotime($pendingTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Chờ hàng -->
                                        <td></td>

                                        <!-- Ngày cập nhật trạng thái Chờ hàng -->
                                        <td></td>

                                        <!-- Thời điểm cập nhật trạng thái Hoàn một phần -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $partReturnedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 15) {
                                                    $partReturnedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($partReturnedTime) ? date('d/m/Y', strtotime($partReturnedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Hoàn một phần -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $partReturnedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 15) {
                                                    $partReturnedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($partReturnedTime) ? date('d/m/Y', strtotime($partReturnedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã thu tiền -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $paidTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 16) {
                                                    $paidTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($paidTime) ? date('d/m/Y', strtotime($paidTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã thu tiền -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $paidTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 16) {
                                                    $paidTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($paidTime) ? date('d/m/Y', strtotime($paidTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Chờ xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $waitingConfirmationTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 17) {
                                                    $waitingConfirmationTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($waitingConfirmationTime) ? date('d/m/Y', strtotime($waitingConfirmationTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Chờ xác nhận -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $waitingConfirmationTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 17) {
                                                    $waitingConfirmationTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($waitingConfirmationTime) ? date('d/m/Y', strtotime($waitingConfirmationTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $exchangeTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 5 && ($order['status_name'] ?? '') == 'returned') {
                                                    $exchangeTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($exchangeTime) ? date('d/m/Y', strtotime($exchangeTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $exchangeTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 5 && ($order['status_name'] ?? '') == 'returned') {
                                                    $exchangeTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($exchangeTime) ? date('d/m/Y', strtotime($exchangeTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Thời điểm cập nhật trạng thái Đã đặt hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $orderedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 20) {
                                                    $orderedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($orderedTime) ? date('d/m/Y', strtotime($orderedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Ngày cập nhật trạng thái Đã đặt hàng -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $orderedTime = null;
                                            foreach (($order['status_history'] ?? []) as $history) {
                                                if (($history['status'] ?? null) == 20) {
                                                    $orderedTime = $history['updated_at'];
                                                    break;
                                                }
                                            }
                                            echo isset($orderedTime) ? date('d/m/Y', strtotime($orderedTime)) : '';
                                            ?>
                                        </td>

                                        <!-- Tình trạng kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">Đủ hàng</td>

                                        <!-- Ghi chú sản phẩm -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $productNotes = [];
                                            foreach (($order['items'] ?? []) as $item) {
                                                if (isset($item['note']) && !empty(trim($item['note']))) $productNotes[] = $item['note'];
                                            }
                                            if (!empty($productNotes)) echo implode('<br>', $productNotes);
                                            ?>
                                        </td>

                                        <!-- Mã rút gọn GHTK -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $shortCode = null;
                                            if (!empty($order['partner']['extend_code'])) {
                                                $parts = explode('.', $order['partner']['extend_code']);
                                                $shortCode = end($parts);
                                            }
                                            echo $shortCode;
                                            ?>
                                        </td>

                                        <!-- Đơn vị tiền tệ -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">VND</td>

                                        <!-- Địa chỉ kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['warehouse_info']['full_address'] ?? '') ?></td>

                                        <!-- SĐT kho -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['warehouse_info']['phone_number'] ?? '') ?></td>

                                        <!-- Tiền từ đơn gốc -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Tiền trả lại khách -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">0</td>

                                        <!-- Số loại sản phẩm -->
                                        <td></td>

                                        <!-- Username -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['customer']['username'] ?? '') ?></td>

                                        <!-- Email -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center">
                                            <?php
                                            $customerEmails = '';
                                            if (!empty($order['data']['customer']['emails']) && is_array($order['data']['customer']['emails'])) $customerEmails = implode(', ', $order['data']['customer']['emails']);
                                            echo $customerEmails;
                                            ?>
                                        </td>

                                        <!-- Mã đối tác -->
                                        <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($extendCode) ?></td>

                                        <!-- Mã đơn hàng đổi -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['id']) ?></td>

                                        <!-- Mã đơn hàng gốc -->
                                        <td class="tw-px-4 tw-py-3 tw-font-semibold tw-border tw-border-gray-300 align-middle text-center"><?= html_escape($order['change_by_orders'][0]['order']['id'] ?? '') ?></td>
                                    </tr>

                                    <!-- ===== MODAL OVERLAY: chi tiết sản phẩm (Cố định giữa màn hình) ===== -->
                                    <tr x-show="openProducts" style="display:none;">
                                        <td colspan="200" style="padding:0;">
                                            <div class="modal-mask" @click.self="openProducts=false" @keydown.escape.window="openProducts=false">
                                                <div class="modal-card tw-rounded-lg tw-shadow-md">
                                                    <div style="background-color: #003cffff; color: white;" class="modal-header tw-flex tw-items-center tw-justify-between">
                                                        <h3 class="tw-text-lg tw-font-semibold">Chi tiết sản phẩm — Đơn #<?= html_escape($order['id'] ?? '') ?></h3>
                                                    </div>

                                                    <div class="modal-body">
                                                        <!-- Info nhanh -->
                                                        <div style="margin-left: 142px;" class="tw-grid md:tw-grid-cols-3 tw-justify-center tw-gap-6 tw-mb-6">
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Ngày tạo</div>
                                                                <div class="tw-font-semibold">
                                                                    <?php
                                                                    if (isset($order['inserted_at'])) {
                                                                        $d = new DateTime($order['inserted_at'], new DateTimeZone('UTC'));
                                                                        $d->setTimezone(new DateTimeZone('Asia/Ho_Chi_Minh'));
                                                                        echo $d->format('d/m/Y H:i');
                                                                    }
                                                                    ?><br>
                                                                    <span><?= html_escape($order['creator']['name'] ?? 'Hệ thống') ?></span>
                                                                </div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Maketer</div>
                                                                <div class="tw-font-semibold"><?= html_escape($order['marketer']['name'] ?? '') ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Nhân viên chăm sóc</div>
                                                                <div class="tw-font-semibold"><?= html_escape($order['assigning_care']['name'] ?? '') ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Phân công cho</div>
                                                                <div class="tw-font-semibold"><?= html_escape($order['assigning_seller']['name'] ?? '') ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Khách hàng</div>
                                                                <div class="tw-font-semibold"><?= html_escape($order['shipping_address']['full_name'] ?? '') ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">SĐT</div>
                                                                <div class="tw-font-semibold"><?= html_escape($order['shipping_address']['phone_number'] ?? '') ?></div>
                                                            </div>

                                                        </div>

                                                        <?php
                                                        // ===== DỮ LIỆU HÀNG HÓA (dùng từ payload order nếu có) =====
                                                        $root  = isset($order['data']) ? $order['data'] : $order;
                                                        $items = $root['items'] ?? $products_to_display ?? [];

                                                        // Hàm nhận diện quà tặng
                                                        $isGiftFn = function ($it) {
                                                            $vi    = $it['variation_info'] ?? [];
                                                            $price = (float)($vi['retail_price'] ?? 0);
                                                            return (!empty($it['is_bonus_product']))              // flag hệ thống
                                                                || ($price <= 0)                                // giá 0
                                                                || (($it['total_discount'] ?? 0) >= $price);    // giảm >= giá
                                                        };
                                                        ?>

                                                        <!-- BẢNG SẢN PHẨM (gộp: combo + sp lẻ + quà tặng trong 1 bảng) -->
                                                        <div class="tw-overflow-x-auto">
                                                            <table class="tw-w-full tw-min-w-max tw-text-sm tw-text-left tw-text-gray-700 tw-border tw-border-gray-300">
                                                                <thead class="tw-text-xs tw-uppercase tw-bg-gray-50">
                                                                    <tr>
                                                                        <th class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">STT</th>
                                                                        <th class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 text-center">Tên SP</th>
                                                                        <th class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">Mã SP</th>
                                                                        <th class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">SL</th>
                                                                        <th class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-right">Đơn giá</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $stt = 1; ?>

                                                                    <?php foreach ($items as $it): ?>
                                                                        <?php
                                                                        $vi      = $it['variation_info'] ?? [];
                                                                        $name    = $vi['name'] ?? '';
                                                                        $qty     = (int)($it['quantity'] ?? 0);
                                                                        $code    = $it['variation_info']['display_id'];
                                                                        $price   = (float)($vi['retail_price'] ?? 0);
                                                                        $isCombo = !empty($it['is_composite']);
                                                                        $isGift  = $isGiftFn($it);
                                                                        $discountItems = 0;
                                                                        foreach ($order['items'] ?? [] as $it) {
                                                                            $discountItems += (float)($it['total_discount'] ?? 0);
                                                                        }
                                                                        $total_discount_all = $total_discount + $discountItems;
                                                                        $displayPrice = $isGift ? 0 : $price;
                                                                        ?>
                                                                        <tr>
                                                                            <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center"><?= $stt++ ?></td>
                                                                            <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">
                                                                                <?= html_escape($name ?: ($isCombo ? 'COMBO' : '')) ?>
                                                                                <?php if ($isCombo): ?>
                                                                                    <span class="tw-ml-2 tw-text-xs tw-font-medium tw-bg-green-100 tw-rounded-full tw-px-2 tw-py-0.5">Combo</span>
                                                                                <?php endif; ?>
                                                                                <?php if ($isGift): ?>
                                                                                    <span></span>
                                                                                <?php endif; ?>
                                                                            </td>
                                                                            <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center"><?= html_escape($code) ?></td>
                                                                            <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center"><?= $qty ?></td>
                                                                            <td class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-right"><?= number_format($displayPrice) ?></td>
                                                                        </tr>
                                                                    <?php endforeach; ?>

                                                                    <?php if (empty($items)): ?>
                                                                        <tr>
                                                                            <td colspan="5" class="tw-px-4 tw-py-3 tw-border tw-border-gray-300 tw-text-center">Không có sản phẩm.</td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <!-- Tổng quan tiền/VC -->
                                                        <div style="margin-left: 142px;" class="tw-grid md:tw-grid-cols-3 tw-gap-6 tw-mt-6">
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Tổng số tiền</div>
                                                                <div class="tw-font-semibold"><?= number_format($totalPrice) ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">Giảm giá</div>
                                                                <div class="tw-font-semibold"><?= number_format($total_discount_all) ?></div>
                                                            </div>
                                                            <div>
                                                                <div class="tw-text-xs tw-text-gray-500">COD</div>
                                                                <div class="tw-font-semibold"><?= number_format($cod) ?></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button class="btn btn-outline" @click="openProducts=false">Đóng</button>
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
            </div>
            <div class="tw-p-4 tw-border-t tw-border-gray-200 pagination">
                <?= $pagination ?>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Giữ nguyên các tiện ích ban đầu ---
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput) searchInput.focus();

        const startDateInput = document.querySelector('input[name="startDateTime"]');
        const endDateInput = document.querySelector('input[name="endDateTime"]');
        if (startDateInput && !startDateInput.value) {
            const sevenDaysAgo = new Date();
            sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
            startDateInput.value = sevenDaysAgo.toISOString().slice(0, 16);
        }
        if (endDateInput && !endDateInput.value) {
            endDateInput.value = new Date().toISOString().slice(0, 16);
        }

        function stylePagination() {
            const paginationContainer = document.querySelector('.pagination');
            if (!paginationContainer) return;
            const paginationLinks = paginationContainer.querySelectorAll('.page-link');
            if (paginationLinks.length === 0) return;
            const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>`;
            const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>`;
            paginationLinks.forEach(link => {
                const content = link.innerHTML.trim();
                if (content.includes('&lt;') || content.includes('«')) {
                    link.innerHTML = prevIcon;
                    link.setAttribute('aria-label', 'Previous');
                } else if (content.includes('&gt;') || content.includes('»')) {
                    link.innerHTML = nextIcon;
                    link.setAttribute('aria-label', 'Next');
                }
            });
        }
        stylePagination();

        // --- Helper: gom filter từ form (nếu có) ---
        function collectFilters() {
            const out = {};
            ['search', 'filter_status', 'include_removed', 'updateStatus', 'startDateTime', 'endDateTime'].forEach(name => {
                const el = document.querySelector(`[name="${name}"]`);
                if (!el) return;
                if (el.type === 'checkbox') {
                    if (el.checked) out[name] = el.value || 1;
                } else if (el.value !== '') {
                    out[name] = el.value;
                }
            });
            return out;
        }

        // --- Biến UI ---
        const syncButton = $('#sync-button');
        const originalButtonText = syncButton.html();
        const progressContainer = $('#sync-progress-container');
        const progressBar = $('#sync-progress-bar');
        const statusText = $('#sync-status-text');

        // --- Click đồng bộ: chạy ALL pages, mỗi lần 1.000 đơn ---
        syncButton.on('click', function(e) {
            e.preventDefault();

            syncButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Bắt đầu...');
            progressContainer.show();
            progressBar.removeClass('progress-bar-success').css('width', '0%').text('0%');
            statusText.text('Đang kết nối đến Pancake API...');

            const filters = collectFilters();

            // 1) Hỏi tổng số trang
            $.ajax({
                url: '<?= admin_url('pancake_sync/start_sync') ?>',
                method: 'POST',
                dataType: 'json',
                data: filters, // gửi cùng filter để server tính đúng total_pages
                timeout: 120000,
                success: function(response) {
                    if (response.success) {
                        const totalPages = parseInt(response.total_pages || 1, 10);
                        const pageSize = parseInt(response.page_size || 1000, 10);
                        statusText.text(`Bắt đầu đồng bộ ${totalPages} trang (mỗi lần tối đa ${pageSize} đơn)...`);
                        // 2) Chạy tuần tự 1 -> totalPages
                        runSyncPages(1, totalPages, pageSize, 0, filters);
                    } else {
                        alert_float('danger', response.message || 'Không khởi tạo được đồng bộ.');
                        resetSyncUI();
                    }
                },
                error: function() {
                    alert_float('danger', 'Lỗi khi bắt đầu quá trình đồng bộ. Không thể kết nối đến máy chủ.');
                    resetSyncUI();
                }
            });
        });

        function runSyncPages(page, totalPages, pageSize, totalProcessed, filters) {
            $.ajax({
                url: '<?= admin_url('pancake_sync/sync_page') ?>',
                method: 'POST',
                dataType: 'json',
                data: Object.assign({
                    page: page
                }, filters),
                timeout: 180000, // 3 phút / trang
                success: function(res) {
                    if (res.status === 'complete') {
                        const processed = totalProcessed + (parseInt(res.processed_count || 0, 10));
                        const percent = Math.min(100, Math.round((page / totalPages) * 100));
                        progressBar.css('width', percent + '%').text(percent + '%');
                        statusText.text(`Trang ${page}/${totalPages} xong — đã xử lý ${processed} đơn.`);

                        if (page < totalPages) {
                            // Gọi trang kế tiếp
                            runSyncPages(page + 1, totalPages, pageSize, processed, filters);
                        } else {
                            // Hoàn tất tất cả
                            progressBar.addClass('progress-bar-success').css('width', '100%').text('100%');
                            statusText.text(`Hoàn tất! Đã xử lý toàn bộ ${totalPages} trang.`);
                            alert_float('success', 'Đồng bộ toàn bộ đơn hàng đã hoàn tất.');
                            setTimeout(function() {
                                window.location.reload();
                            }, 1200);
                        }
                    } else if (res.status === 'error') {
                        alert_float('danger', res.message || `Có lỗi ở trang ${page}. Dừng lại.`);
                        resetSyncUI();
                    } else {
                        alert_float('danger', `Phản hồi không hợp lệ ở trang ${page}.`);
                        resetSyncUI();
                    }
                },
                error: function() {
                    alert_float('danger', `Mất kết nối khi đồng bộ trang ${page}.`);
                    resetSyncUI();
                }
            });
        }

        function resetSyncUI() {
            syncButton.prop('disabled', false).html(originalButtonText);
            progressContainer.hide();
        }

        /* ============================================================
           BỔ SUNG: Đồng bộ 1.000 đơn gần nhất (không thay đổi nút cũ)
           - Cần có nút #recent-sync-button trong view (nếu không có, đoạn dưới tự bỏ qua)
           - Tái dùng progress UI hiện có
           ============================================================ */
        const recentBtn = $('#recent-sync-button');
        if (recentBtn.length) {
            const recentBtnText = recentBtn.html();

            recentBtn.on('click', function(e) {
                e.preventDefault();

                // Khóa nút 1.000 đơn, giữ nguyên nút sync cũ
                recentBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang đồng bộ 1.000 đơn...');
                progressContainer.show();
                progressBar.removeClass('progress-bar-success').css('width', '10%').text('Đang chạy…');
                statusText.text('Đang gọi API để lấy 1.000 đơn gần nhất…');

                const filters = collectFilters(); // nếu muốn kèm filter hiện tại

                $.ajax({
                    url: '<?= admin_url('pancake_sync/sync_recent_1000') ?>',
                    method: 'GET', // endpoint mình đề xuất nhận GET; nếu bạn dùng POST thì đổi tại đây
                    dataType: 'json',
                    data: filters,
                    timeout: 300000,
                    success: function(res) {
                        if (res && res.status === 'complete') {
                            const processed = parseInt(res.processed_count || 0, 10);
                            const ok = parseInt(res.rows_ok || 0, 10);
                            const err = parseInt(res.rows_err || 0, 10);

                            progressBar.addClass('progress-bar-success').css('width', '100%').text('100%');
                            statusText.text(`Hoàn tất: xử lý ${processed} đơn — OK: ${ok}, Lỗi: ${err}.`);
                            if (err > 0 && Array.isArray(res.errors) && res.errors.length) {
                                const first3 = res.errors.slice(0, 3).map(e => {
                                    const id = e.order_id ?? '(không rõ ID)';
                                    const msg = e.error ?? 'Lỗi không xác định';
                                    return `• ${id}: ${msg}`;
                                }).join('<br>');
                                alert_float('warning', 'Một số đơn lỗi:<br>' + first3);
                            } else {
                                alert_float('success', res.message || 'Đã đồng bộ 1.000 đơn gần nhất.');
                            }
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            alert_float('danger', (res && res.message) ? res.message : 'Đồng bộ 1.000 đơn thất bại.');
                            resetRecentBtn();
                        }
                    },
                    error: function() {
                        alert_float('danger', 'Lỗi kết nối khi đồng bộ 1.000 đơn.');
                        resetRecentBtn();
                    }
                });
            });

            function resetRecentBtn() {
                recentBtn.prop('disabled', false).html(recentBtnText);
                // Không ẩn progress để bạn theo dõi; nếu muốn ẩn thì:
                // progressContainer.hide();
            }
        }
    });
</script>

<?php init_tail(); ?>