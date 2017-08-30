<?php

class WbtAbstract
{
    const DELIMITER = '::';

    const TYPE_THEME = 'theme';
    const TYPE_POSTS = 'posts';
    const TYPE_TERMS = 'terms';

    protected $container;

    protected $client;
    protected $config;

    public static $types = array(
        self::TYPE_THEME,
        self::TYPE_POSTS,
        self::TYPE_TERMS,
    );

    public function __construct($container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    public function container()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function client()
    {
        if (null === $this->client) {
            $this->client = $this->container()->get('client');
        }

        return $this->client;
    }

    public function config()
    {
        if (null === $this->config) {
            $this->config = $this->container()->get('config');
        }

        return $this->config;
    }

    public function getPosts()
    {
        $db = $this->container()->get('wpdb');
        $table = $db->prefix . 'posts';
        $translator = $this->container()->get('translator');

        $posts = $db->get_results("SELECT ID, post_title, post_content FROM $table WHERE post_status = 'publish'");

        $result = array();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $result[$post->ID] = array(
                    'ID' => $post->ID,
                    'post_content' => $translator->split($post->post_content),
                    'post_title' => $translator->split($post->post_title),
                );
            }
        }

        return $result;
    }

    public function getTerms()
    {
        $db = $this->container()->get('wpdb');
        $table = $db->prefix . 'terms';
        $translator = $this->container()->get('translator');

        $terms = $db->get_results("SELECT term_id, `name` FROM $table");

        $result = array();

        if (!empty($terms)) {
            foreach ($terms as $term) {
                $result[$term->term_id] = $translator->split($term->name);
            }
        }

        return $result;
    }

    public function getTerm($id)
    {
        $translator = $this->container()->get('translator');

        $term = WP_Term::get_instance( $id );

        if (!empty($term)) {
            $term = $term->to_array();
            $term['name'] =  $translator->split($term['name']);
            return $term;
        }

        return array();
    }

    public function updateTerm($row)
    {
        $translator = $this->container()->get('translator');
        $db = $this->container()->get('wpdb');
        $where = array('term_id' => $row['term_id']);

        $data = array();

        if (is_array($row['name'])) {
            $data['name'] = $translator->join($row['name']);
        }

        if (!empty($data)) {
            $db->update($db->prefix . 'terms', $data, $where);
        }
    }

    public function getPost($id)
    {
        $translator = $this->container()->get('translator');

        $post = get_post($id);

        if (!empty($post)) {
            return array(
                'ID' => $post->ID,
                'post_content' => $translator->split($post->post_content),
                'post_title' => $translator->split($post->post_title),
            );
        }

        return array();
    }

    public function updatePost($row)
    {
        $translator = $this->container()->get('translator');

        if (is_array($row['post_title'])) {
            $row['post_title'] = $translator->join($row['post_title']);
        }

        if (is_array($row['post_content'])) {
            $row['post_content'] = $translator->join($row['post_content']);
        }

        wp_update_post( $row );
    }

    public function log($data)
    {
        if (is_array($data)) {
            $str = print_r($data, true);
        } else {
            $str = $data . PHP_EOL;
        }

        print  "<pre>$str</pre>";
    }
}