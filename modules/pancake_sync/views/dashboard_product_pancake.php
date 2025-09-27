<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
  .product-table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
    text-align: left;
  }

  .product-table tbody td {
    vertical-align: middle;
    padding: 12px 8px;
  }

  .product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .product-thumb {
    width: 44px;
    height: 44px;
    border-radius: 6px;
    object-fit: cover;
    background: #f3f4f6;
    flex: 0 0 44px;
  }

  .product-info .code {
    font-weight: 700;
    font-size: 13px;
    line-height: 1.1;
  }

  .product-info .name {
    color: #555;
    font-size: 13px;
    line-height: 1.2;
  }

  .product-value {
    text-align: right;
  }

  .product-trend.up {
    color: #28a745;
    font-size: 12px;
  }

  .product-trend.down {
    color: #dc3545;
    font-size: 12px;
  }

  .filter-bar {
    text-align: end;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 16px;
  }

  .filter-bar .form-group {
    margin-right: 10px;
    margin-bottom: 0;
  }

  .filter-bar .btn {
    vertical-align: top;
  }

  .kpi-wrap {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .kpi-card {
    flex: 1 1 260px;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 14px 16px;
  }

  .kpi-title {
    color: #6c757d;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: .3px;
    text-transform: uppercase;
    margin-bottom: 6px;
  }

  .kpi-value {
    font-size: 20px;
    font-weight: 700;
  }

  .kpi-sub {
    color: #6c757d;
    font-size: 13px;
  }

  .kpi-combo {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .kpi-combo img {
    width: 40px;
    height: 40px;
    border-radius: 6px;
    object-fit: cover;
    background: #f3f4f6;
  }

  .grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-bottom: 16px;
  }

  @media (max-width: 991.98px) {
    .grid-2 {
      grid-template-columns: 1fr;
    }
  }

  .dash-panel {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 0;
    overflow: hidden;
  }

  .dash-panel .panel-head {
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
    font-weight: 600;
  }

  .dash-panel .panel-body {
    padding: 0;
  }
</style>

<div id="wrapper">
  <div class="content">
    <div class="info-section">
      <!-- Filter bên phải -->
      <div class="d-flex justify-content-end">
        <form method="GET" action="<?= html_escape(current_url()); ?>" class="filter-bar form-inline w-auto ms-auto">
          <div class="form-group me-2">
            <label for="date_from" class="control-label me-1">Từ ngày</label>
            <input type="date" id="date_from" name="date_from" class="form-control" value="<?= html_escape($date_from ?? date('Y-m-01')); ?>">
          </div>
          <div class="form-group me-2">
            <label for="date_to" class="control-label me-1">Đến ngày</label>
            <input type="date" id="date_to" name="date_to" class="form-control" value="<?= html_escape($date_to ?? date('Y-m-d')); ?>">
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Tìm</button>
        </form>
      </div>

      <!-- KPIs -->
      <div class="kpi-wrap">
        <div class="kpi-card">
          <div class="kpi-title">Doanh thu tổng</div>
          <div class="kpi-value"><?= number_format((float)($total_revenue ?? 0), 0, ',', '.'); ?></div>
          <div class="kpi-sub">Trong khoảng:<?= htmlspecialchars($date_from ?? '-', ENT_QUOTES, 'UTF-8'); ?>→<?= htmlspecialchars($date_to ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
        </div>

        <div class="kpi-card">
          <div class="kpi-title">Combo được mua nhiều nhất</div>
          <div class="kpi-combo">
            <?php
            $tcimg = $top_combo['image_url'] ?? '';
            $tcimg = $tcimg ?: 'https://via.placeholder.com/40x40?text=%20';
            ?>
            <img src="<?= htmlspecialchars($tcimg); ?>" alt="">
            <div>
              <div class="kpi-value" style="font-size:16px;"><?= htmlspecialchars($top_combo['name'] ?? '—'); ?></div>
              <div class="kpi-sub">Doanh thu: <strong><?= number_format((float)($top_combo['revenue'] ?? 0), 0, ',', '.'); ?> đ</strong></div>
            </div>
          </div>
        </div>

        <div class="kpi-card">
          <div class="kpi-title">Tỷ lệ đóng góp (Top combo)</div>
          <div class="kpi-value">
            <?php $pct = isset($top_combo['contribution_pct']) ? (float)$top_combo['contribution_pct'] : null;
            echo is_null($pct) ? '—' : (number_format($pct, 2, ',', '.') . '%'); ?>
          </div>
          <div class="kpi-sub">So với tổng doanh thu đã lọc</div>
        </div>
      </div>

      <!-- Hàng 1: Doanh thu theo SP & theo Combo -->
      <div class="grid-2">
        <div class="dash-panel">
          <div class="panel-head"><i class="fa fa-cubes"></i> Doanh thu theo từng sản phẩm</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-hover product-table">
                <thead>
                  <tr>
                    <th style="width:70%;">Thông tin sản phẩm</th>
                    <th style="width:30%; text-align:right;">Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($products_metrics)) : foreach ($products_metrics as $p) :
                      $img = $p['image_url'] ?: 'https://via.placeholder.com/44x44?text=%20';
                      $code = $p['product_code'] ?: '-';
                      $name = $p['product_name'] ?: '-';
                      $rev  = (float)($p['revenue'] ?? 0);
                      $pct  = $p['pct'] ?? null;
                      $isUp = !is_null($pct) && ($pct >= 0);
                  ?>
                      <tr>
                        <td>
                          <div class="product-cell">
                            <img class="product-thumb" src="<?= htmlspecialchars($img); ?>" alt="">
                            <div class="product-info">
                              <div class="code"><?= htmlspecialchars($code); ?></div>
                              <div class="name"><?= htmlspecialchars($name); ?></div>
                            </div>
                          </div>
                        </td>
                        <td class="product-value">
                          <div class="value"><strong><?= number_format($rev, 0, ',', '.'); ?> đ</strong></div>
                          <?php if (!is_null($pct)) : ?>
                            <div class="product-trend <?= $isUp ? 'up' : 'down'; ?>">
                              <?= $isUp ? '▲' : '▼'; ?> <?= number_format(abs($pct), 2, ',', '.'); ?>%
                            </div>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach;
                  else: ?>
                    <tr>
                      <td colspan="2" class="text-center">Không có dữ liệu sản phẩm.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="dash-panel">
          <div class="panel-head"><i class="fa fa-layer-group"></i> Doanh thu theo từng combo</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-hover product-table">
                <thead>
                  <tr>
                    <th style="width:70%;">Thông tin combo</th>
                    <th style="width:30%; text-align:right;">Doanh thu</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($combos_revenue)) : foreach ($combos_revenue as $c) :
                      $img = $c['image_url'] ?: 'https://via.placeholder.com/44x44?text=%20';
                      $code = $c['combo_code'] ?: '-';
                      $name = $c['combo_name'] ?: '-';
                      $rev  = (float)($c['revenue'] ?? 0);
                  ?>
                      <tr>
                        <td>
                          <div class="product-cell">
                            <img class="product-thumb" src="<?= htmlspecialchars($img); ?>" alt="">
                            <div class="product-info">
                              <div class="code"><?= htmlspecialchars($code); ?></div>
                              <div class="name"><?= htmlspecialchars($name); ?></div>
                            </div>
                          </div>
                        </td>
                        <td class="product-value">
                          <div class="value"><strong><?= number_format($rev, 0, ',', '.'); ?> đ</strong></div>
                        </td>
                      </tr>
                    <?php endforeach;
                  else: ?>
                    <tr>
                      <td colspan="2" class="text-center">Không có dữ liệu combo.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Hàng 2: AOV theo combo & Tỷ lệ mua lại theo combo -->
      <div class="grid-2">
        <div class="dash-panel">
          <div class="panel-head"><i class="fa fa-hand-holding-usd"></i> AOV theo từng combo</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-hover product-table">
                <thead>
                  <tr>
                    <th style="width:70%;">Combo</th>
                    <th style="width:30%; text-align:right;">AOV</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($aov_by_combo)) : foreach ($aov_by_combo as $c) :
                      $img = $c['image_url'] ?: 'https://via.placeholder.com/44x44?text=%20';
                      $code = $c['combo_code'] ?: '-';
                      $name = $c['combo_name'] ?: '-';
                      $aov  = (float)($c['aov'] ?? 0);
                  ?>
                      <tr>
                        <td>
                          <div class="product-cell">
                            <img class="product-thumb" src="<?= htmlspecialchars($img); ?>" alt="">
                            <div class="product-info">
                              <div class="code"><?= htmlspecialchars($code); ?></div>
                              <div class="name"><?= htmlspecialchars($name); ?></div>
                            </div>
                          </div>
                        </td>
                        <td class="product-value">
                          <div class="value"><strong><?= number_format($aov, 0, ',', '.'); ?> đ</strong></div>
                        </td>
                      </tr>
                    <?php endforeach;
                  else: ?>
                    <tr>
                      <td colspan="2" class="text-center">Không có dữ liệu AOV theo combo.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="dash-panel">
          <div class="panel-head"><i class="fa fa-sync"></i> Tỷ lệ mua lại theo combo</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-hover product-table">
                <thead>
                  <tr>
                    <th style="width:70%;">Combo</th>
                    <th style="width:30%; text-align:right;">Tỷ lệ mua lại</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($repurchase_by_combo)) : foreach ($repurchase_by_combo as $c) :
                      $img = $c['image_url'] ?: 'https://via.placeholder.com/44x44?text=%20';
                      $code = $c['combo_code'] ?: '-';
                      $name = $c['combo_name'] ?: '-';
                      $rate = isset($c['repurchase_rate']) ? (float)$c['repurchase_rate'] : null;
                  ?>
                      <tr>
                        <td>
                          <div class="product-cell">
                            <img class="product-thumb" src="<?= htmlspecialchars($img); ?>" alt="">
                            <div class="product-info">
                              <div class="code"><?= htmlspecialchars($code); ?></div>
                              <div class="name"><?= htmlspecialchars($name); ?></div>
                            </div>
                          </div>
                        </td>
                        <td class="product-value">
                          <div class="value"><strong><?= is_null($rate) ? '—' : (number_format($rate, 2, ',', '.') . '%'); ?></strong></div>
                        </td>
                      </tr>
                    <?php endforeach;
                  else: ?>
                    <tr>
                      <td colspan="2" class="text-center">Không có dữ liệu tỷ lệ mua lại.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /.info-section -->
  </div><!-- /.content -->
</div><!-- /#wrapper -->

<?php init_tail(); ?>
</body>

</html>