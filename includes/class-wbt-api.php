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

    public function themeGroup($theme)
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
                    'name' => $theme,
                    'lang_file' => $lang_file,
                );
            }
        }

        return $result;
    }

    public function export()
    {
        return $this->createAbstractions();
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

    public function uploadLangFile($filename, $group = null)
    {
        $this->container()->get('client')->remote('/abstractions/upload', array(
            'method' => 'POST',
            'headers' => array(
                'content-type' => 'multipart/form-data',
            ),
            'file' => $filename,
            /*'body' => array(
                'name' => $key,
                'value' => $post['post_content'][$default_language],
            )*/
        ));
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