<?php

/**
 * The api functionality of the plugin.
 */

class Fwt_Api
{
    const FWT_API_URL = 'http://127.0.0.1./api/v1/';

    protected $api_key = '';

    protected $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly8xOTIuMTY4Ljg4LjE0OTo4MDgwL2FwaS92MS9hdXRoL2xvZ2luIiwiaWF0IjoxNDk3MjY3MTYzLCJleHAiOjE1MDI0NTExNjMsIm5iZiI6MTQ5NzI2NzE2MywianRpIjoiTXNIVVljS0VuYlQ5ckV4aiJ9.PCfZX62Tp2Fgj-9_Roe80U0TeqAIxJo1JjBv6CRQTKM',
    ];

    private $config;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $config )
    {
        $this->config = $config;
    }

    public function sync()
    {
        print "OK";
        //$project = $this->api('project');
    }

    public function api($type, $method = 'GET', $params = [])
    {
        $url = rtrim(self::FWT_API_URL, '/') . '/' . $type . '/?api_key=' . $this->get_api_key();
        $body = $this->get_content($url, $method, $params);
        return json_decode($body);
    }

    public function get_content($url, $method = 'GET', $params = [])
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            if (!empty($this->headers)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
            }

            $body = curl_exec($curl);
            curl_close($curl);
        } else {
            $body = file_get_contents($url);
        }

        return $body;
    }

    public function get_api_key()
    {
        return $this->api_key;
    }
}