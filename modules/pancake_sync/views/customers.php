<?php
defined('BASEPATH') or exit('No direct script access allowed');

// === HÀM HỖ TRỢ ĐƯỢC ĐẶT TRỰC TIẾP TẠI ĐÂY ===
if (!function_exists('get_pancake_mobile_network')) {
    /**
     * Nhận diện nhà mạng dựa trên đầu số điện thoại Việt Nam.
     * @param string $phoneNumber Số điện thoại cần kiểm tra.
     * @return array Mảng chứa tên nhà mạng và class màu.
     */
    function get_pancake_mobile_network(string $phoneNumber): array
    {
        if (empty($phoneNumber)) {
            return ['name' => '', 'class' => 'default'];
        }

        // Chuẩn hóa SĐT về dạng 9 chữ số (loại bỏ +84, 0)
        if (strpos($phoneNumber, '+84') === 0) {
            $phoneNumber = substr($phoneNumber, 3);
        }
        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) < 9) {
            return ['name' => 'Khác', 'class' => 'default'];
        }

        $prefix = substr($phoneNumber, 0, 2);

        $networks = [
            'Viettel' => ['class' => 'success', 'prefixes' => ['96', '97', '98', '32', '33', '34', '35', '36', '37', '38', '39']],
            'Mobifone' => ['class' => 'primary', 'prefixes' => ['90', '93', '70', '79', '77', '76', '78']],
            'Vinaphone' => ['class' => 'info', 'prefixes' => ['91', '94', '83', '84', '85', '81', '82']],
            'Vietnamobile' => ['class' => 'warning', 'prefixes' => ['92', '56', '58']],
            'Gmobile' => ['class' => 'danger', 'prefixes' => ['99', '59']],
        ];

        foreach ($networks as $name => $details) {
            if (in_array($prefix, $details['prefixes'])) {
                return ['name' => $name, 'class' => $details['class']];
            }
        }

        return ['name' => 'Khác', 'class' => 'default'];
    }
}
?>
<?php init_head(); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/styles/overlayscrollbars.min.css" />

