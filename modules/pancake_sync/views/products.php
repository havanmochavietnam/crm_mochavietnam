<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<link rel="stylesheet" href="<?= module_dir_url('pancake_sync', 'assets/css/pancake_product.css'); ?>?v=<?= time(); ?>">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/styles/overlayscrollbars.min.css" integrity="sha512-sCl3ircQkHTtLgLhCYN4CiWgKkC/IpdvEzaN/f1_Q1N/A+z1k/fESXpFHgo1kE/5KVdRODxt1LzE/exk3K3S1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
                                <form action="<?= admin_url('pancake_sync/pancake_sync_products'); ?>" method="get" class="search-wrapper">
                                    <span class="search-icon"><i class="fa fa-search"></i></span>
                                    <input type="text" name="search_ids" class="form-control search-input"
                                        placeholder="Mã / Tên sản phẩm / Barcode / Keyword"
                                        value="<?= html_escape($search_ids); ?>">
                                    <button type="submit" class="search-button">Tìm kiếm</button>
                                </form>
                                <a href="<?= admin_url('pancake_sync/pancake_sync_products/sync'); ?>" class="sync-button">
                                    <i class="fa fa-refresh" aria-hidden="true"></i> Đồng bộ Sản Phẩm
                                </a>
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
                                        <?php
                                        $i = 1;
                                        // 1. Khởi tạo mảng để theo dõi các display_id đã xử lý
                                        $processed_display_ids = [];
                                        ?>
                                        <?php foreach ($products as $product): ?>
                                            <?php
                                            // 2. Lấy display_id của sản phẩm hiện tại
                                            $display_id = $product['product']['display_id'] ?? null;

                                            // 3. KIỂM TRA TRÙNG LẶP: Nếu ID này đã tồn tại trong mảng, bỏ qua lần lặp này
                                            if (isset($processed_display_ids[$display_id])) {
                                                continue;
                                            }

                                            // 4. ĐÁNH DẤU: Nếu ID chưa có, đánh dấu là đã xử lý để lần sau bỏ qua
                                            $processed_display_ids[$display_id] = true;
                                            ?>
                                            <tr>
                                                <td class="text-center"><?php echo $i++; ?></td>
                                                <td class="text-center">
                                                    <span class="label label-default"><?php echo $display_id ?: ''; ?></span>
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
                            <div class="tw-p-4 tw-border-t tw-border-gray-200 pagination">
                                <?= $pagination ?>
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