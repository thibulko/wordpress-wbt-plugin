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

            //$this->refresh();
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

        $wp_tasks = $this->container()->get('config')->getOption('tasks');

        $cnt = 0;

        foreach ($languages as $language) {
            $data = $this->container()->get('client')->remote($url . $language['id'] . '?api_key=' . $this->getApiKey());

            if( is_wp_error( $data ) ){
                $this->log($data->get_error_code() . $data->get_error_message());
                break;
            }

            if (!empty($data['data']['data'])) {
                foreach ($data['data']['data'] as $val) {
                    if (!empty($wp_tasks[$val['name']]) && !empty($val['translation'])) {
                        $task = $wp_tasks[$val['name']];

                        switch ($task['type']) {
                            case 'post_title': {
                                $item = $this->getPost($task['id']);
                                if (isset($item['post_title'][$language['code']])) {
                                    $item['post_title'][$language['code']] = $val['translation']['value'];
                                    $this->updatePost($item);
                                    $cnt++;
                                }
                            } break;

                            case 'post_content': {
                                $item = $this->getPost($task['id']);
                                if (isset($item['post_content'][$language['code']])) {
                                    $item['post_content'][$language['code']] = $val['translation']['value'];
                                    $this->updatePost($item);
                                    $cnt++;
                                }
                            } break;

                            case 'term_name': {
                                $item = $this->getTerm($task['id']);
                                if (isset($item['name'][$language['code']])) {
                                    $item['name'][$language['code']] = $val['translation']['value'];
                                    $this->updateTerm($item);
                                    $cnt++;
                                }
                            } break;
                        }
                        //$this->dump($post);

                        //$this->updatePost($post);
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

        // Posts
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

        // Terms
        $terms = $this->getTerms();

        if (!empty($terms)) {
            foreach ($terms as $term_id => $term) {
                if (!empty($term[$default_language])) {
                    $task = array(
                        'id' => $term_id,
                        'type' => 'term_name',
                    );

                    $key = md5(serialize($task));

                    $this->container()->get('client')->remote($url, array(
                        'method' => 'POST',
                        'body' => array(
                            'name' => $key,
                            'value' => $term[$default_language],
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