<?php

class FwtAbstract
{
    protected $container;

    protected $errors;

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

    public function addError($code = null, $message = null, $data = null)
    {
        if (null === $this->errors) {
            $this->errors = new WP_Error();
        }

        $this->errors->add($code, $message, $data);
    }

    public function getErrors()
    {
        return $this->errors;
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
            print "<pre>" . print_r($data, true) . "</pre>";
        } else {
            print $data . PHP_EOL;
        }
    }
}