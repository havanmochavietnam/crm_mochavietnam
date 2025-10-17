<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_sync_model extends App_Model
{
    protected $token_table;
    protected $logs_table;
    protected $history_table;
    protected $lark_table;
    protected $locks_table;

    public function __construct()
    {
        parent::__construct();
        $prefix               = db_prefix();
        $this->token_table    = $prefix . 'call_token';
        $this->logs_table     = $prefix . 'call_sync_logs';
        $this->history_table  = $prefix . 'call_history';
        $this->lark_table     = $prefix . 'call_lark_token';
        $this->locks_table    = $prefix . 'call_job_locks';
        $this->ensure_tables();
    }

    protected function ensure_tables()
    {
        // Token table
        if (!$this->db->table_exists($this->token_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->token_table} (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    base_url VARCHAR(1000) NOT NULL,
                    service_name VARCHAR(255) DEFAULT NULL,
                    auth_user VARCHAR(255) DEFAULT NULL,
                    auth_key VARCHAR(255) DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Logs table
        if (!$this->db->table_exists($this->logs_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->logs_table} (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    sync_type VARCHAR(50) NOT NULL,
                    records_synced INT(11) DEFAULT 0,
                    date DATETIME NOT NULL,
                    status VARCHAR(20) NOT NULL,
                    message TEXT NULL,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Call history table
        if (!$this->db->table_exists($this->history_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->history_table} (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    unique_key VARCHAR(255) DEFAULT NULL,
                    call_key VARCHAR(255) DEFAULT NULL,
                    call_date DATETIME DEFAULT NULL,
                    caller_number VARCHAR(50) DEFAULT NULL,
                    caller_name VARCHAR(100) DEFAULT NULL,
                    head_number VARCHAR(50) DEFAULT NULL,
                    receive_number VARCHAR(50) DEFAULT NULL,
                    status VARCHAR(50) DEFAULT NULL,
                    total_call_time INT DEFAULT 0,
                    real_call_time INT DEFAULT 0,
                    link_file TEXT DEFAULT NULL,
                    gsm_port VARCHAR(50) DEFAULT NULL,
                    type_call VARCHAR(50) DEFAULT NULL,
                    raw_response JSON DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    UNIQUE KEY uq_unique_key (unique_key)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }

        // Lark config table (có sẵn 2 cột bitable target)
        if (!$this->db->table_exists($this->lark_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->lark_table} (
                    id INT(11) NOT NULL AUTO_INCREMENT,
                    auth_endpoint VARCHAR(1000) NOT NULL,
                    app_id VARCHAR(255) NOT NULL,
                    app_secret VARCHAR(255) NOT NULL,
                    bitable_app_token VARCHAR(255) DEFAULT NULL,
                    bitable_table_id VARCHAR(255) DEFAULT NULL,
                    tenant_access_token VARCHAR(1024) DEFAULT NULL,
                    token_expires_at DATETIME DEFAULT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        } else {
            if (!$this->db->field_exists('bitable_app_token', $this->lark_table)) {
                $this->db->query("ALTER TABLE {$this->lark_table} ADD COLUMN bitable_app_token VARCHAR(255) DEFAULT NULL AFTER app_secret;");
            }
            if (!$this->db->field_exists('bitable_table_id', $this->lark_table)) {
                $this->db->query("ALTER TABLE {$this->lark_table} ADD COLUMN bitable_table_id VARCHAR(255) DEFAULT NULL AFTER bitable_app_token;");
            }
        }

        // Job locks table
        if (!$this->db->table_exists($this->locks_table)) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS {$this->locks_table} (
                    job_name VARCHAR(100) NOT NULL,
                    locked_until DATETIME NOT NULL,
                    PRIMARY KEY (job_name)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }

    /* ============== TOKEN CRUD (dịch vụ MBO) ============== */
    public function get_token()
    {
        return $this->db->get($this->token_table)->row();
    }

    /* ============== LƯU TOKEN ============== */
    public function save_token($payload)
    {
        $exists = $this->get_token();
        $data = [
            'base_url'     => $payload['base_url'] ?? '',
            'service_name' => $payload['service_name'] ?? null,
            'auth_user'    => $payload['auth_user'] ?? null,
            'auth_key'     => $payload['auth_key'] ?? null,
        ];
        if ($exists) {
            $this->db->update($this->token_table, $data, ['id' => $exists->id]);
            return $exists->id;
        } else {
            $this->db->insert($this->token_table, $data);
            return $this->db->insert_id();
        }
    }

    /* ============== LARK CONFIG ============== */
    public function get_lark_config()
    {
        return $this->db->get($this->lark_table)->row();
    }

    /* ============== UPDATE LARK ============== */
    public function upsert_lark_and_fetch_token(array $payload)
    {
        $auth_endpoint = trim($payload['lark_auth_endpoint'] ?? $payload['auth_endpoint'] ?? '');
        $app_id        = trim($payload['lark_app_id'] ?? $payload['app_id'] ?? '');
        $app_secret    = trim($payload['lark_app_secret'] ?? $payload['app_secret'] ?? '');

        $bitable_app_token = trim($payload['bitable_app_token'] ?? '');
        $bitable_table_id  = trim($payload['bitable_table_id']  ?? '');

        if ($auth_endpoint === '' || $app_id === '' || $app_secret === '') {
            return ['success' => false, 'message' => 'Thiếu AuthEndpoint/AppId/AppSecret'];
        }

        $res = $this->call_api($auth_endpoint, [
            'app_id'     => $app_id,
            'app_secret' => $app_secret,
        ]);
        if (!$res['success']) {
            return ['success' => false, 'message' => $res['error'] ?: ('HTTP ' . $res['http_code']), 'raw' => $res['response'] ?? null];
        }

        $decoded = json_decode($res['response'], true);
        $code   = $decoded['code'] ?? $decoded['Code'] ?? null;
        $token  = $decoded['tenant_access_token'] ?? $decoded['tenantAccessToken'] ?? null;
        $expire = $decoded['expire'] ?? $decoded['ExpiresIn'] ?? null;
        if ($code !== 0 || empty($token)) {
            return ['success' => false, 'message' => 'Lark API error: ' . ($decoded['msg'] ?? 'unknown'), 'raw' => $decoded];
        }

        $expires_at = is_numeric($expire) ? date('Y-m-d H:i:s', time() + (int)$expire) : null;

        // Lưu DB
        $this->db->trans_begin();

        $exists = $this->get_lark_config();
        $data   = [
            'auth_endpoint'       => $auth_endpoint,
            'app_id'              => $app_id,
            'app_secret'          => $app_secret,
            'tenant_access_token' => $token,
            'token_expires_at'    => $expires_at,
        ];
        if ($bitable_app_token !== '') $data['bitable_app_token'] = $bitable_app_token;
        if ($bitable_table_id  !== '') $data['bitable_table_id']  = $bitable_table_id;

        if ($exists) {
            $this->db->update($this->lark_table, $data, ['id' => $exists->id]);
            $id = $exists->id;
        } else {
            $this->db->insert($this->lark_table, $data);
            $id = $this->db->insert_id();
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return ['success' => false, 'message' => 'DB transaction failed when saving Lark config'];
        }
        $this->db->trans_commit();

        return ['success' => true, 'id' => $id, 'token' => $token, 'expires_at' => $expires_at, 'raw' => $decoded];
    }

    /* ============== HÀM LẤY ID VÀ ID TABLE LARK ============== */
    public function get_bitable_target(): array
    {
        $cfg = $this->get_lark_config();
        return [
            'app_token' => $cfg->bitable_app_token ?? null,
            'table_id'  => $cfg->bitable_table_id  ?? null,
        ];
    }

    /* ============== LẤY LARK TENANT TOKEN ============== */
    public function get_lark_tenant_token($force_refresh = false)
    {
        $cfg = $this->get_lark_config();
        if (!$cfg || empty($cfg->auth_endpoint) || empty($cfg->app_id) || empty($cfg->app_secret)) {
            return ['success' => false, 'message' => 'Lark config is missing', 'token' => null, 'expires_at' => null, 'raw' => null];
        }

        if (!$force_refresh && !empty($cfg->tenant_access_token) && !empty($cfg->token_expires_at)) {
            if (strtotime($cfg->token_expires_at) > time() + 60) {
                return ['success' => true, 'token' => $cfg->tenant_access_token, 'expires_at' => $cfg->token_expires_at, 'message' => 'cache', 'raw' => null];
            }
        }

        $res = $this->call_api($cfg->auth_endpoint, ['app_id' => $cfg->app_id, 'app_secret' => $cfg->app_secret]);
        if (!$res['success']) {
            return ['success' => false, 'message' => $res['error'] ?: ('HTTP ' . $res['http_code']), 'token' => null, 'expires_at' => null, 'raw' => $res['response'] ?? null];
        }

        $decoded = json_decode($res['response'], true);
        $code   = $decoded['code'] ?? $decoded['Code'] ?? null;
        $token  = $decoded['tenant_access_token'] ?? $decoded['tenantAccessToken'] ?? null;
        $expire = $decoded['expire'] ?? $decoded['ExpiresIn'] ?? null;
        if ($code !== 0 || empty($token)) {
            return ['success' => false, 'message' => 'Lark error: ' . ($decoded['msg'] ?? 'unknown'), 'token' => null, 'expires_at' => null, 'raw' => $decoded];
        }

        $expires_at = is_numeric($expire) ? date('Y-m-d H:i:s', time() + (int)$expire) : null;
        $this->db->update($this->lark_table, ['tenant_access_token' => $token, 'token_expires_at' => $expires_at], ['id' => $cfg->id]);

        return ['success' => true, 'token' => $token, 'expires_at' => $expires_at, 'message' => 'refreshed', 'raw' => $decoded];
    }

    /* ======= THÊM: log tiện dụng cho controller ======= */
    public function log_event(string $type, string $status = 'success', int $count = 0, ?string $message = null)
    {
        return $this->insert_sync_log($type, $count, $status, $message);
    }

    
    //Đảm bảo có tenant token (force nếu sắp hết hạn), đồng thời ghi log 'lark_token'
    public function ensure_lark_token_logged(bool $force = false): array
    {
        $cfg = $this->get_lark_config();
        if (!$cfg) {
            $this->log_event('lark_token', 'failed', 0, 'Missing Lark config');
            return ['success' => false, 'message' => 'Missing Lark config'];
        }

        // Nếu không force thì thử xài cache
        $tok = $this->get_lark_tenant_token(false);
        $need = (!$tok['success'] || empty($tok['token']) || empty($tok['expires_at']) || strtotime($tok['expires_at']) <= time() + 60);
        if ($force || $need) {
            $tok = $this->get_lark_tenant_token(true);
        }

        if ($tok['success']) {
            $this->log_event('lark_token', 'success', 0, 'Token ok until ' . $tok['expires_at'] . ' (' . $tok['message'] . ')');
        } else {
            $this->log_event('lark_token', 'failed', 0, $tok['message'] ?? 'refresh failed');
        }
        return $tok;
    }

    /* ============== LOGS ============== */
    public function get_logs($limit = 10)
    {
        return $this->db->order_by('date', 'DESC')->limit((int)$limit)->get($this->logs_table)->result_array();
    }

    /* ============== CẬP NHẬT LOGS LÊN DB ============== */
    protected function insert_sync_log($sync_type, $count, $status = 'success', $message = null)
    {
        $row = [
            'sync_type'      => $sync_type,
            'records_synced' => (int)$count,
            'date'           => date('Y-m-d H:i:s'),
            'status'         => $status,
            'message'        => $message
        ];
        $this->db->insert($this->logs_table, $row);
        return $this->db->insert_id();
    }

    /* ============== HTTP helper ============== */
    protected function call_api($endpoint, $payload)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_UNICODE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Content-Length: ' . strlen($jsonPayload)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['success' => ($response !== false && $code < 400), 'http_code' => $code, 'error' => $err, 'response' => $response];
    }

    /* ============== Đồng bộ về DB ============== */
    public function sync_range($opts = [], string $syncType = 'manual')
    {
        $token = $this->get_token();
        if (!$token) return ['success' => false, 'message' => 'Token not configured'];

        $endpoint  = rtrim($token->base_url, '/');
        $pageSize  = (int)($opts['PageSize'] ?? 200);
        if ($pageSize <= 0) $pageSize = 200;
        $maxPages  = (int)($opts['maxPages'] ?? 0);
        $pageIndex = (int)($opts['PageIndex'] ?? 1);

        $insertedTotal = 0;
        $lastDecoded   = null;

        $firstPayload = [
            "ServiceName" => $token->service_name ?? '',
            "AuthUser"    => $token->auth_user ?? '',
            "AuthKey"     => $token->auth_key ?? '',
            "TypeGet"     => 0,
            "HideFWOut"   => 1,
            "DateStart"   => $opts['DateStart'] ?? date('Y-m-d\T00:00:00', strtotime('-1 day')),
            "DateEnd"     => $opts['DateEnd']   ?? date('Y-m-d\T23:59:59'),
            "IsSort"      => 1,
            "PageIndex"   => $pageIndex,
            "PageSize"    => $pageSize
        ];

        $res = $this->call_api($endpoint, $firstPayload);
        if (!$res['success']) {
            $this->insert_sync_log($syncType, 0, 'failed', $res['error'] ?? 'HTTP ' . $res['http_code']);
            return ['success' => false, 'message' => $res['error'] ?? 'HTTP ' . $res['http_code']];
        }

        $decoded = json_decode($res['response'], true);
        $lastDecoded = $decoded;

        $total = (int)($decoded['total'] ?? $decoded['Total'] ?? 0);
        $items = $decoded['data'] ?? $decoded['Data'] ?? (is_array($decoded) ? $decoded : []);
        if (empty($items) && $total === 0) {
            $this->insert_sync_log($syncType, 0, 'success', 'No records to sync');
            return ['success' => true, 'inserted' => 0, 'pages' => 0, 'raw' => $lastDecoded];
        }

        $totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
        if ($maxPages > 0) $totalPages = min($totalPages, $maxPages);

        $processItems = function ($items) use (&$insertedTotal) {
            foreach ($items as $it) {
                $callKey        = $it['key'] ?? $it['CallId'] ?? null;
                $callDate       = $it['callDate'] ?? $it['CallTime'] ?? null;
                $callerNumber   = $it['callerNumber'] ?? $it['CallingNumber'] ?? null;
                $receiveNumber  = $it['receiveNumber'] ?? $it['CalledNumber'] ?? null;
                $status         = $it['status'] ?? $it['CallResult'] ?? null;
                $totalCallTime  = (int)($it['totalCallTime'] ?? $it['TotalDuration'] ?? 0);
                $realCallTime   = (int)($it['realCallTime'] ?? $it['Duration'] ?? 0);
                $linkFile       = $it['linkFile'] ?? $it['RecordingUrl'] ?? null;
                $typeCall       = $it['typecall'] ?? $it['TypeCall'] ?? null;
                $gsmPort        = $it['gsmPort'] ?? null;
                $callerName     = $it['callerName'] ?? null;
                $headNumber     = $it['head_number'] ?? $it['headNumber'] ?? null;

                $callDateConv = $callDate ? date('Y-m-d H:i:s', strtotime($callDate)) : null;

                $src = implode('|', [
                    $callKey,
                    $callDateConv,
                    $callerNumber,
                    $callerName,
                    $headNumber,
                    $receiveNumber,
                    $status,
                    $totalCallTime,
                    $realCallTime,
                    $linkFile,
                    $gsmPort,
                    $typeCall
                ]);
                $uniqueKey = md5($src);

                $exists = $this->db->get_where($this->history_table, ['unique_key' => $uniqueKey])->row();
                if ($exists) continue;

                $row = [
                    'unique_key'      => $uniqueKey,
                    'call_key'        => $callKey,
                    'call_date'       => $callDateConv,
                    'caller_number'   => $callerNumber,
                    'caller_name'     => $callerName,
                    'head_number'     => $headNumber,
                    'receive_number'  => $receiveNumber,
                    'status'          => $status,
                    'total_call_time' => $totalCallTime,
                    'real_call_time'  => $realCallTime,
                    'link_file'       => $linkFile,
                    'gsm_port'        => $gsmPort,
                    'type_call'       => $typeCall,
                    'raw_response'    => json_encode($it, JSON_UNESCAPED_UNICODE)
                ];

                $this->db->insert($this->history_table, $row);
                if ($this->db->insert_id()) $insertedTotal++;
            }
        };

        // page 1
        $processItems($items);

        // remaining pages
        for ($p = $pageIndex + 1; $p <= $totalPages; $p++) {
            usleep(100000);

            $payload = [
                "ServiceName" => $token->service_name ?? '',
                "AuthUser"    => $token->auth_user ?? '',
                "AuthKey"     => $token->auth_key ?? '',
                "TypeGet"     => 0,
                "HideFWOut"   => 1,
                "DateStart"   => $opts['DateStart'] ?? date('Y-m-d\T00:00:00', strtotime('-1 day')),
                "DateEnd"     => $opts['DateEnd']   ?? date('Y-m-d\T23:59:59'),
                "IsSort"      => 1,
                "PageIndex"   => $p,
                "PageSize"    => $pageSize
            ];

            $attempt = 0;
            $maxAttempt = 2;
            $pageRes = null;
            while ($attempt <= $maxAttempt) {
                $attempt++;
                $pageRes = $this->call_api($endpoint, $payload);
                if ($pageRes['success']) break;
                sleep(1);
            }

            if (!$pageRes['success']) {
                $this->insert_sync_log($syncType, $insertedTotal, 'failed', 'Failed page ' . $p . ' - ' . ($pageRes['error'] ?? ('HTTP ' . $pageRes['http_code'])));
                return ['success' => false, 'message' => 'Failed to fetch page ' . $p];
            }

            $decodedPage = json_decode($pageRes['response'], true);
            $lastDecoded = $decodedPage;
            $pageItems = $decodedPage['data'] ?? $decodedPage['Data'] ?? (is_array($decodedPage) ? $decodedPage : []);
            if (empty($pageItems)) break;

            $processItems($pageItems);
        }

        $this->insert_sync_log($syncType, $insertedTotal, 'success', 'Đồng bộ hoàn tất');

        return ['success' => true, 'inserted' => $insertedTotal, 'pages' => ($total > 0 ? (int)ceil($total / $pageSize) : 1), 'raw' => $lastDecoded];
    }

    /* ============== Sync + đẩy Lark ngay ============== */
    public function sync_range_and_push_to_lark(array $opts, array $larkOpts, string $syncType = 'manual')
    {
        $larkCfg = $this->get_lark_config();
        if (!$larkCfg) return ['success' => false, 'message' => 'Chưa cấu hình Lark'];

        $tok = $this->get_lark_tenant_token(false);
        if (!$tok['success']) {
            $tok = $this->get_lark_tenant_token(true);
            if (!$tok['success']) return ['success' => false, 'message' => 'Không lấy được tenant token Lark: ' . $tok['message']];
        }
        $tenantToken = $tok['token'];

        $appToken   = $larkOpts['app_token']  ?? '';
        $tableId    = $larkOpts['table_id']   ?? '';
        $fieldMap   = $larkOpts['field_map']  ?? [];
        $fieldTypes = $larkOpts['field_types'] ?? [];
        $batchSize  = (int)($larkOpts['batch_size'] ?? 120);
        $retryCfg   = $larkOpts['retry'] ?? ['times' => 3, 'sleep' => 2];
        $dtFormat   = $larkOpts['datetime_format'] ?? 'c';
        if (!$appToken || !$tableId || empty($fieldMap)) return ['success' => false, 'message' => 'Thiếu app_token/table_id/field_map cho Lark'];
        if ($batchSize <= 0) $batchSize = 120;

        $token = $this->get_token();
        if (!$token) return ['success' => false, 'message' => 'Token MBO chưa cấu hình'];

        $endpoint  = rtrim($token->base_url, '/');
        $pageSize  = (int)($opts['PageSize'] ?? 200);
        if ($pageSize <= 0) $pageSize = 200;
        $maxPages  = (int)($opts['maxPages'] ?? 0);
        $pageIndex = (int)($opts['PageIndex'] ?? 1);

        $insertedTotal = 0;
        $lastDecoded   = null;
        $larkPayloadRecords = [];

        $firstPayload = [
            "ServiceName" => $token->service_name ?? '',
            "AuthUser"    => $token->auth_user ?? '',
            "AuthKey"     => $token->auth_key ?? '',
            "TypeGet"     => 0,
            "HideFWOut"   => 1,
            "DateStart"   => $opts['DateStart'] ?? date('Y-m-d\T00:00:00', strtotime('-1 day')),
            "DateEnd"     => $opts['DateEnd']   ?? date('Y-m-d\T23:59:59'),
            "IsSort"      => 1,
            "PageIndex"   => $pageIndex,
            "PageSize"    => $pageSize
        ];
        $res = $this->call_api($endpoint, $firstPayload);
        if (!$res['success']) {
            $this->insert_sync_log($syncType, 0, 'failed', $res['error'] ?? 'HTTP ' . $res['http_code']);
            return ['success' => false, 'message' => $res['error'] ?? 'HTTP ' . $res['http_code']];
        }
        $decoded = json_decode($res['response'], true);
        $lastDecoded = $decoded;
        $total = (int)($decoded['total'] ?? $decoded['Total'] ?? 0);
        $items = $decoded['data'] ?? $decoded['Data'] ?? (is_array($decoded) ? $decoded : []);
        if (empty($items) && $total === 0) {
            $this->insert_sync_log($syncType, 0, 'success', 'No records to sync');
            return ['success' => true, 'inserted' => 0, 'pages' => 0, 'pushed' => 0, 'raw' => $lastDecoded];
        }
        $totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
        if ($maxPages > 0) $totalPages = min($totalPages, $maxPages);

        $map_db_row_to_lark_fields = function (array $dbRow) use ($fieldMap, $dtFormat, $fieldTypes) {
            $fields = [];
            foreach ($fieldMap as $bitCol => $dbCol) {
                $val = $dbRow[$dbCol] ?? null;
                if ($val === null || $val === '') continue;

                $type = $fieldTypes[$bitCol] ?? 'text';
                switch ($type) {
                    case 'datetime_ms':
                        if (is_numeric($val)) {
                            $val = (strlen((string)$val) >= 13) ? (int)$val : ((int)$val * 1000);
                        } else {
                            $ts = strtotime((string)$val);
                            if ($ts) $val = $ts * 1000;
                            else continue 2;
                        }
                        break;
                    case 'number':
                        if (is_numeric($val)) $val = 0 + $val;
                        else continue 2;
                        break;
                    case 'text':
                    default:
                        if (is_string($val) && preg_match('/^\d{4}-\d{2}-\d{2} /', $val)) {
                            $ts = strtotime($val);
                            if ($ts) $val = date($dtFormat, $ts);
                        }
                        if (is_bool($val))   $val = $val ? 'true' : 'false';
                        if (is_scalar($val)) $val = (string)$val;
                        if (!is_string($val)) continue 2;
                        break;
                }
                $fields[$bitCol] = $val;
            }
            return $fields;
        };

        $processItems = function ($items) use (&$insertedTotal, &$larkPayloadRecords, $map_db_row_to_lark_fields) {
            foreach ($items as $it) {
                $callKey        = $it['key'] ?? $it['CallId'] ?? null;
                $callDate       = $it['callDate'] ?? $it['CallTime'] ?? null;
                $callerNumber   = $it['callerNumber'] ?? $it['CallingNumber'] ?? null;
                $receiveNumber  = $it['receiveNumber'] ?? $it['CalledNumber'] ?? null;
                $status         = $it['status'] ?? $it['CallResult'] ?? null;
                $totalCallTime  = (int)($it['totalCallTime'] ?? $it['TotalDuration'] ?? 0);
                $realCallTime   = (int)($it['realCallTime'] ?? $it['Duration'] ?? 0);
                $linkFile       = $it['linkFile'] ?? $it['RecordingUrl'] ?? null;
                $typeCall       = $it['typecall'] ?? $it['TypeCall'] ?? null;
                $gsmPort        = $it['gsmPort'] ?? null;
                $callerName     = $it['callerName'] ?? null;
                $headNumber     = $it['head_number'] ?? $it['headNumber'] ?? null;

                $callDateConv = $callDate ? date('Y-m-d H:i:s', strtotime($callDate)) : null;

                $src = implode('|', [
                    $callKey,
                    $callDateConv,
                    $callerNumber,
                    $callerName,
                    $headNumber,
                    $receiveNumber,
                    $status,
                    $totalCallTime,
                    $realCallTime,
                    $linkFile,
                    $gsmPort,
                    $typeCall
                ]);
                $uniqueKey = md5($src);

                $exists = $this->db->get_where($this->history_table, ['unique_key' => $uniqueKey])->row();
                if ($exists) continue;

                $row = [
                    'unique_key'      => $uniqueKey,
                    'call_key'        => $callKey,
                    'call_date'       => $callDateConv,
                    'caller_number'   => $callerNumber,
                    'caller_name'     => $callerName,
                    'head_number'     => $headNumber,
                    'receive_number'  => $receiveNumber,
                    'status'          => $status,
                    'total_call_time' => $totalCallTime,
                    'real_call_time'  => $realCallTime,
                    'link_file'       => $linkFile,
                    'gsm_port'        => $gsmPort,
                    'type_call'       => $typeCall,
                    'raw_response'    => json_encode($it, JSON_UNESCAPED_UNICODE)
                ];
                $this->db->insert($this->history_table, $row);
                if ($this->db->insert_id()) {
                    $insertedTotal++;

                    $mapped = $map_db_row_to_lark_fields($row);
                    if (!empty($mapped)) {
                        $mapped = $this->post_map_adjust_for_bitable($mapped, $row);
                    }
                    if (!empty($mapped)) {
                        $larkPayloadRecords[] = ['fields' => $mapped];
                    }
                }
            }
        };

        $processItems($items);

        for ($p = $pageIndex + 1; $p <= $totalPages; $p++) {
            usleep(100000);
            $payload = [
                "ServiceName" => $token->service_name ?? '',
                "AuthUser"    => $token->auth_user ?? '',
                "AuthKey"     => $token->auth_key ?? '',
                "TypeGet"     => 0,
                "HideFWOut"   => 1,
                "DateStart"   => $opts['DateStart'] ?? date('Y-m-d\T00:00:00', strtotime('-1 day')),
                "DateEnd"     => $opts['DateEnd']   ?? date('Y-m-d\T23:59:59'),
                "IsSort"      => 1,
                "PageIndex"   => $p,
                "PageSize"    => $pageSize
            ];

            $attempt = 0;
            $maxAttempt = 2;
            $pageRes = null;
            while ($attempt <= $maxAttempt) {
                $attempt++;
                $pageRes = $this->call_api($endpoint, $payload);
                if ($pageRes['success']) break;
                sleep(1);
            }
            if (!$pageRes['success']) {
                $this->insert_sync_log($syncType, $insertedTotal, 'failed', 'Failed page ' . $p . ' - ' . ($pageRes['error'] ?? ('HTTP ' . $pageRes['http_code'])));
                return ['success' => false, 'message' => 'Failed to fetch page ' . $p];
            }

            $decodedPage = json_decode($pageRes['response'], true);
            $lastDecoded = $decodedPage;
            $pageItems = $decodedPage['data'] ?? $decodedPage['Data'] ?? (is_array($decodedPage) ? $decodedPage : []);
            if (empty($pageItems)) break;

            $processItems($pageItems);
        }

        $pushed = 0;
        if (!empty($larkPayloadRecords)) {
            $pushRes = $this->lark_batch_create_records($appToken, $tableId, $tenantToken, $larkPayloadRecords, $batchSize, $retryCfg);
            if (!$pushRes['success']) {
                $this->insert_sync_log($syncType, $insertedTotal, 'failed', 'Push to Lark failed: ' . $pushRes['message'] . ' | resp=' . json_encode($pushRes['resp'] ?? null, JSON_UNESCAPED_UNICODE));
                return [
                    'success'  => false,
                    'message'  => 'Đồng bộ DB xong nhưng đẩy Lark lỗi: ' . $pushRes['message'],
                    'inserted' => $insertedTotal,
                    'pushed'   => $pushRes['pushed'] ?? 0,
                    'raw'      => $lastDecoded,
                    'resp'     => $pushRes['resp'] ?? null,
                ];
            }
            $pushed = $pushRes['pushed'];
        }

        $this->insert_sync_log($syncType, $insertedTotal, 'success', 'Đồng bộ + đẩy Lark hoàn tất');

        return ['success' => true, 'inserted' => $insertedTotal, 'pushed' => $pushed, 'pages' => ($total > 0 ? (int)ceil($total / $pageSize) : 1), 'raw' => $lastDecoded];
    }

    // TẠO CUỘC GỌI TRÊN LARK
    protected function lark_batch_create_records($appToken, $tableId, $tenantToken, array $records, $batchSize = 120, array $retryCfg = ['times' => 3, 'sleep' => 2])
    {
        $url = "https://open.larksuite.com/open-apis/bitable/v1/apps/{$appToken}/tables/{$tableId}/records/batch_create";
        $pushed = 0;
        $chunks = array_chunk($records, $batchSize);

        foreach ($chunks as $chunk) {
            $payload = ['records' => $chunk];
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE);

            $attempts = 0;
            $maxTimes = (int)($retryCfg['times'] ?? 3);
            $sleepSec = (int)($retryCfg['sleep'] ?? 2);

            do {
                $attempts++;
                $res = $this->lark_post_json($url, $json, $tenantToken);

                if (!$res['success']) {
                    $http = $res['http_code'] ?? 0;
                    if (in_array($http, [429, 500, 502, 503, 504]) && $attempts <= $maxTimes) {
                        sleep($sleepSec);
                        continue;
                    }
                    return ['success' => false, 'message' => 'HTTP ' . $http . ' - ' . ($res['error'] ?? ''), 'pushed' => $pushed, 'resp' => $res['response'] ?? null];
                }

                $resp = json_decode($res['response'], true);
                $code = $resp['code'] ?? null;

                if ($code === 0) {
                    $created = isset($resp['data']['records']) && is_array($resp['data']['records'])
                        ? count($resp['data']['records']) : count($chunk);
                    $pushed += $created;
                    break;
                }

                if ($attempts > $maxTimes) {
                    $msg = $resp['msg'] ?? 'unknown';
                    return ['success' => false, 'message' => 'Lark error: ' . $msg, 'pushed' => $pushed, 'resp' => $resp];
                }

                sleep($sleepSec);
            } while ($attempts <= $maxTimes);
        }

        return ['success' => true, 'pushed' => $pushed];
    }

    // TẠO JSON QUA LARK
    protected function lark_post_json($url, $jsonBody, $tenantToken)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $tenantToken,
            'Content-Length: ' . strlen($jsonBody)
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);

        $response = curl_exec($ch);
        $err      = curl_error($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['success' => ($response !== false && $code < 400), 'http_code' => $code, 'error' => $err, 'response' => $response];
    }

    /* ===== Helpers biến đổi ===== */
    protected function seconds_to_hms($sec)
    {
        $sec = is_numeric($sec) ? (int)$sec : 0;
        $h = floor($sec / 3600);
        $m = floor(($sec % 3600) / 60);
        $s = $sec % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    // TRANSLATE STATUS
    protected function status_vi($raw)
    {
        if ($raw === null) return '';
        $v = trim(mb_strtoupper((string)$raw, 'UTF-8'));
        $map = [
            'ANSWERED'   => 'Thành công',
            'NO ANSWER'  => 'Không bắt máy',
            'NOANSWER'   => 'Không bắt máy',
            'BUSY'       => 'Máy bận',
            'FAILED'     => 'Không thể kết nối',
            'CANCEL'     => 'Huỷ cuộc gọi',
        ];
        return $map[$v] ?? ($raw ?: '');
    }

    // MAP BẢNG CHO CỤM THỜI GIAN ĐƯỢC ĐƯA LÊN VIEW
    protected function post_map_adjust_for_bitable(array $mapped, array $row)
    {
        // Thời gian => Y-m-d H:i:s
        if (isset($mapped['Thời gian'])) {
            $src = $row['call_date'] ?? $mapped['Thời gian'];
            $ts  = is_numeric($src) ? (int)$src : strtotime((string)$src);
            if ($ts) {
                $mapped['Thời gian'] = date('Y-m-d H:i:s', $ts);
            } elseif (is_string($src) && preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $src, $m)) {
                $mapped['Thời gian'] = $m[0];
            }
        }

        // Hướng cuộc gọi
        if (isset($mapped['Hướng cuộc gọi'])) {
            $v  = mb_strtolower(trim((string)$mapped['Hướng cuộc gọi']), 'UTF-8');
            $tc = isset($row['type_call']) ? mb_strtolower((string)$row['type_call'], 'UTF-8') : '';
            if ($v === 'outbound' || $tc === 'outbound') $mapped['Hướng cuộc gọi'] = 'Gọi ra';
            elseif ($v === 'inbound' || $tc === 'inbound') $mapped['Hướng cuộc gọi'] = 'Gọi vào';
        }

        // Trạng thái -> tiếng Việt
        if (isset($mapped['Trạng thái'])) {
            $mapped['Trạng thái'] = $this->status_vi($row['status'] ?? $mapped['Trạng thái']);
        }

        // Thời lượng -> HH:MM:SS
        if (isset($mapped['Thời gian đàm thoại'])) {
            $sec = $row['real_call_time'] ?? $mapped['Thời gian đàm thoại'];
            $mapped['Thời gian đàm thoại'] = $this->seconds_to_hms($sec);
        }
        if (isset($mapped['Tổng thời gian gọi'])) {
            $sec = $row['total_call_time'] ?? $mapped['Tổng thời gian gọi'];
            $mapped['Tổng thời gian gọi'] = $this->seconds_to_hms($sec);
        }

        return $mapped;
    }

    /* ===== Job lock helpers ===== */
    public function acquire_job_lock($jobName, $ttl = 300): bool
    {
        $now   = date('Y-m-d H:i:s');
        $until = date('Y-m-d H:i:s', time() + (int)$ttl);
        $sql = "
            INSERT INTO {$this->locks_table} (job_name, locked_until)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE locked_until = IF(locked_until < ?, VALUES(locked_until), locked_until)
        ";
        $this->db->query($sql, [$jobName, $until, $now]);

        $row = $this->db->get_where($this->locks_table, ['job_name' => $jobName])->row_array();
        if (!$row) return false;
        return (strtotime($row['locked_until']) >= strtotime($until) - 1);
    }

    // CRON JOB CHO LOCAL
    public function release_job_lock($jobName): void
    {
        $this->db->delete($this->locks_table, ['job_name' => $jobName]);
    }

    public function get_job_lock($jobName): array
    {
        $row = $this->db->get_where($this->locks_table, ['job_name' => $jobName])->row_array();
        if (!$row) return ['job_name' => $jobName, 'locked' => false, 'locked_until' => null, 'seconds_left' => 0];
        $left = max(0, strtotime($row['locked_until']) - time());
        return ['job_name' => $jobName, 'locked' => ($left > 0), 'locked_until' => $row['locked_until'], 'seconds_left' => $left];
    }

    public function get_locks_status(array $names): array
    {
        $out = [];
        foreach ($names as $n) $out[$n] = $this->get_job_lock($n);
        return $out;
    }
}
