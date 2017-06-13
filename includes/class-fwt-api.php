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

        $posts = $this->translate->get_posts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (!empty($post['post_content'][$default_language['code']])) {
                    $args = array(
                        'method' => 'POST',
                        'body' => array(
                            'name' => 'post_' . $post['ID'] . '_content',
                            'value' => $post['post_content'][$default_language['code']],
                        )
                    );

                    $this->remote_request('project/' . $this->get_api_key() . '/tasks/create', $args);
                }

                if (!empty($post['post_title'][$default_language['code']])) {
                    $args = array(
                        'method' => 'POST',
                        'data' => array(
                            'name' => 'post_' . $post['ID'] . '_title',
                            'value' => $post['post_title'][$default_language['code']],
                        )
                    );

                    $this->remote_request('project/' . $this->get_api_key() . '/tasks/create', $args);
                }
            }
        }
    }

    public function remote_request($type, $params = [])
    {
        $url = rtrim(self::API_URL, '/') . '/' . $type;

        $request = wp_remote_request($url, array_merge($this->params, $params));

        $code = wp_remote_retrieve_response_code( $request );
        $mesg = wp_remote_retrieve_response_message( $request );
        $body = json_decode(wp_remote_retrieve_body( $request ));

        if ( 200 != $code && !empty( $mesg ) ) {
            return new WP_Error($code, $mesg);
        } elseif ( 200 != $code ) {
            return new WP_Error($code, 'Unknown error!');
        } elseif( !$body ) {
            return new WP_Error('nodata', 'Data not found.');
        } else {
            return $body;
        }
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