<style>
    .card-modern {
        background-color: #fff;
        border-radius: 8px;
        border: 1px solid #dfe1e6;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .08);
    }

    .card-modern-body {
        padding: 20px 25px;
    }

    .table-container {
        max-height: 75vh;
        overflow: auto;
    }

    .table-pancake th,
    .table-pancake td {
        vertical-align: middle !important;
        padding: 12px 15px !important;
        white-space: nowrap;
    }

    .table-container thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 10;
        box-shadow: inset 0 -2px 0 #dee2e6;
    }

    .label-tag {
        margin: 2px;
        display: inline-block;
    }
    
    /* Header styling */
    .card-modern-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    
    .header-title {
        flex: 1;
        min-width: 250px;
    }
    
    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .search-icon {
        position: absolute;
        left: 10px;
        color: #6c757d;
        z-index: 3;
    }
    
    .search-input {
        padding-left: 35px;
        padding-right: 100px;
        width: 300px;
        border-radius: 4px;
        height: 38px;
    }
    
    .search-button {
        position: absolute;
        right: 5px;
        background: #f4f4f4;
        border: none;
        height: 28px;
        border-radius: 3px;
        padding: 0 10px;
        color: #333;
        z-index: 2;
    }
    
    .sync-button {
        background: #26B99A;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 5px;
        height: 38px;
    }
    
    .sync-button:hover {
        background: #1e9e87;
        color: white;
    }
    
    @media (max-width: 768px) {
        .card-modern-header {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }
        
        .header-actions {
            flex-direction: column;
        }
        
        .search-wrapper {
            width: 100%;
        }
        
        .search-input {
            width: 100%;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-modern-body">
                        <div class="card-modern-header">
                            <div class="header-title">
                                <h4 class="no-margin">
                                    <i class="fa fa-users" aria-hidden="true"></i> <?php echo $title; ?>
                                    <small>(Tổng: <?php echo $total; ?>)</small>
                                </h4>
                            </div>
                            <div class="header-actions">
                                <form action="<?= admin_url('pancake_sync/pancake_sync_customers'); ?>" method="get" class="search-wrapper">
                                    <span class="search-icon"><i class="fa fa-search"></i></span>
                                    <input type="text" name="search_ids" class="form-control search-input"
                                        placeholder="Nhập Customer ID, cách nhau bởi dấu phẩy..."
                                        value="<?= html_escape($search_ids); ?>">
                                    <button type="submit" class="search-button">Tìm kiếm</button>
                                </form>
                                <a href="<?= admin_url('pancake_sync/pancake_sync_customers/sync'); ?>" class="sync-button">
                                    <i class="fa fa-refresh" aria-hidden="true"></i> Đồng bộ DB
                                </a>
                            </div>
                        </div>
                        
                        <hr class="hr-panel-heading">
                        
                        <?php if (!empty($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php echo html_escape($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <div id="scrollableTable" class="table-container">
                            <table class="table table-pancake table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>ID Khách</th>
                                        <th>Ngày tạo</th>
                                        <th>Tháng tạo</th>
                                        <th>Tên khách hàng</th>
                                        <th>Ngày sinh</th>
                                        <th>Giới tính</th>
                                        <th>Nhà mạng (SĐT)</th>
                                        <th>Địa chỉ</th>
                                        <th>Cấp độ khách hàng</th>
                                        <th>Điểm thưởng</th>
                                        <th class="text-right">Tổng chi</th>
                                        <th class="text-center">Tổng đơn</th>
                                        <th class="text-center">Đơn thành công</th>
                                        <th>Lần mua cuối</th>
                                        <th>Thẻ</th>
                                        <th>Emails</th>
                                        <th>Thời gian phát sinh đơn hàng đầu tiên</th>
                                        <th>FB ID</th>
                                        <th>Ghi chú cuối</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($customers)) : ?>
                                        <?php $page = $this->input->get('page_number') ?: 1;
                                        $per_page = 30; // Giả sử page_size là 30
                                        $i = (($page - 1) * $per_page) + 1; ?>
                                        <?php foreach ($customers as $customer) : ?>
                                            <tr>
                                                <td class="text-center"><?php echo $i++; ?></td>
                                                <td class="text-center">
                                                    <span class="label label-default"><?php echo html_escape($customer['customer_id'] ?? 'N/A'); ?></span>
                                                </td>
                                                <td class="text-center"><?= isset($customer['inserted_at']) ? date('d/m/Y', strtotime($customer['inserted_at'])) : '' ?></td>
                                                <td class="text-center"><?= isset($customer['inserted_at']) ? date('m/Y', strtotime($customer['inserted_at'])) : '' ?></td>
                                                <td><b><?php echo html_escape($customer['name'] ?? ''); ?></b></td>
                                                <td class="text-center"><?= isset($customer['date_of_birth']) ? date('d/m/Y', strtotime($customer['date_of_birth'])) : '' ?></td>
                                                <td class="text-center">
                                                    <?php $gender = $customer['gender'] ?? '';
                                                    echo ($gender === 'male') ? 'Nam' : (($gender === 'female') ? 'Nữ' : ''); ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($customer['phone_numbers']) && is_array($customer['phone_numbers'])) : ?>
                                                        <?php foreach ($customer['phone_numbers'] as $phone) : ?>
                                                            <?php
                                                            $network_info = get_pancake_mobile_network($phone);
                                                            $network_name = $network_info['name'];
                                                            $label_class = $network_info['class'];
                                                            ?>
                                                            <?php if (!empty($network_name) && $network_name !== 'Khác') : ?>
                                                                <span class="label label-<?php echo $label_class; ?>">
                                                                    <?php echo $network_name . ' (' . html_escape($phone) . ')'; ?>
                                                                </span><br>
                                                            <?php else: ?>
                                                                <span><?php echo html_escape($phone); ?></span><br>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </td>

                                                <td><?php echo html_escape($customer['shop_customer_addresses'][0]['full_address'] ?? ''); ?></td>
                                                <td class="text-center"><?php echo html_escape($customer['level']['name'] ?? ''); ?></td>
                                                <td class="text-right text-success"><?php echo number_format($customer['reward_point'] ?? 0); ?></td>
                                                <td class="text-right text-danger"><b><?php echo number_format($customer['purchased_amount'] ?? 0); ?></b></td>
                                                <td class="text-center"><?php echo html_escape($customer['order_count'] ?? 0); ?></td>
                                                <td class="text-center"><?php echo html_escape($customer['succeed_order_count'] ?? 0); ?></td>
                                                <td class="text-center"><?= isset($customer['last_order_at']) ? date('d/m/Y', strtotime($customer['last_order_at'])) : '' ?></td>
                                                <td>
                                                    <?php if (!empty($customer['tags']) && is_array($customer['tags'])) : ?>
                                                        <?php foreach ($customer['tags'] as $tag) : ?>
                                                            <span class="label label-tag label-info"><?php echo html_escape($tag); ?></span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-left">
                                                    <?php if (!empty($customer['emails']) && is_array($customer['emails'])) {
                                                        echo implode(', ', array_map('html_escape', $customer['emails']));
                                                    } ?>
                                                </td>
                                                <td class="text-center"><?= isset($customer['inserted_at']) ? date('d/m/Y', strtotime($customer['inserted_at'])) : '' ?></td>
                                                <td class="text-left"><b><?php echo html_escape($customer['fb_id'] ?? ''); ?></b></td>
                                                <td><?php echo html_escape($customer['notes'][0]['message'] ?? ''); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="13" class="text-center">Không tìm thấy khách hàng nào.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($pagination)): ?>
                            <div class="text-center" style="margin-top: 15px;"><?= $pagination ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/browser/overlayscrollbars.browser.es6.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const {
            OverlayScrollbars
        } = OverlayScrollbarsGlobal;
        if (document.getElementById('scrollableTable')) {
            OverlayScrollbars(document.getElementById('scrollableTable'), {
                scrollbars: {
                    theme: 'os-theme-dark'
                }
            });
        }
    });
</script>
</body>
</html>