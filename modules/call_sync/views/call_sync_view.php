<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$title = $title ?? 'Nhật ký cuộc gọi';
$total = isset($total) ? (int)$total : null;
$calls = is_array($calls) ? $calls : [];
$pagination = $pagination ?? '';

// === HÀM HELPER ĐỂ CHUYỂN ĐỔI GIÂY SANG HH:MM:SS ===
if (!function_exists('seconds_to_hms')) {
    function seconds_to_hms($seconds) {
        $seconds = (int)$seconds;
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }
}
?>

<?php init_head(); ?>
<link rel="stylesheet" href="<?= module_dir_url('call_sync', 'assets/css/call_sync.css'); ?>?v=<?= time(); ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/styles/overlayscrollbars.min.css" />

<div id="wrapper">
  <div class="row">
    <div class="col-md-12">
      <div class="card-modern">
        <div class="card-modern-body">
          <div class="card-modern-header">
            <div>
              <h4 class="no-margin">
                <i class="fa fa-phone"></i> <?= html_escape($title) ?>
                <small style="margin-left:8px;color:#6c757d;">(Tổng: <?= (int)($total ?? count($calls)) ?>)</small>
              </h4>
            </div>

            <div class="header-actions">
              <form action="<?= admin_url('call_sync'); ?>" method="get" style="display:flex;gap:8px;align-items:center;">
                <input type="text" name="q" class="form-control search-input" placeholder="Nhập số gọi, số nhận, tên hoặc trạng thái..."
                  value="<?= html_escape($this->input->get('q') ?? '') ?>">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
              </form>
            </div>
          </div>

          <hr />

          <form action="<?= admin_url('call_sync'); ?>" method="get" class="filters-bar" role="search">
            <div class="form-group">
              <label>Từ ngày</label>
              <input type="date" name="start_date" class="form-control" value="<?= html_escape($this->input->get('start_date') ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Đến ngày</label>
              <input type="date" name="end_date" class="form-control" value="<?= html_escape($this->input->get('end_date') ?? '') ?>">
            </div>

            <div class="form-group">
              <label>Trạng thái</label>
              <select name="status" class="form-control">
                <?php
                $st = $this->input->get('status') ?? '';
                $sopts = ['' => 'Tất cả', 'ANSWERED' => 'Thành công', 'BUSY' => 'Máy bận', 'NO ANSWER' => 'Không trả lời', 'FAILED' => 'FAILED'];
                foreach ($sopts as $k => $v) {
                  $sel = ($st === $k) ? 'selected' : '';
                  echo "<option value=\"" . html_escape($k) . "\" $sel>" . html_escape($v) . "</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group">
              <label>Số dòng / trang</label>
              <select name="page_size" class="form-control">
                <?php
                $ps = (int)($this->input->get('page_size') ?? 30);
                foreach ([30, 50, 100, 200, 500] as $n) {
                  $sel = ($ps === $n) ? 'selected' : '';
                  echo "<option value=\"$n\" $sel>$n</option>";
                }
                ?>
              </select>
            </div>

            <div class="form-group" style="display:flex;align-items:flex-end;gap:8px;">
              <button type="submit" class="btn btn-success">Lọc</button>
              <a href="<?= admin_url('call_sync'); ?>" class="btn btn-secondary">Đặt lại</a>
            </div>
          </form>

          <div id="scrollableTable" class="table-container">
            <table class="table table-sticky table-bordered">
              <thead>
                <tr>
                  <th class="sticky-col sticky-col-1">#</th>
                  <th class="sticky-col sticky-col-2">Thời gian</th>
                  <th>Ghi âm</th>
                  <th>Hướng cuộc gọi</th>
                  <th>Tên người gọi</th>
                  <th>Số gọi</th>
                  <th>Đầu số</th>
                  <th>Số nhận</th>
                  <th>Trạng thái</th>
                  <th>Thời lượng thực</th>
                  <th>Tổng thời gian</th>
                  <th>File ghi âm</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($calls)):
                  $page = (int)($this->input->get('page_number') ?? 1);
                  $page_size = (int)($this->input->get('page_size') ?? 30);
                  $stt = ($page - 1) * $page_size + 1;
                  foreach ($calls as $row): ?>
                    <tr>
                      <td class="sticky-col sticky-col-1"><?= $stt++; ?></td>
                      <td class="sticky-col sticky-col-2"><?= html_escape($row['call_date'] ?? ''); ?></td>
                      <td class="text-center">
                        <?php if (!empty($row['link_file'])): ?>
                          <button type="button" class="badge-audio btn-play" data-src="<?= html_escape($row['link_file']); ?>">Nghe</button>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php
                        $type_call = $row['type_call'] ?? '';
                        if ($type_call === 'Inbound') {
                          echo '<span class="call-direction-badge call-inbound">Gọi vào</span>';
                        } elseif ($type_call === 'Outbound') {
                          echo '<span class="call-direction-badge call-outbound">Gọi ra</span>';
                        } else {
                          echo '<span class="status-badge status-default">' . html_escape($type_call) . '</span>';
                        }
                        ?>
                      </td>
                      <td><?= html_escape($row['caller_name'] ?? ''); ?></td>
                      <td><?= html_escape($row['caller_number'] ?? ''); ?></td>
                      <td><?= html_escape($row['head_number'] ?? ''); ?></td>
                      <td><?= html_escape($row['receive_number'] ?? ''); ?></td>
                      <td>
                        <?php
                        $status = $row['status'] ?? '';
                        if ($status === 'ANSWERED') {
                          echo '<span class="status-badge status-success">Thành công</span>';
                        } elseif ($status === 'NO ANSWER') {
                          echo '<span class="status-badge status-no-answer">Không trả lời</span>';
                        } elseif ($status === 'BUSY') {
                          echo '<span class="status-badge status-busy">Máy bận</span>';
                        } else {
                          echo '<span class="status-badge status-default">' . html_escape($status) . '</span>';
                        }
                        ?>
                      </td>
                      <td class="text-right"><?= seconds_to_hms($row['real_call_time'] ?? 0); ?></td>
                      <td class="text-right"><?= seconds_to_hms($row['total_call_time'] ?? 0); ?></td>
                      <td>
                        <?php if (!empty($row['link_file'])): ?>
                          <a href="<?= html_escape($row['link_file']); ?>" target="_blank" rel="noopener">File</a>
                        <?php else: ?>
                          <span class="text-muted">-</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach;
                else: ?>
                  <tr>
                    <td colspan="13" class="text-center">Không có dữ liệu cuộc gọi.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <?php if (!empty($pagination)): ?>
            <div class="text-center" style="margin-top:15px;"><?= $pagination ?></div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<div id="audioPopup" class="audio-popup" aria-hidden="true">
  <div class="audio-popup-content" role="dialog" aria-modal="true" aria-label="Nghe ghi âm">
    <span class="close-popup" title="Đóng">&times;</span>
    <h5>🎧 Nghe ghi âm</h5>
    <audio id="audioPlayer" controls style="width:100%; margin-top:8px;">Your browser does not support the audio element.</audio>
    <div style="margin-top:8px; font-size:13px; color:#666;">Nhấn <strong>×</strong> hoặc click ra ngoài để đóng.</div>
  </div>
