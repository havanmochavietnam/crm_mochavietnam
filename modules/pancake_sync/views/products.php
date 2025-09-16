<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/styles/overlayscrollbars.min.css" integrity="sha512-sCl3ircQkHTtLgLhCYN4CiWgKkC/IpdvEzaN/f1_Q1N/A+z1k/fESXpFHgo1kE/5KVdRODxt1LzE/exk3K3S1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    .card-modern {
        background-color: #fff;
        border-radius: 8px;
        border: 1px solid #dfe1e6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .card-modern-body {
        padding: 20px 25px;
    }

    .card-modern-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        /* Cho phép xuống dòng trên màn hình nhỏ */
    }

    .card-modern-header .no-margin {
        font-size: 1.5rem;
        font-weight: 600;
        color: #333;
    }

    /* ----- CSS CHO KHU VỰC TÌM KIẾM VÀ NÚT BẤM ----- */
    .header-actions {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .search-wrapper {
        position: relative;
    }

    .search-wrapper .search-icon {
        position: absolute;
        top: 50%;
        left: 12px;
        transform: translateY(-50%);
        color: #9e9e9e;
        pointer-events: none;
        /* Kích vào icon vẫn focus vào input */
    }

    .search-wrapper .form-control {
        padding-left: 35px;
        /* Tạo khoảng trống cho icon */
        width: 280px;
        height: 36px;
        border-radius: 6px;
    }

    /* ------------------------------------------------ */

    .table-container {
        max-height: 75vh;
        overflow: auto;
    }

    .table-container thead th {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: inset 0 -2px 0 #dee2e6;
    }

    .table-pancake {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-pancake th,
    .table-pancake td {
        vertical-align: middle !important;
        padding: 12px 15px !important;
    }

    .table-pancake tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Các CSS khác giữ nguyên */
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
    }

    .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        color: #ccc;
        background-color: #f9f9f9;
        border-radius: 6px;
    }

    .tag-label {
        margin: 0 5px 5px 0;
        display: inline-block;
        font-weight: 500;
        border-radius: 12px;
        padding: 4px 10px;
        font-size: 0.8rem;
    }

    /* CSS cho thanh cuộn nổi */
    .os-theme-dark.os-scrollbar-horizontal {
        height: 14px;
    }

    .os-theme-dark.os-scrollbar-horizontal .os-scrollbar-handle {
        min-height: 10px;
    }

    .os-theme-dark.os-scrollbar-vertical {
        width: 15px;
    }

    .os-theme-dark.os-scrollbar-vertical .os-scrollbar-handle {
        background-color: #888;
    }

    .os-theme-dark.os-scrollbar-vertical .os-scrollbar-handle:hover {
        background-color: #555;
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-modern-body">
                        <div class="card-modern-header">
                            <h4 class="no-margin">
                                <i class="fa fa-cubes" aria-hidden="true"></i> Danh sách sản phẩm Pancake
                            </h4>

                            <div class="header-actions">
                                <div class="search-wrapper">
                                    <span class="search-icon"><i class="fa fa-search"></i></span>
                                    <input type="text" id="productSearchInput" class="form-control" placeholder="Tìm trên trang hiện tại...">
                                </div>
                                <a href="<?php echo admin_url('pancake_sync_products/sync'); ?>" class="btn btn-success"><i class="fa fa-refresh"></i> Đồng bộ về Database</a>
                            </div>  
                        </div>
                        <hr class="hr-panel-heading">

                        <div id="scrollableTable" class="table-container">
                            <table class="table table-pancake table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">STT</th>
                                        <th class="text-center" style="width: 120px;">Mã sản phẩm (SKU)</th>
                                        <th class="text-left" style="min-width: 250px;">Tên sản phẩm</th>
                                        <th style="width: 80px;" class="text-center">Hình ảnh</th>
                                        <th class="text-left" style="min-width: 150px;">Thẻ</th>
                                        <th class="text-left" style="min-width: 150px;">Danh mục</th>
                                        <th class="text-right" style="width: 120px;">Tổng nhập</th>
                                        <th class="text-right" style="width: 150px;">Giá bán</th>
                                        <th class="text-right" style="width: 150px;">Giá sau giảm</th>
                                        <th class="text-right" style="width: 160px;">Tổng tiền còn lại</th>
                                        <th class="text-right" style="width: 120px;">Có thể bán</th>
                                        <th class="text-center" style="width: 100px;">Ẩn/Hiện</th> <!-- Thêm mới -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($products)): ?>
                                        <?php $i = 1;
                                        foreach ($products as $product): ?>
                                            <tr>
                                                <td class="text-center"><?php echo $i++; ?></td>
                                                <td class="text-center">
                                                    <span class="label label-default"><?php echo $product['barcode'] ?: 'N/A'; ?></span>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($product['product']['name']); ?></strong></td>
                                                <td class="text-center">
                                                    <?php if (!empty($product['images'])): ?>
                                                        <img src="<?php echo $product['images'][0]; ?>" class="product-image">
                                                    <?php else: ?>
                                                        <span class="image-placeholder">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($product['product']['tags'])): ?>
                                                        <?php foreach ($product['product']['tags'] as $tag): ?>
                                                            <span class="label label-default tag-label"><?php echo htmlspecialchars($tag['note']); ?></span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($product['product']['categories'])): ?>
                                                        <?php foreach ($product['product']['categories'] as $category): ?>
                                                            <span class="label label-info tag-label"><?php echo htmlspecialchars($category['name']); ?></span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right"><strong><?php echo number_format($product['variations_warehouses'][0]['total_quantity'] ?? 0); ?></strong></td>
                                                <td class="text-right"><strong class="text-info"><?php echo number_format($product['retail_price']); ?></strong></td>
                                                <td class="text-right"><strong class="text-success"><?php echo number_format($product['retail_price_after_discount']); ?></strong></td>
                                                <td class="text-right"><strong class="text-danger"><?php echo number_format($product['total_purchase_price']); ?></strong></td>
                                                <td class="text-right"><strong class="text-primary"><?php echo number_format($product['remain_quantity']); ?></strong></td>

                                                <!-- Cột bật/tắt is_hidden -->
                                                <td class="text-center">
                                                    <button
                                                        class="btn btn-sm toggle-hidden <?php echo isset($product['is_locked']) && $product['is_locked'] ? 'btn-danger' : 'btn-success'; ?>"
                                                        data-id="<?php echo $product['id']; ?>"
                                                        data-status="<?php echo isset($product['is_locked']) && $product['is_locked'] ? 'false' : 'true'; ?>">
                                                        <?php echo isset($product['is_locked']) && $product['is_locked'] ? '<i class="fa fa-eye-slash"></i>' : '<i class="fa fa-eye"></i>'; ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12 text-center">
                                <?php echo $pagination ?? ''; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/browser/overlayscrollbars.browser.es6.min.js" integrity="sha512-En1FAMGGYf3gBw7sSt4sHwIPsW+Q13oP/gnk1bJdaJscqOAy2Mv0s6/53A81s41c/xNnRTR/4+ChbEAM84b6sA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ----- KHỞI TẠO THANH CUỘN NỔI -----
        const {
            OverlayScrollbars
        } = OverlayScrollbarsGlobal;
        const scrollableElement = document.getElementById('scrollableTable');

        if (scrollableElement) {
            OverlayScrollbars(scrollableElement, {
                scrollbars: {
                    theme: 'os-theme-dark',
                    visibility: 'visible',
                    autoHide: 'never',
                    clickScroll: true
                }
            });
        }

        // ----- LOGIC TÌM KIẾM SẢN PHẨM -----
        const searchInput = document.getElementById('productSearchInput');
        const productTableBody = document.querySelector('.table-pancake tbody');

        if (searchInput && productTableBody) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = productTableBody.getElementsByTagName('tr');

                for (let i = 0; i < rows.length; i++) {
                    const row = rows[i];
                    if (row.getElementsByTagName('td').length <= 1) { // Bỏ qua dòng "không tìm thấy sp"
                        continue;
                    }

                    const skuCell = row.getElementsByTagName('td')[0];
                    const nameCell = row.getElementsByTagName('td')[1];

                    if (nameCell && skuCell) {
                        const skuText = skuCell.textContent.toLowerCase();
                        const nameText = nameCell.textContent.toLowerCase();

                        // Nếu tìm thấy trong SKU hoặc Tên sản phẩm thì hiện ra
                        if (skuText.includes(searchTerm) || nameText.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                }
            });
        }
    });
</script>

</body>

</html>