<?php

/**
 * The api functionality of the plugin.
 */

class Fwt_Api
{
    const API_URL = 'http://192.168.88.149:8080/api/v2/';

    protected $api_key;

    protected $errors;

    protected $params = array(
        'method' => "GET",
        'user-agent' => 'WEB Translator'
    );

    private $config;

    private $translate;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $config, $translate )
    {
        $this->config = $config;
        $this->translate = $translate;
    }

    public function sync()
    {
        /*$project = $this->remote_get('project');

        if (isset($project['data']['language'])) {
            $this->config->set_option('default_language', $project['data']['language']);
        }

        if (isset($project['data']['languages'])) {
            $this->config->set_option('languages', $project['data']['languages']);
        }

        $this->config->set_option('updated_at', time());*/

        $this->create_tasks();
    }

    public function refresh()
    {
        /*$posts = $this->translate->get_posts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                foreach ($post as $k => $v) {
                    $post[$k] = $this->translate->join($v);
                }

                wp_update_post($post);
            }
        }*/
    }

    public function create_tasks()
    {
        $default_language = $this->config->get_option('default_language');

        $posts = $this->translate->get_posts($default_language['code']);

        if (!empty($posts)) {
$this->dump($posts);

            $args = array(
                'method' => 'POST',
                'body' => array(),
            );

            foreach ($posts as $post) {
                $args['body'] = array(
                    'name' => '',
                    'value' => $post[]
                );

                $this->remote_get('project/' . $this->get_api_key() . '/tasks/create', $args);
            }
        }
    }

    public function remote_get($type, $params = [])
    {
        $url = rtrim(self::API_URL, '/') . '/' . $type . '/' . $this->get_api_key();
        $request = wp_remote_get($url, array_merge_recursive($this->params, $params));

        if( is_array($request) ) {
            return json_decode($request['body'], true);
        }

        return $request;
    }

    public function get_api_key()
    {
        if (null === $this->api_key) {
            $this->api_key = $this->config->get_option('api_key');
        }
        return $this->api_key;
    }

    public function dump($data)
    {
        print "<pre>";
        print_r($data);
        print "</pre>";
    }
}