</div>

<?php init_tail(); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/overlayscrollbars/2.7.0/browser/overlayscrollbars.browser.es6.min.js"></script>
<script>
  // ... (Phần JavaScript giữ nguyên không đổi) ...
  document.addEventListener('DOMContentLoaded', function() {
    try {
      const { OverlayScrollbars } = OverlayScrollbarsGlobal;
      const wrap = document.getElementById('scrollableTable');
      if (wrap) OverlayScrollbars(wrap, { scrollbars: { theme: 'os-theme-dark' } });
    } catch (e) { console.warn('OverlayScrollbars init failed', e); }

    const popup = document.getElementById('audioPopup');
    const audioPlayer = document.getElementById('audioPlayer');
    const closeBtn = popup ? popup.querySelector('.close-popup') : null;

    function openPopup(src) {
      if (!popup || !audioPlayer) return;
      audioPlayer.src = src;
      audioPlayer.currentTime = 0;
      audioPlayer.play().catch(() => {});
      popup.style.display = 'flex';
      popup.setAttribute('aria-hidden', 'false');
    }

    function closePopup() {
      if (!popup || !audioPlayer) return;
      audioPlayer.pause();
      audioPlayer.src = '';
      popup.style.display = 'none';
      popup.setAttribute('aria-hidden', 'true');
    }

    document.querySelectorAll('.btn-play').forEach(btn => {
      btn.addEventListener('click', function(ev) {
        ev.preventDefault();
        const src = this.dataset.src;
        if (src) openPopup(src);
      });
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function() { closePopup(); });
    }
    window.addEventListener('click', function(e) {
      if (popup && e.target === popup) closePopup();
    });
    window.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closePopup();
    });
  });
</script>
</body>

</html>