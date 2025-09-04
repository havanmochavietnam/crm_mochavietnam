<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pancake_api
{
    private $api_url;
    private $api_key;
    
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('pancake_sync_model');
        
        $settings = $this->CI->pancake_sync_model->get_settings();
        $this->api_url = rtrim($settings['pancake_url'], '/') . '/api/';
        $this->api_key = $settings['api_key'];
    }
    
    public function get_customers()
    {
        return $this->make_request('GET', 'customers');
    }
    
    public function get_products()
    {
        return $this->make_request('GET', 'products');
    }
    
    public function get_invoices()
    {
        return $this->make_request('GET', 'invoices');
    }
    
    private function make_request($method, $endpoint, $data = [])
    {
        $url = $this->api_url . $endpoint;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ]);
        
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            throw new Exception('CURL Error: ' . curl_error($ch));
        }
        
        curl_close($ch);
        
        if ($http_code != 200) {
            throw new Exception('API Error: ' . $response);
        }
        
        return json_decode($response, true);
    }
}