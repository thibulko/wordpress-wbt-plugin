<?php

class WbtApi extends WbtAbstract
{
    protected $api_key;

    public function init()
    {
        $project = $this->container()->get('client')->remote('/');

        if (!empty($project['data']['id'])) {
            $config = $this->container()->get('config');
            $config->setOption('default_language', isset($project['data']['language']) ? $project['data']['language'] : []);
            $config->setOption('languages', isset($project['data']['languages']) ? $project['data']['languages'] : []);
            $config->setOption('updated_at', time());
            $config->setOption('abstractions', []);

            //$this->refresh();
        }
    }

    public function uploadLangFile($filename, $group = null)
    {
        //$filename = '/www/wordpress/wp-content/themes/activello/languages/ru_RU.po';
        //$filename = '/www/wordpress/wp-content/themes/parallel/languages/parallel.pot';
        //$group = array('name' => 'testGroup', 'parent' => array('name' => 'parentGroup'));

        $params = array();
        $client = $this->container()->get('client');

        $boundary = wp_generate_password(24);

        if ($group) {
            $params['group'] = $group;
        }

        $headers = array(
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            'Cache-Control' => 'no-cache',
        );

        $payload = $this->create_payload($boundary, $filename, $params);

        $body = $client->remote('/abstractions/upload', array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => $payload,
        ));

        return !empty($body['data']) ? count($body['data']) : 0;
    }

    protected function create_payload($boundary, $filename, $params = array())
    {
        $payload = '';

        if (!empty($params)) {
            $params = WbtHttpClient::normalize_multipart_params($params) ;

            foreach ($params as $param) {
                $payload .= '--' . $boundary;
                $payload .= "\r\n";
                $payload .= 'Content-Disposition: form-data; name="' . $param['name'] . '"';
                $payload .= "\r\n\r\n";
                $payload .= $param['contents'];
                $payload .= "\r\n";
            }
        }

        if ($filename) {
            $payload .= '--' . $boundary;
            $payload .= "\r\n";
            $payload .= 'Content-Disposition: form-data; name="file"; filename="' . basename($filename);
            $payload .= "\r\n";
            $payload .= 'Content-Type: text/plain';
            $payload .= "\r\n\r\n";
            $payload .= file_get_contents($filename);
            $payload .= "\r\n";

        }

        $payload .= '--' . $boundary . '--';

        return $payload;
    }

    public function export()
    {
        $themes = $this->themesWithLanguages();

        $n = 0;

        if (!empty($themes)) {
            foreach ($themes as $theme) {
                $group = $this->theme_group($theme['id']);
                $n += $this->uploadLangFile($theme['lang_file'], $group);
            }
        }

        $n += $this->createAbstractions();

        return $n;
    }

    public function import()
    {
        return $this->getTranslations();
    }

    public function theme_group($theme)
    {
        $parentGroup = array(
            'name' => get_theme_roots(),
        );

        $group = array(
            'name' => $theme,
            'parent' => $parentGroup,
        );

        return $group;
    }

    public function themesWithLanguages()
    {
        $result = array();

        $default_language = $this->container()->get('config')->getOption('default_language');
        if (!empty($default_language['code'])) {
            $language_code = strtolower($default_language['code']) . '_' . strtoupper($default_language['code']);
        }

        $themes = wp_get_themes();

        foreach ($themes as $key => $theme) {
            $language_path = $theme->theme_root . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'languages';

            if (!empty($language_code) && file_exists($language_path . DIRECTORY_SEPARATOR . $language_code . '.po')) {
                $lang_file = $language_path . DIRECTORY_SEPARATOR . $language_code . '.po';
            } elseif (file_exists($language_path . DIRECTORY_SEPARATOR . $key . '.pot')) {
                $lang_file = $language_path . DIRECTORY_SEPARATOR . $key . '.pot';
            } else {
                $lang_file = null;
            }

            if (!empty($lang_file)) {
                $result[$key] = array(
                    'id' => $key,
                    'name' => $theme,
                    'lang_file' => $lang_file,
                );
            }
        }

        return $result;
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
        $wp_tasks = $this->container()->get('config')->getOption('abstractions');

        $cnt = 0;

        $data = $this->container()->get('client')->remote('/translations');

        if (!empty($data['data'])) {
            $abstractions = [];
            foreach ($data['data'] as $d) {
                $abstractions[$d['abstract_name']][$d['language']] = $d['original_value'];
                
                if (!empty($d['translations'])) {
                    foreach ($d['translations'] as $trans) {
                        $abstractions[$d['abstract_name']][$trans['language']] = $trans['value'];
                    }
                }
            }
            
            if (!empty($abstractions)) {
                foreach ($abstractions as $k => $languages) {
                    if (!empty($wp_tasks[$k])) {
                        $task = $wp_tasks[$k];
    
                        foreach($languages as $language => $translation) {
                            switch ($task['type']) {
                                case 'post_title':
                                    $item = $this->getPost($task['id']);
                                    if (isset($item['post_title'][$language])) {
                                        $item['post_title'][$language] = $translation;
                                        $this->updatePost($item);
                                        $cnt++;
                                    }
                                    break;
        
                                case 'post_content':
                                    $item = $this->getPost($task['id']);
                                    if (isset($item['post_content'][$language])) {
                                        $item['post_content'][$language] = $translation;
                                        $this->updatePost($item);
                                        $cnt++;
                                    }
                                    break;
        
                                case 'term_name':
                                    $item = $this->getTerm($task['id']);
                                    if (isset($item['name'][$language])) {
                                        $item['name'][$language] = $translation;
                                        $this->updateTerm($item);
                                        $cnt++;
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $cnt;
    }

    public function createAbstractions()
    {
        $url = '/abstractions/create';

        $default_language = $this->container()->get('config')->getOption('default_language');
        $default_language = $default_language['code'];

        $abstractions = $this->container()->get('config')->getOption('abstractions');

        if (empty($abstractions)) {
            $abstractions = array();
            $this->container()->get('config')->setOption('abstractions', $abstractions);
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
    
                    $abstractions[$key] = $task;
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
    
                    $abstractions[$key] = $task;
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
    
                    $abstractions[$key] = $task;
                }
            }
        }

        $this->container()->get('config')->setOption('abstractions', $abstractions);

        return count($abstractions);
    }
}