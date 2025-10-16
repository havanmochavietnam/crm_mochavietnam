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

            <form id="call-sync-settings-form" method="post" action="<?= admin_url('call_sync/call_settings/save'); ?>" autocomplete="off">
              <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

              <div class="row">
                <div class="col-md-6">
                  <div class="card-modern">
                    <div class="card-modern-body">
                      
                      <h5 class="tw-mb-3"><i class="fa fa-sliders text-primary"></i> Cấu hình dịch vụ</h5>

                      <div class="form-group">
                        <label for="base_url">Base URL</label>
                        <input type="text" id="base_url" name="base_url" class="form-control" value="<?= html_escape($token->base_url ?? ''); ?>">
                      </div>

                      <div class="form-group">
                        <label for="service_name">Service Name</label>
                        <input type="text" id="service_name" name="service_name" class="form-control" value="<?= html_escape($token->service_name ?? ''); ?>">
                      </div>

                      <div class="form-group">
                        <label for="auth_user">Auth User</label>
                        <input type="text" id="auth_user" name="auth_user" class="form-control" value="<?= html_escape($token->auth_user ?? ''); ?>" autocomplete="off">
                      </div>

                      <div class="form-group">
                        <label for="auth_key">Auth Key</label>
                        <div class="input-group">
                          <input type="password" id="auth_key" name="auth_key" class="form-control" value="<?= html_escape($token->auth_key ?? ''); ?>" autocomplete="new-password">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="toggleAuthKey" tabindex="-1" aria-label="Hiện/ẩn Auth Key">
                              <i class="fa fa-eye"></i>
                            </button>
                          </span>
                        </div>
                      </div>

                      <hr>
                      <h5 class="tw-mb-3"><i class="fa fa-feather text-info"></i> Token Lark</h5>

                      <div class="form-group">
                        <label for="lark_auth_endpoint">AuthEndpoint</label>
                        <input type="text" id="lark_auth_endpoint" name="lark_auth_endpoint" class="form-control"
                          value="<?= html_escape($token->lark_auth_endpoint ?? 'https://open.larksuite.com/open-apis/auth/v3/tenant_access_token/internal/'); ?>"
                          placeholder="https://open.larksuite.com/open-apis/auth/v3/tenant_access_token/internal/">
                        <small class="text-muted">Endpoint để lấy <code>tenant_access_token</code>.</small>
                      </div>

                      <div class="form-group">
                        <label for="lark_app_id">AppId</label>
                        <input type="text" id="lark_app_id" name="lark_app_id" class="form-control" value="<?= html_escape($token->lark_app_id ?? ''); ?>" placeholder="cli_xxxxxxxxxxxxxxxxx" autocomplete="off">
                      </div>

                      <div class="form-group">
                        <label for="lark_app_secret">AppSecret</label>
                        <div class="input-group">
                          <input type="password" id="lark_app_secret" name="lark_app_secret" class="form-control" value="<?= html_escape($token->lark_app_secret ?? ''); ?>" placeholder="************************" autocomplete="new-password">
                          <span class="input-group-btn">
                            <button class="btn btn-default" type="button" id="toggleSecret" tabindex="-1" aria-label="Hiện/ẩn AppSecret">
                              <i class="fa fa-eye"></i>
                            </button>
                          </span>
                        </div>
                        <small class="text-muted">Nhấn biểu tượng mắt để hiện/ẩn giá trị.</small>
                      </div>

                      <button type="submit" class="btn btn-primary">Lưu cài đặt</button>
                    </div>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="card-modern">
                    <div class="card-modern-body">

                      <h5 class="tw-mb-3"><i class="fa fa-table text-info"></i> Bảng Lark Cần Đẩy Dữ Liệu Vào</h5>
                      <div class="form-group">
                        <label for="bitable_app_token">App Token</label>
                        <input type="text" id="bitable_app_token" name="bitable_app_token" class="form-control"
                          value="<?= html_escape($token->bitable_app_token ?: 'X20AblnVNaP6mbseIIplCo7RgWq'); ?>"
                          placeholder="Bitable App Token">
                      </div>
                      <div class="form-group">
                        <label for="bitable_table_id">Table ID</label>
                        <input type="text" id="bitable_table_id" name="bitable_table_id" class="form-control"
                          value="<?= html_escape($token->bitable_table_id ?: 'tblRA8n77fO2eKxF'); ?>"
                          placeholder="Bitable Table ID">
                      </div>
                      <hr>
                      <h5 class="tw-mb-3"><i class="fa fa-cloud-upload text-info"></i> Đồng bộ & đẩy lên Lark</h5>
                      <p class="text-muted tw-mt-2">Đồng bộ dữ liệu trong ngày hôm nay và đẩy các bản ghi mới lên Lark.</p>

                      <button type="button" id="btnSyncNowPushLark" class="btn btn-info">
                        <i class="fa fa-cloud-upload"></i> Đồng bộ & đẩy Lark
                      </button>

                      <div id="lock-status" class="tw-mt-2 text-muted"></div>

                      <hr>
                      <h5><i class="fa fa-history text-info"></i> Lịch sử đồng bộ gần đây</h5>
                      <div id="sync-log" class="sync-log-box">
                          <div class="log-item text-muted">Đang tải lịch sử...</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>

<style>
/* ... (Phần CSS giữ nguyên không đổi) ... */
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
  max-height: 260px;
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
.tw-mb-3 {
  margin-bottom: 12px;
}
.tw-mt-2 {
  margin-top: 8px;
}
</style>

