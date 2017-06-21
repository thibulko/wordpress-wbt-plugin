<?php

class FwtApi extends FwtAbstract
{
    protected $api_key;

    public function init()
    {
        $project = $this->container()->get('client')->remote('project?api_key=' . $this->getApiKey());

        if (!empty($project['data']['id'])) {
            $config = $this->container()->get('config');
            $config->setOption('default_language', isset($project['data']['language']) ? $project['data']['language'] : []);
            $config->setOption('languages', isset($project['data']['languages']) ? $project['data']['languages'] : []);
            $config->setOption('updated_at', time());
            $config->setOption('tasks', []);

            $this->refresh();
        }
    }

    public function export()
    {
        return $this->createTasks();
    }

    public function import()
    {
        return $this->getTranslations();
    }

    public function refresh()
    {
        $posts = $this->getPosts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $this->updatePost($post);
            }
        }
    }

    public function getTranslations()
    {
        $url = 'project/translations/';

        $languages = $this->container()->get('config')->getLanguages();

        $tasks = $this->container()->get('config')->getOption('tasks');

        $cnt = 0;

        foreach ($languages as $language) {
            $data = $this->container()->get('client')->remote($url . $language['id'] . '?api_key=' . $this->getApiKey());

            if( is_wp_error( $data ) ){
                $this->log($data->get_error_code() . $data->get_error_message());
                break;
            }

            if (!empty($data['data']['data'])) {
                foreach ($data['data']['data'] as $val) {
                    if (!empty($tasks[$val['name']]) && !empty($val['translation'])) {
                        $task = $tasks[$val['name']];
                        $post = $this->getPost($task['id']);

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

                        $this->updatePost($post);
                    }
                }
            }
        }

        return $cnt;
    }

    public function createTasks()
    {
        $url = 'project/tasks/create?api_key=' . $this->getApiKey();

        $default_language = $this->container()->get('config')->getOption('default_language');
        $default_language = $default_language['code'];

        $tasks = $this->container()->get('config')->getOption('tasks');

        if (empty($tasks)) {
            $tasks = array();
            $this->container()->get('config')->setOption('tasks', $tasks);
        }

        $posts = $this->getPosts();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                if (!empty($post['post_content'][$default_language])) {
                    $task = array(
                        'id' => $post['ID'],
                        'type' => 'post_content',
                    );

                    $key = md5(serialize($task));

                    $this->container()->get('client')->remote($url, array(
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

                    $this->container()->get('client')->remote($url, array(
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

        $this->container()->get('config')->setOption('tasks', $tasks);

        return count($tasks);
    }

    public function getApiKey()
    {
        if (null === $this->api_key) {
            $this->api_key = $this->container()->get('config')->getOption('api_key');
        }
        return $this->api_key;
    }
}