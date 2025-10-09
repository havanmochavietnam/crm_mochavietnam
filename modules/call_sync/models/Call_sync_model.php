<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Call_sync_model extends App_Model
{
    protected $token_table;
    protected $logs_table;
    protected $history_table;

    public function __construct()
    {
        parent::__construct();
        $this->token_table   = db_prefix() . 'call_token';
        $this->logs_table    = db_prefix() . 'call_sync_logs';
        $this->history_table = db_prefix() . 'call_history'; // <-- CHỈNH Ở ĐÂY

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

        // Call history table (mới)
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
    }

    /* TOKEN CRUD */
    public function get_token()
    {
        return $this->db->get($this->token_table)->row();
    }

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

    /* LOGS */
    public function get_logs($limit = 10)
    {
        return $this->db->order_by('date', 'DESC')->limit((int)$limit)->get($this->logs_table)->result_array();
    }

    protected function insert_sync_log($sync_type, $count, $status = 'success', $message = null)
    {
        $row = [
            'sync_type' => $sync_type,
            'records_synced' => (int)$count,
            'date' => date('Y-m-d H:i:s'),
            'status' => $status,
            'message' => $message
        ];
        $this->db->insert($this->logs_table, $row);
        return $this->db->insert_id();
    }

    /* HTTP helper */
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

        return [
            'success' => ($response !== false && $code < 400),
            'http_code' => $code,
            'error' => $err,
            'response' => $response
        ];
    }

    /* Đồng bộ dữ liệu vào bảng call_history */
    public function sync_range($opts = [])
    {
        $token = $this->get_token();
        if (!$token) {
            return ['success' => false, 'message' => 'Token not configured'];
        }

        $endpoint = rtrim($token->base_url, '/');
        $pageSize  = (int)($opts['PageSize'] ?? 200);
        if ($pageSize <= 0) $pageSize = 200;
        $maxPages  = (int)($opts['maxPages'] ?? 0); // 0 => no explicit cap (we'll rely on total)
        $pageIndex = (int)($opts['PageIndex'] ?? 1);

        $insertedTotal = 0;
        $lastDecoded = null;

        // First request: get total
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
            $this->insert_sync_log('manual', 0, 'failed', $res['error'] ?? 'HTTP ' . $res['http_code']);
            return ['success' => false, 'message' => $res['error'] ?? 'HTTP ' . $res['http_code']];
        }

        $decoded = json_decode($res['response'], true);
        $lastDecoded = $decoded;

        // total may be in 'total' or 'Total' or 'TOTAL'
        $total = (int)($decoded['total'] ?? $decoded['Total'] ?? 0);

        // extract items from first page
        $items = $decoded['data'] ?? $decoded['Data'] ?? (is_array($decoded) ? $decoded : []);
        if (empty($items) && $total === 0) {
            // nothing to do
            $this->insert_sync_log('manual', 0, 'success', 'No records to sync');
            return ['success' => true, 'inserted' => 0, 'pages' => 0, 'raw' => $lastDecoded];
        }

        // compute pages from total
        $totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
        if ($maxPages > 0) {
            $totalPages = min($totalPages, $maxPages);
        }

        // Helper to process items array
        $processItems = function ($items) use (&$insertedTotal) {
            foreach ($items as $it) {
                // map fields
                $callKey = $it['key'] ?? $it['CallId'] ?? null;
                $callDate = $it['callDate'] ?? $it['CallTime'] ?? null;
                $callerNumber = $it['callerNumber'] ?? $it['CallingNumber'] ?? null;
                $receiveNumber = $it['receiveNumber'] ?? $it['CalledNumber'] ?? null;
                $status = $it['status'] ?? $it['CallResult'] ?? null;
                $totalCallTime = (int)($it['totalCallTime'] ?? $it['TotalDuration'] ?? 0);
                $realCallTime = (int)($it['realCallTime'] ?? $it['Duration'] ?? 0);
                $linkFile = $it['linkFile'] ?? $it['RecordingUrl'] ?? null;
                $typeCall = $it['typecall'] ?? $it['TypeCall'] ?? null;
                $gsmPort = $it['gsmPort'] ?? null;
                $callerName = $it['callerName'] ?? null;
                $headNumber = $it['headNumber'] ?? null;

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

                // skip if exists
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

        // process first page
        $processItems($items);

        // loop remaining pages (2..totalPages)
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

            // retry logic (2 retries)
            $attempt = 0;
            $maxAttempt = 2;
            $pageRes = null;
            while ($attempt <= $maxAttempt) {
                $attempt++;
                $pageRes = $this->call_api($endpoint, $payload);
                if ($pageRes['success']) break;
                // brief wait then retry
                sleep(1);
            }

            if (!$pageRes['success']) {
                // log partial failure and continue or abort — we will abort and log
                $this->insert_sync_log('manual', $insertedTotal, 'failed', 'Failed page ' . $p . ' - ' . $pageRes['error'] ?? 'HTTP ' . $pageRes['http_code']);
                return ['success' => false, 'message' => 'Failed to fetch page ' . $p];
            }

            $decodedPage = json_decode($pageRes['response'], true);
            $lastDecoded = $decodedPage;
            $pageItems = $decodedPage['data'] ?? $decodedPage['Data'] ?? (is_array($decodedPage) ? $decodedPage : []);
            if (empty($pageItems)) {
                // no more items returned
                break;
            }

            $processItems($pageItems);
        }

        // insert success log
        $this->insert_sync_log('manual', $insertedTotal, 'success', 'Đồng bộ hoàn tất');

        return [
            'success' => true,
            'inserted' => $insertedTotal,
            'pages' => ($total > 0 ? (int)ceil($total / $pageSize) : 1),
            'raw' => $lastDecoded
        ];
    }
}
