<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="tw-max-w-4xl tw-mx-auto">

            <!-- Pancake Settings Form -->
            <h4 class="tw-mt-0 tw-font-bold tw-text-lg tw-text-neutral-700">
                <?= _l('pancake_sync_settings'); ?>
            </h4>

            <?= form_open(admin_url('pancake_sync/save_settings'), ['id' => 'pancake-settings-form']); ?>
            <div class="panel_s">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="pancake_url"><?= _l('pancake_url'); ?></label>
                        <input type="text" name="pancake_url" id="pancake_url" class="form-control"
                               value="<?= e($settings['pancake_url']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="api_key"><?= _l('api_key'); ?></label>
                        <input type="text" name="api_key" id="api_key" class="form-control"
                               value="<?= e($settings['api_key']); ?>" required>
                    </div>

                    <div class="form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="enabled" id="enabled" <?= $settings['enabled'] ? 'checked' : ''; ?>>
                            <label for="enabled"><?= _l('enable_sync'); ?></label>
                        </div>
                    </div>

                </div>
                <div class="panel-footer text-right">
                    <button type="submit" class="btn btn-primary"><?= _l('save'); ?></button>
                </div>
            </div>
            <?= form_close(); ?>

            <!-- Sync Actions -->
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="tw-mt-0 tw-font-bold tw-text-base tw-text-neutral-700"><?= _l('sync_actions'); ?></h4>
                    <hr class="hr-panel-heading">
                    <div class="row">
                        <?php $syncTypes = ['customers', 'products', 'invoices', 'all']; ?>
                        <?php foreach ($syncTypes as $type): ?>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-block" onclick="syncData('<?= $type; ?>')">
                                <?= _l('sync_' . $type); ?>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Sync Logs -->
            <div class="panel_s">
                <div class="panel-body">
                    <h4 class="tw-mt-0 tw-font-bold tw-text-base tw-text-neutral-700"><?= _l('sync_logs'); ?></h4>
                    <hr class="hr-panel-heading">
                    <div class="table-responsive">
                        <table class="table dt-table">
                            <thead>
                                <tr>
                                    <th><?= _l('sync_type'); ?></th>
                                    <th><?= _l('records_synced'); ?></th>
                                    <th><?= _l('date'); ?></th>
                                    <th><?= _l('status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
                                <tr>
                                    <td><?= ucfirst($log['sync_type']); ?></td>
                                    <td><?= $log['records_synced']; ?></td>
                                    <td><?= _dt($log['date']); ?></td>
                                    <td>
                                        <span class="label label-<?= $log['status'] == 'success' ? 'success' : 'danger'; ?>">
                                            <?= ucfirst($log['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
appValidateForm('#pancake-settings-form');

function syncData(type) {
    $.post(admin_url + 'pancake_sync/sync_' + type, {
        csrf_token_name: $('input[name=csrf_token_name]').val()
    }).done(function(response) {
        alert_float('success', response.message);
        location.reload();
    }).fail(function(error) {
        alert_float('danger', error.responseJSON.message);
    });
}
</script>
</body>
</html>
