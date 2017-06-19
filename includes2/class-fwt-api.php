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

    public function init()
    {
        $project = $this->remote_request('project/' . $this->get_api_key());

        if (!empty($project['data']['id'])) {
            $this->config->set_option('default_language', isset($project['data']['language']) ? $project['data']['language'] : []);
            $this->config->set_option('languages', isset($project['data']['languages']) ? $project['data']['languages'] : []);
            $this->config->set_option('updated_at', time());
            $this->config->set_option('tasks', []);

            $this->refresh();
        }
    }

    public function export()
    {
        return $this->create_tasks();
    }

    public function import()
    {
        return $this->get_translations();
    }

    public function refresh()
    {
        $posts = $this->translate->get_posts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $this->translate->update_post($post);
            }
        }
    }

    public function get_translations()
    {
        $url = 'project/' . $this->get_api_key() . '/translations/';

        $languages = $this->config->get_languages();

        $tasks = $this->config->get_option('tasks');

        $cnt = 0;

        foreach ($languages as $language) {
            $data = $this->remote_request($url . $language['id']);

            if( is_wp_error( $data ) ){
                echo $data->get_error_code() . $data->get_error_message();
                break;
            }

            if (!empty($data['data']['data'])) {
                foreach ($data['data']['data'] as $val) {
                    if (!empty($tasks[$val['name']]) && !empty($val['translation'])) {
                        $task = $tasks[$val['name']];
                        $post = $this->translate->get_post($task['id']);

                        if (empty($post)) {
                            continue;
                        }

                        switch ($task['type']) {
                            case 'post_title': {
                                if (isset($post['post_title'][$language['code']])) {
                                    $post['post_title'][$language['code']] = $val['translation']['value'];
                                    $cnt++;
                                }
                            }
                            break;

                            case 'post_content': {
                                if (isset($post['post_content'][$language['code']])) {
                                    $post['post_content'][$language['code']] = $val['translation']['value'];
                                    $cnt++;
                                }
                            }
                            break;
                        }
                        //$this->dump($post);

                        $this->translate->update_post($post);
                    }
                }
            }
        }

        return $cnt;
    }

    public function create_tasks()
    {
        $url = 'project/' . $this->get_api_key() . '/tasks/create';

        $default_language = $this->config->get_option('default_language');
        $default_language = $default_language['code'];

        $tasks = $this->config->get_option('tasks');

        if (empty($tasks)) {
            $tasks = array();
            $this->config->set_option('tasks', $tasks);
        }

        $posts = $this->translate->get_posts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (!empty($post['post_content'][$default_language])) {
                    $task = array(
                        'id' => $post['ID'],
                        'type' => 'post_content',
                    );

                    $key = md5(serialize($task));

                    $this->remote_request($url, array(
                        'method' => 'POST',
                        'body' => array(
                            'name' => $key,
                            'value' => $post['post_content'][$default_language],
                        )
                    ));

                    $tasks[$key] = $task;
                }

                if (!empty($post['post_title'][$default_language])) {
                    $task = array(
                        'id' => $post['ID'],
                        'type' => 'post_title',
                    );

                    $key = md5(serialize($task));

                    $this->remote_request($url, array(
                        'method' => 'POST',
                        'body' => array(
                            'name' => $key,
                            'value' => $post['post_title'][$default_language],
                        )
                    ));

                    $tasks[$key] = $task;
                }
            }
        }

        $this->config->set_option('tasks', $tasks);

        return count($tasks);
    }

    public function remote_request($type, $params = [])
    {
        $url = rtrim(self::API_URL, '/') . '/' . $type;

        $request = wp_remote_request($url, array_merge($this->params, $params));

        $code = wp_remote_retrieve_response_code( $request );
        $mesg = wp_remote_retrieve_response_message( $request );
        $body = json_decode(wp_remote_retrieve_body( $request ), true );

        //$this->dump($params);
        //$this->dump($body);

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
        print "<pre>" . print_r($data, true) . "</pre>";
    }
}