<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
// Cho phép view chạy độc lập để bạn test nhanh: nếu controller chưa truyền sẵn $title, $total, $calls
$title = $title ?? 'Nhật ký cuộc gọi';
$total = isset($total) ? (int)$total : null;

// Dữ liệu giả (10 dòng) nếu chưa có $calls từ controller
if (!isset($calls) || !is_array($calls) || empty($calls)) {
    $calls = [];
    $base = strtotime(date('Y-m-d 09:00:00'));
    for ($i = 1; $i <= 10; $i++) {
        $ts = date('Y-m-d H:i:s', $base + ($i * 137));
        $status = ['Thành công','Nhỡ','Bận','Thất bại'][($i % 4)];
        $direction = ($i % 2 === 0) ? 'Inbound' : 'Outbound';
        $calls[] = [
            'time'            => $ts,
            'recording_url'   => ($status === 'Thành công') ? 'https://example.com/audio/sample'.$i.'.mp3' : '',
            'caller_name'     => 'Nguyễn Văn '.sprintf('%02d', $i),
            'caller_number'   => '09'.str_pad((string)(1234567+$i), 8, '0', STR_PAD_LEFT),
            'trunk'           => '0247-VOIP-0'.(($i%3)+1),
            'receive_group'   => ['CSKH','Bán hàng','Kỹ thuật'][($i%3)],
            'callee_number'   => '0283'.str_pad((string)(765432+$i), 7, '0', STR_PAD_LEFT),
            'status'          => $status,
            'talk_duration'   => sprintf('00:0%d:%02d', ($i%6), ($i*7)%60),
            'total_duration'  => sprintf('00:%02d:%02d', 1+($i%5), ($i*11)%60),
            'call_id'         => 'CALL-2025-'.str_pad((string)$i, 6, '0', STR_PAD_LEFT),
            'direction'       => $direction,
            'agent'           => 'Agent #'.(($i%5)+1),
            'ring_seconds'    => (string)(2 + ($i%6)),
            'queue_seconds'   => (string)(($i%4)*3),
            'ended_by'        => ($status==='Thành công') ? 'Agent' : 'Hệ thống',
            'cost'            => (int)(850 + ($i%5)*120),
            'campaign'        => 'Chiến dịch Q'.(($i%3)+1),
            'tags'            => ($i%2?['VIP','Ưu tiên']:['Thường']),
            'note'            => ($i%3===0?'Khách yêu cầu gọi lại':''),
            'sla'             => ($i%2===0?'Đạt SLA':'Quá SLA'),
        ];
    }
    if ($total === null) $total = count($calls);
}
?>

