<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">

            <div class="tw-flex tw-justify-between tw-items-center tw-mb-4">
              <h4><i class="fa fa-cog text-primary"></i> <?= _l('Cài đặt & Đồng bộ Tổng đài'); ?></h4>
            </div>
            <hr>

            <div class="row">
              <!-- CỘT TRÁI: FORM CÀI ĐẶT -->
              <div class="col-md-6">
                <div class="card-modern">
                  <div class="card-modern-body">
                    <form id="call-sync-settings-form" method="post" action="<?= admin_url('call_sync/call_settings/save'); ?>">
                      <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                        value="<?= $this->security->get_csrf_hash(); ?>">

                      <div class="form-group">
                        <label for="base_url">Base URL</label>
                        <input type="text" id="base_url" name="base_url" class="form-control"
                          value="<?= html_escape($token->base_url ?? ''); ?>"
                          placeholder="">
                      </div>

                      <div class="form-group">
                        <label for="service_name">Service Name</label>
                        <input type="text" id="service_name" name="service_name" class="form-control"
                          value="<?= html_escape($token->service_name ?? ''); ?>"
                          placeholder="">
                      </div>

                      <div class="form-group">
                        <label for="auth_user">Auth User</label>
                        <input type="text" id="auth_user" name="auth_user" class="form-control"
                          value="<?= html_escape($token->auth_user ?? ''); ?>"
                          placeholder="">
                      </div>

                      <div class="form-group">
                        <label for="auth_key">Auth Key</label>
                        <input type="text" id="auth_key" name="auth_key" class="form-control"
                          value="<?= html_escape($token->auth_key ?? ''); ?>"
                          placeholder="">
                      </div>

                      <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </form>
                  </div>
                </div>
              </div>

              <!-- CỘT PHẢI: ĐỒNG BỘ NHANH + LỊCH SỬ -->
              <div class="col-md-6">
                <div class="card-modern">
                  <div class="card-modern-body">
                    <h5><i class="fa fa-sync text-success"></i> Công cụ đồng bộ</h5>
                    <p class="text-muted tw-mt-2">
                      Thực hiện đồng bộ dữ liệu ngay lập tức giữa hệ thống của bạn và MBO API.
                    </p>

                    <button type="button" id="btnSyncNow" class="btn btn-success tw-mt-2">
                      <i class="fa fa-refresh"></i> Đồng bộ ngay
                    </button>

                    <hr>
                    <h5><i class="fa fa-history text-info"></i> Lịch sử đồng bộ gần đây</h5>
                    <div id="sync-log" class="sync-log-box">
                      <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                          <div class="log-item <?= $log['status'] == 'success' ? 'text-success' : 'text-danger'; ?>">
                            [<?= _dt($log['date']); ?>]
                            <?= ucfirst($log['sync_type']); ?> - <?= $log['records_synced']; ?> bản ghi (<?= $log['status']; ?>)
                          </div>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <div class="log-item text-muted">Chưa có lịch sử đồng bộ...</div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div> <!-- /row -->

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<style>
  .card-modern {
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    background-color: #fff;
    transition: all 0.2s ease;
  }

  .card-modern:hover {
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  }

  .card-modern-body {
    padding: 25px;
  }

  .sync-log-box {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 10px 15px;
    max-height: 220px;
    overflow-y: auto;
    font-size: 13px;
    border: 1px solid #e1e1e1;
  }

  .log-item {
    border-bottom: 1px solid #e1e1e1;
    padding: 6px 0;
  }

  .log-item:last-child {
    border-bottom: none;
  }
</style>

<script>
  $(function() {

    // Gửi form lưu token
    $('#call-sync-settings-form').on('submit', function(e) {
      e.preventDefault();
      const form = $(this);
      $.post(form.attr('action'), form.serialize())
        .done(function(res) {
          // nếu server trả JSON string
          let data = res;
          try {
            data = (typeof res === 'string') ? JSON.parse(res) : res;
          } catch (e) {}
          if (data.csrf_name && data.csrf_hash) {
            $('input[name="' + data.csrf_name + '"]').val(data.csrf_hash);
          }
          alert_float('success', 'Đã lưu cài đặt!');
        })
        .fail(function(xhr) {
          if (xhr.status === 419 || xhr.status === 403) {
            alert_float('danger', 'Phiên đã hết hạn. Vui lòng tải lại trang và thử lại.');
          } else {
            alert_float('danger', 'Lưu thất bại');
          }
        });
    });

    // Giả lập đồng bộ
    $('#btnSyncNow').on('click', function() {
      const $btn = $(this);
      const logBox = $('#sync-log');
      const time = new Date().toLocaleString();

      $btn.prop('disabled', true).text('Đang đồng bộ...');
      logBox.prepend(`<div class="log-item text-info temp-sync">[${time}] Đang đồng bộ dữ liệu...</div>`);

      $.post("<?= admin_url('call_sync/call_settings/sync_now'); ?>", {})
        .done(function(res) {
          try {
            res = (typeof res === 'string') ? JSON.parse(res) : res;
          } catch (e) {}
          $('.temp-sync').remove();
          if (res.success) {
            logBox.prepend(`<div class="log-item text-success">[${time}] ✅ Đồng bộ hoàn tất - ${res.inserted || 0} bản ghi</div>`);
            alert_float('success', 'Đã đồng bộ: ' + (res.inserted || 0) + ' bản ghi.');
          } else {
            logBox.prepend(`<div class="log-item text-danger">[${time}] ❌ Đồng bộ thất bại: ${res.message || 'Lỗi'}</div>`);
            alert_float('danger', res.message || 'Đồng bộ thất bại');
          }
        })
        .fail(function() {
          $('.temp-sync').remove();
          logBox.prepend(`<div class="log-item text-danger">[${time}] ❌ Lỗi gọi API đồng bộ.</div>`);
          alert_float('danger', 'Lỗi gọi API đồng bộ');
        })
        .always(function() {
          $btn.prop('disabled', false).text('Đồng bộ ngay');
        });
    });
  });
</script>