<script>
$(function() {
  // === BẮT ĐẦU PHẦN CODE MỚI CHO REAL-TIME LOG ===

  // Hàm để tránh lỗi XSS khi hiển thị dữ liệu từ server
  function escapeHtml(text) {
    if (typeof text !== 'string') return '';
    var map = {
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }

  // Hàm chính để cập nhật bảng log
  function updateSyncLog() {
    $.get("<?= admin_url('call_sync/call_settings/fetch_logs_ajax'); ?>")
      .done(function(data) {
        const logBox = $('#sync-log');
        logBox.empty(); // Xóa log cũ

        if (data && data.length > 0) {
          data.forEach(function(log) {
            const statusClass = log.status === 'success' ? 'text-success' : 'text-danger';
            const type = log.sync_type ? escapeHtml(log.sync_type.charAt(0).toUpperCase() + log.sync_type.slice(1)) : 'N/A';
            let messageHtml = log.message ? `<div class="text-muted">${escapeHtml(log.message)}</div>` : '';
            
            const logHtml = `
              <div class="log-item ${statusClass}">
                [${log.formatted_date}]
                ${type} - ${log.records_synced} bản ghi (${log.status})
                ${messageHtml}
              </div>
            `;
            logBox.append(logHtml);
          });
        } else {
          logBox.html('<div class="log-item text-muted">Chưa có lịch sử đồng bộ...</div>');
        }
      })
      .fail(function() {
         console.error("Không thể tải lịch sử đồng bộ.");
      });
  }

  // Chạy lần đầu tiên ngay khi tải trang
  updateSyncLog();
  // Thiết lập bộ đếm thời gian: cứ 15 giây sẽ gọi hàm updateSyncLog một lần
  setInterval(updateSyncLog, 15000); 

  // === KẾT THÚC PHẦN CODE MỚI ===


  // Toggle secret inputs
  $('#toggleSecret').on('click', function() {
    const $input = $('#lark_app_secret');
    const isPwd = $input.attr('type') === 'password';
    $input.attr('type', isPwd ? 'text' : 'password');
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });
  $('#toggleAuthKey').on('click', function() {
    const $input = $('#auth_key');
    const isPwd = $input.attr('type') === 'password';
    $input.attr('type', isPwd ? 'text' : 'password');
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
  });

  // Submit form (save only)
  $('#call-sync-settings-form').on('submit', function(e) {
    e.preventDefault();
    const form = $(this);
    $.post(form.attr('action'), form.serialize())
      .done(function(res) {
        let data = res;
        try {
          data = (typeof res === 'string') ? JSON.parse(res) : res;
        } catch (e) {}
        if (data.csrf_name && data.csrf_hash) {
          $('input[name="' + data.csrf_name + '"]').val(data.csrf_hash);
        }
        if (data.success) {
          alert_float('success', data.message || 'Đã lưu cài đặt!');
        } else {
          alert_float('danger', data.message || 'Lưu thất bại');
        }
      })
      .fail(function(xhr) {
        alert_float('danger', 'Lưu thất bại. Vui lòng kiểm tra lại.');
      });
  });

  // Đồng bộ + đẩy Lark: LƯU TRƯỚC -> rồi mới sync
  $('#btnSyncNowPushLark').on('click', function() {
    const $btn = $(this);
    const saveData = $('#call-sync-settings-form').serialize();

    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu & lấy token...');
    
    $.post("<?= admin_url('call_sync/call_settings/save'); ?>", saveData)
      .done(function(res) {
        try {
          res = (typeof res === 'string') ? JSON.parse(res) : res;
        } catch (e) {}

        if (res.csrf_name && res.csrf_hash) {
          $('input[name="' + res.csrf_name + '"]').val(res.csrf_hash);
        }

        if (!res.success) {
          alert_float('danger', res.message || 'Lưu cấu hình thất bại');
          $btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Đồng bộ & đẩy Lark');
          updateSyncLog();
          return;
        }

        $btn.html('<i class="fa fa-spinner fa-spin"></i> Đang đồng bộ & đẩy lên Lark...');
        const appToken = $('#bitable_app_token').val().trim();
        const tableId = $('#bitable_table_id').val().trim();

        $.post("<?= admin_url('call_sync/call_settings/sync_now_push_lark'); ?>", {
            app_token: appToken,
            table_id: tableId,
            '<?= $this->security->get_csrf_token_name(); ?>': $('input[name="<?= $this->security->get_csrf_token_name(); ?>"]').val()
          })
          .done(function(r2) {
            try { r2 = (typeof r2 === 'string') ? JSON.parse(r2) : r2; } catch (e) {}
            
            if (r2.success) {
              alert_float('success', `Đã đồng bộ và đẩy Lark: ${r2.pushed || 0} bản ghi`);
            } else {
              alert_float('danger', r2.message || 'Đồng bộ + đẩy Lark thất bại');
            }
          })
          .fail(function() {
            alert_float('danger', 'Lỗi gọi API đồng bộ + đẩy Lark');
          })
          .always(function() {
            $btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Đồng bộ & đẩy Lark');
            // Gọi hàm cập nhật log ngay sau khi hoàn tất để thấy kết quả ngay lập tức
            setTimeout(updateSyncLog, 500);
          });
      })
      .fail(function() {
        alert_float('danger', 'Lỗi gọi API lưu cấu hình');
        $btn.prop('disabled', false).html('<i class="fa fa-cloud-upload"></i> Đồng bộ & đẩy Lark');
      });
  });
});
</script>