<?php init_head(); ?>
<link rel="stylesheet" href="<?= module_dir_url('call_sync', 'assets/css/call_sync.css'); ?>?v=<?= time(); ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/styles/overlayscrollbars.min.css" />

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="card-modern">
          <div class="card-modern-body">

            <!-- Header -->
            <div class="card-modern-header">
              <div class="header-title">
                <h4 class="no-margin">
                  <i class="fa fa-phone" aria-hidden="true"></i>
                  <?= html_escape($title) ?>
                  <small>(Tổng: <?= (int)($total ?? count($calls)) ?>)</small>
                </h4>
              </div>

              <div class="header-actions">
                <form action="<?= admin_url('call_sync/call_logs'); ?>" method="get" class="search-wrapper">
                  <span class="search-icon"><i class="fa fa-search"></i></span>
                  <input type="text"
                         name="q"
                         class="form-control search-input"
                         placeholder="Nhập số gọi, số nhận, tên hoặc ghi chú..."
                         value="<?= html_escape($this->input->get('q') ?? '') ?>">
                  <button type="submit" class="search-button">Tìm kiếm</button>
                </form>

                <a href="<?= admin_url('call_sync/call_logs/sync'); ?>" class="sync-button">
                  <i class="fa fa-refresh" aria-hidden="true"></i> Đồng bộ
                </a>
              </div>
            </div>

            <!-- Bộ lọc -->
            <form action="<?= admin_url('call_sync/call_logs'); ?>" method="get" class="filters-bar">
              <div class="filters-grid">
                <div class="form-group">
                  <label>Từ ngày</label>
                  <input type="date" name="start_date" class="form-control"
                         value="<?= html_escape($this->input->get('start_date') ?? '') ?>">
                </div>
                <div class="form-group">
                  <label>Đến ngày</label>
                  <input type="date" name="end_date" class="form-control"
                         value="<?= html_escape($this->input->get('end_date') ?? '') ?>">
                </div>
                <div class="form-group">
                  <label>Hướng gọi</label>
                  <select name="direction" class="form-control">
                    <?php
                    $dir = $this->input->get('direction') ?? '';
                    $opts = ['' => 'Tất cả', 'Inbound' => 'Inbound', 'Outbound' => 'Outbound'];
                    foreach ($opts as $k => $v) {
                        $sel = ($dir===$k)?'selected':'';
                        echo "<option value=\"".html_escape($k)."\" $sel>".html_escape($v)."</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Trạng thái</label>
                  <select name="status" class="form-control">
                    <?php
                    $st = $this->input->get('status') ?? '';
                    $sopts = [''=>'Tất cả','Thành công'=>'Thành công','Nhỡ'=>'Nhỡ','Bận'=>'Bận','Thất bại'=>'Thất bại'];
                    foreach ($sopts as $k=>$v){
                        $sel = ($st===$k)?'selected':'';
                        echo "<option value=\"".html_escape($k)."\" $sel>".html_escape($v)."</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="form-group">
                  <label>Số dòng / trang</label>
                  <select name="page_size" class="form-control">
                    <?php
                    $ps = (int)($this->input->get('page_size') ?? 30);
                    foreach ([30,50,100,200,500] as $n) {
                      $sel = ($ps===$n)?'selected':'';
                      echo "<option value=\"$n\" $sel>$n</option>";
                    }
                    ?>
                  </select>
                </div>

                <div class="filter-actions">
                  <button type="submit" class="btn btn-primary">Lọc</button>
                  <a class="btn btn-outline" href="<?= admin_url('call_sync/call_logs'); ?>">Đặt lại</a>
                </div>
              </div>
            </form>

            <!-- Bảng -->
            <div id="scrollableTable" class="table-container">
              <table class="table table-pancake table-bordered table-striped table-sticky">
                <thead>
                  <tr>
                    <th class="sticky-col sticky-col-1 text-center">STT</th>
                    <th class="sticky-col sticky-col-2">Thời gian</th>

                    <th>Ghi âm</th>
                    <th>Tên số gọi</th>
                    <th>Số gọi</th>
                    <th>Đầu số tổng đài</th>
                    <th>Nhóm nhận</th>
                    <th>Số nhận</th>
                    <th>Trạng thái</th>
                    <th>Thời lượng đàm thoại</th>
                    <th>Thời lượng gọi</th>
                    <th>Mã cuộc gọi</th>

                    <th>Hướng gọi</th>
                    <th>Agent</th>
                    <th>Ring (s)</th>
                    <th>Queue (s)</th>
                    <th>Kết thúc bởi</th>
                    <th>Cước (đ)</th>
                    <th>Chiến dịch</th>
                    <th>Thẻ</th>
                    <th>Ghi chú</th>
                    <th>SLA</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $page      = (int)($this->input->get('page_number') ?? 1);
                  $page_size = (int)($this->input->get('page_size') ?? 30);
                  $stt       = ($page - 1) * $page_size + 1;
                ?>
                <?php foreach ($calls as $row): ?>
                  <tr>
                    <td class="sticky-col sticky-col-1 text-center"><?= $stt++; ?></td>
                    <td class="sticky-col sticky-col-2"><?= date('d/m/Y H:i:s', strtotime($row['time'])) ?></td>

                    <td class="text-center">
                      <?php if (!empty($row['recording_url'])): ?>
                        <a href="<?= html_escape($row['recording_url']) ?>" target="_blank" class="badge badge-audio">Nghe/Tải</a>
                      <?php else: ?>
                        <span class="text-muted">N/A</span>
                      <?php endif; ?>
                    </td>
                    <td><strong><?= html_escape($row['caller_name']) ?></strong></td>
                    <td><?= html_escape($row['caller_number']) ?></td>
                    <td><?= html_escape($row['trunk']) ?></td>
                    <td><?= html_escape($row['receive_group']) ?></td>
                    <td><?= html_escape($row['callee_number']) ?></td>
                    <td>
                      <?php
                        $status = $row['status'];
                        $map = [
                          'Thành công' => 'success',
                          'Nhỡ'        => 'warning',
                          'Bận'        => 'info',
                          'Thất bại'   => 'danger'
                        ];
                        $cls = $map[$status] ?? 'default';
                      ?>
                      <span class="label label-<?= $cls ?>"><?= html_escape($status) ?></span>
                    </td>
                    <td class="text-right"><?= html_escape($row['talk_duration']) ?></td>
                    <td class="text-right"><?= html_escape($row['total_duration']) ?></td>
                    <td><code><?= html_escape($row['call_id']) ?></code></td>

                    <td><?= html_escape($row['direction']) ?></td>
                    <td><?= html_escape($row['agent']) ?></td>
                    <td class="text-right"><?= html_escape($row['ring_seconds']) ?></td>
                    <td class="text-right"><?= html_escape($row['queue_seconds']) ?></td>
                    <td><?= html_escape($row['ended_by']) ?></td>
                    <td class="text-right"><?= number_format($row['cost']) ?></td>
                    <td><?= html_escape($row['campaign']) ?></td>
                    <td>
                      <?php if (!empty($row['tags'])): ?>
                        <?php foreach ($row['tags'] as $t): ?>
                          <span class="label label-tag label-info"><?= html_escape($t) ?></span>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </td>
                    <td class="truncate-cell" title="<?= html_escape($row['note']) ?>"><?= html_escape($row['note']) ?></td>
                    <td>
                      <span class="label <?= ($row['sla']==='Đạt SLA'?'label-success':'label-danger') ?>">
                        <?= html_escape($row['sla']) ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>

                <?php if (empty($calls)): ?>
                  <tr><td colspan="22" class="text-center">Không có dữ liệu.</td></tr>
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
document.addEventListener('DOMContentLoaded', function () {
  // Auto fill last 7 days nếu chưa có filter
  const s = document.querySelector('input[name="start_date"]');
  const e = document.querySelector('input[name="end_date"]');
  if (s && e && !s.value && !e.value) {
    const now = new Date();
    const from = new Date(now); from.setDate(now.getDate() - 7);
    const toISO = d => d.toISOString().slice(0,10);
    s.value = toISO(from);
    e.value = toISO(now);
  }

  const wrap = document.getElementById('scrollableTable');
  if (wrap && typeof OverlayScrollbars !== 'undefined') {
    try {
      OverlayScrollbars(wrap, { scrollbars: { theme: 'os-theme-dark' } });
    } catch (err) {
      // không block nếu plugin gặp lỗi
      console.warn('OverlayScrollbars init failed', err);
    }
  }
});
</script>
</body>
</html>
