<?php

class WbtApi extends WbtAbstract
{
    protected $api_key;

    public function init()
    {
        $project = $this->client()->remote('/');

        if (!empty($project['data']['id'])) {
            $config = $this->container()->get('config');
            $config->setOption('default_language', isset($project['data']['language']) ? $project['data']['language'] : []);
            $config->setOption('languages', isset($project['data']['languages']) ? $project['data']['languages'] : []);
            $config->setOption('updated_at', time());
            //$config->setOption('abstractions', []);

            $this->refresh();
        }
    }

    public function export()
    {
        $result = array();

        $types = $this->config()->getOption('types');

        if (!empty($types)) {
            if (in_array(self::TYPE_THEME, $types)) {
                $result[self::TYPE_THEME] = $this->send_theme();
            }

            if (in_array(self::TYPE_POSTS, $types)) {
                $result[self::TYPE_POSTS] = $this->send_content(self::TYPE_POSTS);
            }

            if (in_array(self::TYPE_TERMS, $types)) {
                $result[self::TYPE_TERMS] = $this->send_content(self::TYPE_TERMS);
            }
        }

        return $result;
    }

    public function import()
    {
        $result = array();

        $types = $this->config()->getOption('types');

        if (!empty($types)) {
            if (in_array(self::TYPE_POSTS, $types)) {
                //$this->get_posts_data();
            }
        }

        return $result;
        //return $this->getTranslations();
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

    /*
     *  ================= Export =================
     */

    protected function send_theme()
    {
        $theme = $this->current_theme();

        if (empty($theme)) {
            return 0;
        }

        $params = array();

        $boundary = wp_generate_password(24);

        if ($theme['group']) {
            $params['group'] = $theme['group'];
        }

        $headers = array(
            'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
            'Cache-Control' => 'no-cache',
        );

        $payload = $this->create_payload($boundary, $theme['lang_file'], $params);

        $body = $this->client()->remote('/abstractions/upload', array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => $payload,
        ));

        return !empty($body['data']) ? count($body['data']) : 0;
    }

    protected function send_content($type)
    {
        $default_language = $this->config()->getOption('default_language');
        $language_code = !empty($default_language['code']) ? $default_language['code'] : 'en';

        switch ($type) {
            case self::TYPE_POSTS: $data = $this->posts_data($language_code);
                break;

            case self::TYPE_TERMS: $data = $this->terms_data($language_code);
                break;

            default: $data = array();
        }

        $result = $this->client()->remote('/abstractions/create', array(
            'method' => 'POST',
            'body' => array(
                'data' => $data,
            )
        ));

        return !empty($result['data']) ? count($result['data']) : 0;
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

    protected function theme_group($theme)
    {
        $parentGroup = array(
            'name' => trim(get_theme_roots(), DIRECTORY_SEPARATOR),
        );

        $group = array(
            'name' => $theme,
            'parent' => $parentGroup,
        );

        return $group;
    }

    protected function current_theme()
    {
        $theme = wp_get_theme();
        $TextDomain = $theme->get('TextDomain');

        $default_language = $this->config()->getOption('default_language');

        if (!empty($default_language['code']) && (strlen($default_language['code']) == 2)) {
            $language_code = strtolower($default_language['code']) . '_' . strtoupper($default_language['code']);
        }

        $language_path = $theme->theme_root . DIRECTORY_SEPARATOR . $TextDomain . DIRECTORY_SEPARATOR . 'languages';

        if (!empty($language_code) && file_exists($language_path . DIRECTORY_SEPARATOR . $language_code . '.po')) {
            $lang_file = $language_path . DIRECTORY_SEPARATOR . $language_code . '.po';
        } elseif (file_exists($language_path . DIRECTORY_SEPARATOR . $TextDomain . '.pot')) {
            $lang_file = $language_path . DIRECTORY_SEPARATOR . $TextDomain . '.pot';
        } else {
            $lang_file = null;
        }

        if (!empty($lang_file)) {
            return array(
                'id' => $TextDomain,
                'name' => (string) $theme,
                'lang_file' => $lang_file,
                'group' => $this->theme_group($TextDomain),
            );
        }

        return array();
    }

    protected function posts_data($language_code)
    {
        // Posts
        $posts = $this->getPosts();

        $group = array('name' => self::TYPE_POSTS);

        $result = array();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                // Post Title
                if (!empty($post['post_title'][$language_code])) {
                    $result[] = array(
                        'name' => 'title' . self::DELIMITER . $post['ID'],
                        'value' => $post['post_title'][$language_code],
                        'group' => $group,
                    );
                }

                // Post Content
                if (!empty($post['post_content'][$language_code])) {
                    $result[] = array(
                        'name' => 'content' . self::DELIMITER . $post['ID'],
                        'value' => $post['post_content'][$language_code],
                        'group' => $group,
                    );
                }
            }
        }

        return $result;
    }

    protected function terms_data($language_code)
    {
        // Terms
        $terms = $this->getTerms();

        $group = array('name' => self::TYPE_TERMS);

        $result = array();

        if (!empty($terms)) {
            foreach ($terms as $term_id => $term) {
                // term Name
                if (!empty($term[$language_code])) {
                    $result[] = array(
                        'name' => 'name' . self::DELIMITER . $term_id,
                        'value' => $term[$language_code],
                        'group' => $group,
                    );
                }
            }
        }

        return $result;
    }

    /*
     *  ================= Import =================
     */

    protected function get_posts_data()
    {
        $data = $this->client()->remote('/translations?group_name=' . self::TYPE_POSTS);

        $result = array();

        if (!empty($data)) {
            foreach ($data as $v) {
                if (!empty($v['translations'])) {

                }
            }
        }

        return $result;
    }

    /*protected function getTranslations()
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
    }*/
}