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
    padding: 10px 8px;
    font-size: 13px;
  }
  .product-cell { display:flex; align-items:center; gap:12px; }
  .product-thumb {
    width:44px; height:44px; border-radius:6px; object-fit:cover; background:#f3f4f6; flex:0 0 44px;
  }
  .product-info .code { font-weight:700; font-size:13px; line-height:1.1; }
  .product-info .name { color:#555; font-size:13px; line-height:1.2; }
  .product-value { text-align:right; white-space:nowrap; }

  .filter-bar { text-align:end; background:#fff; border:1px solid #e9ecef; border-radius:8px; padding:12px; margin-bottom:16px; }
  .filter-bar .form-group { margin-right:10px; margin-bottom:0; }

  .kpi-wrap { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
  .kpi-card { flex:1 1 260px; background:#fff; border:1px solid #e9ecef; border-radius:8px; padding:14px 16px; }
  .kpi-title { color:#6c757d; font-weight:600; font-size:12px; letter-spacing:.3px; text-transform:uppercase; margin-bottom:6px; }
  .kpi-value { font-size:20px; font-weight:700; }
  .kpi-sub { color:#6c757d; font-size:13px; }
  .kpi-combo { display:flex; align-items:center; gap:10px; }
  .kpi-combo img { width:40px; height:40px; border-radius:6px; object-fit:cover; background:#f3f4f6; }

  /* Panel */
  .dash-panel { background:#fff; border:1px solid #e9ecef; border-radius:8px; overflow:hidden; margin-bottom:16px; }
  .dash-panel .panel-head { padding:12px 16px; border-bottom:1px solid #e9ecef; background:#f8f9fa; font-weight:600; }
  .dash-panel .panel-body { padding:0; }

  /* Bảng cao ~10 dòng, có cuộn; header & tổng dính */
  .table-wrap { max-height: 560px; overflow-y: auto; position: relative; }
  .product-table thead th.sticky { position: sticky; top: 0; z-index: 2; background: #f8f9fa; }
  .product-table tfoot th,
  .product-table tfoot td {
    position: sticky; bottom: 0; z-index: 3;
    background: #f8f9fa; border-top: 1px solid #e9ecef; box-shadow: 0 -4px 6px rgba(0,0,0,.04);
  }

  .text-right { text-align:right !important; }
</style>

<div id="wrapper">
  <div class="content">
    <div class="info-section">

      <!-- Filter -->
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
          <div class="kpi-value">
            <?= number_format((float)($total_revenue ?? 0), 0, ',', '.'); ?>
          </div>
          <div class="kpi-sub">
            <?php if (($date_from ?? null) == ($date_to ?? null)): ?>
              Hôm nay: <?= htmlspecialchars($date_from ?? '', ENT_QUOTES, 'UTF-8'); ?>
            <?php else: ?>
              Trong khoảng:
              <?= htmlspecialchars($date_from ?? '-', ENT_QUOTES, 'UTF-8'); ?> →
              <?= htmlspecialchars($date_to ?? '-', ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
          </div>
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
              <div class="kpi-value" style="font-size:16px;"><?= htmlspecialchars($top_combo['product_name'] ?? '—'); ?></div>
              <div class="kpi-sub">Doanh thu: <strong><?= number_format((float)($top_combo['revenue'] ?? 0), 0, ',', '.'); ?> </strong></div>
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

      <?php
        /* ===== Map AOV & Repurchase theo mã/tên Combo (nếu controller truyền) ===== */
        $aov_map_code = $aov_map_name = [];
        if (!empty($aov_by_combo)) {
          foreach ($aov_by_combo as $r) {
            $code = isset($r['combo_code']) ? (string)$r['combo_code'] : (isset($r['product_id']) ? (string)$r['product_id'] : '');
            $name = $r['combo_name'] ?? ($r['product_name'] ?? '');
            $aov  = isset($r['aov']) ? (float)$r['aov'] : 0.0;
            if ($code !== '') $aov_map_code[$code] = $aov;
            if ($name !== '') $aov_map_name[$name] = $aov;
          }
        }
        $rep_map_code = $rep_map_name = [];
        if (!empty($repurchase_by_combo)) {
          foreach ($repurchase_by_combo as $r) {
            $code = isset($r['combo_code']) ? (string)$r['combo_code'] : (isset($r['product_id']) ? (string)$r['product_id'] : '');
            $name = $r['combo_name'] ?? ($r['product_name'] ?? '');
            $rate = isset($r['repurchase_rate']) ? (float)$r['repurchase_rate'] : null; // % nếu controller đã nhân 100
            if ($code !== '') $rep_map_code[$code] = $rate;
            if ($name !== '') $rep_map_name[$name] = $rate;
          }
        }

        /* ===== Helper: đếm SỐ ĐƠN DISTINCT cho từng dòng ("có thì tính 1") ===== */
        if (!function_exists('view_extract_order_count')) {
          function view_extract_order_count($row) {
            // Có sẵn số đơn?
            foreach (['order_count','orders','distinct_orders'] as $k) {
              if (isset($row[$k]) && is_numeric($row[$k])) return (int)$row[$k];
            }
            // Có danh sách id đơn? (mảng hoặc CSV)
            foreach (['order_ids','orders_list'] as $k) {
              if (!empty($row[$k])) {
                $ids = is_array($row[$k]) ? $row[$k] : explode(',', (string)$row[$k]);
                $uniq = [];
                foreach ($ids as $id) {
                  $id = trim((string)$id);
                  if ($id === '') continue;
                  $uniq[$id] = true;
                }
                return count($uniq);
              }
            }
            return 0;
          }
        }

        // Hiển thị tối đa 10 item mỗi bảng
        $MAX_ROWS = 10;
      ?>

      <!-- ======================= BẢNG SẢN PHẨM ======================= -->
      <div class="dash-panel">
        <div class="panel-head"><i class="fa fa-cubes"></i> Sản phẩm</div>
        <div class="panel-body">
          <div class="table-wrap">
            <table class="table table-hover table-sm product-table">
              <thead>
                <tr>
                  <th class="sticky" style="width:50%;">Thông tin sản phẩm</th>
                  <!-- Thứ tự metric theo yêu cầu: Doanh thu, Số lượng (đơn), Tỷ lệ đóng góp -->
                  <th class="sticky text-right" style="width:20%;">Doanh thu</th>
                  <th class="sticky text-right" style="width:15%;">Số lượng</th><!-- = số đơn -->
                  <th class="sticky text-right" style="width:15%;">Tỷ lệ đóng góp</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $total_products_revenue = 0.0;
                $total_products_orders  = 0;
                $rows_products = !empty($products_metrics) ? array_slice($products_metrics, 0, $MAX_ROWS) : [];
                if (!empty($rows_products)) :
                  foreach ($rows_products as $p) :
                    $img   = !empty($p['image_url']) ? $p['image_url'] : 'https://via.placeholder.com/44x44?text=%20';
                    $code  = isset($p['product_id']) ? (string)$p['product_id'] : '-';
                    $name  = !empty($p['product_name']) ? $p['product_name'] : '-';
                    $rev   = (float)($p['revenue'] ?? 0);
                    $orders= view_extract_order_count($p); // SỐ ĐƠN DISTINCT

                    $total_products_revenue += $rev;
                    $total_products_orders  += $orders;

                    $pct  = ($total_revenue > 0) ? ($rev * 100 / $total_revenue) : 0;
              ?>
                <tr>
                  <td>
                    <div class="product-cell">
                      <img class="product-thumb" src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                      <div class="product-info">
                        <div class="code"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="product-value"><strong><?= number_format($rev, 0, ',', '.'); ?></strong></td>
                  <td class="product-value"><strong><?= number_format($orders, 0, ',', '.'); ?></strong></td>
                  <td class="product-value"><strong><?= number_format($pct, 2, ',', '.'); ?>%</strong></td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="4" class="text-center">Không có dữ liệu sản phẩm.</td></tr>
              <?php endif; ?>
              </tbody>

              <?php if (!empty($rows_products)) : ?>
              <tfoot>
                <tr>
                  <th class="text-right">TỔNG</th>
                  <th class="product-value"><strong><?= number_format($total_products_revenue, 0, ',', '.'); ?></strong></th>
                  <th class="product-value"><strong><?= number_format($total_products_orders, 0, ',', '.'); ?></strong></th>
                  <th class="product-value">
                    <strong><?= ($total_revenue > 0) ? number_format(($total_products_revenue * 100 / $total_revenue), 2, ',', '.') . '%' : '0%'; ?></strong>
                  </th>
                </tr>
              </tfoot>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>

      <!-- ======================= BẢNG COMBO ======================= -->
      <div class="dash-panel">
        <div class="panel-head"><i class="fa fa-layer-group"></i> Combo</div>
        <div class="panel-body">
          <div class="table-wrap">
            <table class="table table-hover table-sm product-table">
              <thead>
                <tr>
                  <th class="sticky" style="width:45%;">Thông tin combo</th>
                  <!-- Thứ tự metric theo yêu cầu: Doanh thu, Số lượng (đơn), Tỷ lệ đóng góp, AOV, Tỷ lệ mua lại -->
                  <th class="sticky text-right" style="width:15%;">Doanh thu</th>
                  <th class="sticky text-right" style="width:15%;">Số lượng</th><!-- = số đơn -->
                  <th class="sticky text-right" style="width:12%;">Tỷ lệ đóng góp</th>
                  <th class="sticky text-right" style="width:13%;">AOV</th>
                  <th class="sticky text-right" style="width:10%;">Tỷ lệ mua lại</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $rows_combos = !empty($combos_revenue) ? array_slice($combos_revenue, 0, $MAX_ROWS) : [];
                if (!empty($rows_combos)) :
                  $total_combos_revenue = 0.0;
                  $sumOrdersForAOV      = 0;

                  foreach ($rows_combos as $c) :
                    $img   = !empty($c['image_url']) ? $c['image_url'] : 'https://mochavietnam.com.vn/thumbs/600x600x2/upload/photo/tai-xuong-3278.png';
                    $code  = isset($c['product_id']) ? (string)$c['product_id'] : '-';
                    $name  = !empty($c['product_name']) ? $c['product_name'] : '-';
                    $rev   = (float)($c['revenue'] ?? 0);

                    // SỐ ĐƠN DISTINCT cho combo (đúng "có thì 1")
                    $orders = view_extract_order_count($c);

                    $total_combos_revenue += $rev;
                    $sumOrdersForAOV      += $orders;

                    $pct  = ($total_revenue > 0) ? ($rev * 100 / $total_revenue) : 0;

                    // AOV: field sẵn có / map / fallback theo orders
                    $aov  = isset($c['aov']) ? (float)$c['aov']
                          : (isset($aov_map_code[$code]) ? $aov_map_code[$code]
                          : (isset($aov_map_name[$name]) ? $aov_map_name[$name]
                          : ($orders > 0 ? ($rev / $orders) : 0.0)));

                    // Repurchase rate (%): field sẵn có / map
                    $rate = isset($c['repurchase_rate']) ? (float)$c['repurchase_rate']
                          : (isset($rep_map_code[$code]) ? $rep_map_code[$code]
                          : (isset($rep_map_name[$name]) ? $rep_map_name[$name] : null));
              ?>
                <tr>
                  <td>
                    <div class="product-cell">
                      <img class="product-thumb" src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="">
                      <div class="product-info">
                        <div class="code"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="name"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="product-value"><strong><?= number_format($rev, 0, ',', '.'); ?></strong></td>
                  <td class="product-value"><strong><?= number_format($orders, 0, ',', '.'); ?></strong></td>
                  <td class="product-value"><strong><?= number_format($pct, 2, ',', '.'); ?>%</strong></td>
                  <td class="product-value"><strong><?= number_format($aov, 0, ',', '.'); ?></strong></td>
                  <td class="product-value"><strong><?= is_null($rate) ? '—' : (number_format($rate, 2, ',', '.') . '%'); ?></strong></td>
                </tr>
              <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="6" class="text-center">Không có dữ liệu combo.</td></tr>
              <?php endif; ?>
              </tbody>

              <?php if (!empty($rows_combos)) : ?>
              <?php $weightedAOV = ($sumOrdersForAOV > 0) ? ($total_combos_revenue / $sumOrdersForAOV) : null; ?>
              <tfoot>
                <tr>
                  <th class="text-right">TỔNG</th>
                  <th class="product-value"><strong><?= number_format($total_combos_revenue, 0, ',', '.'); ?> đ</strong></th>
                  <th class="product-value"><strong><?= number_format($sumOrdersForAOV, 0, ',', '.'); ?></strong></th>
                  <th class="product-value">
                    <strong><?= ($total_revenue > 0) ? number_format(($total_combos_revenue * 100 / $total_revenue), 2, ',', '.') . '%' : '0%'; ?></strong>
                  </th>
                  <th class="product-value">
                    <strong><?= is_null($weightedAOV) ? '—' : number_format($weightedAOV, 0, ',', '.'); ?></strong>
                  </th>
                  <th class="product-value"><strong>—</strong></th>
                </tr>
              </tfoot>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>

    </div><!-- /.info-section -->
  </div><!-- /.content -->
</div><!-- /#wrapper -->

<?php init_tail(); ?>
</body>
</html>
