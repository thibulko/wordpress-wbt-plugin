<?php

/**
 * The api functionality of the plugin.
 */

class Fwt_Api
{
    const API_URL = 'http://192.168.88.149:8080/api/v2/';

    protected $api_key = '';

    protected $errors;

    protected $params = array();

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
        $project = $this->api_get('project/56');

        if (isset($project['data']['language'])) {
            $this->config->set_option('default_language', $project['data']['language']);
        }

        if (isset($project['data']['languages'])) {
            $this->config->set_option('languages', $project['data']['languages']);
        }

        return true;
    }

    public function api_get($type, $params = [])
    {
        $url = rtrim(self::API_URL, '/') . '/' . $type;
        $request = wp_remote_get($url, array_merge_recursive($this->params, $params));

        if( is_array($request) ) {
            return json_decode($request['body'], true);
        }

        return $request;
    }

    public function get_api_key()
    {
        return $this->api_key;
    }
}