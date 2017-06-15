<?php

class Fwt_Translate
{
    private $config;

    private $languages = [];

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function get_config()
    {
        return $this->config;
    }

    /**
     *  Join functions
     *
     * @param $texts
     * @return string
     */
    public function join($texts)
    {
        if (!is_array($texts)) {
            return $texts;
        }

        $text = '';

        foreach ($texts as $lang => $val) {
            if (!empty($val)) {
                $text .= '[:' . $lang . ']' . $val;
            }
        }

        if (strlen($text) > 0) {
            $text .= '[:]';
        }

        return $text;
    }

    /**
     *  Split functions
     *
     * @param $text
     * @return array
     */
    function split($text)
    {
        $blocks = $this->get_language_blocks($text);
        return $this->split_blocks($blocks);
    }

    public function get_language_blocks($text)
    {
        $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        return preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public function split_blocks($blocks)
    {
        $result = array();
        $current_language = false;

        $languages = $this->get_config()->get_languages();

        if (empty($languages)) {
            return $blocks;
        }

        foreach ($languages as $language) {
            $result[$language['code']] = '';
        }

        foreach ($blocks as $block) {
            // detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }

            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    // correctly categorize text block
                    if ($current_language) {
                        if (!isset($result[$current_language])) {
                            $result[$current_language] = '';
                        }

                        $result[$current_language] .= $block;
                        $current_language = false;
                    } else {
                        foreach ($languages as $language) {
                            $result[$language['code']] .= $block;
                        }
                    }
                    break;
            }
        }

        foreach ($result as $lang => $text) {
            $result[$lang] = trim($text);
        }

        return $result;
    }

    public function split_languages($blocks)
    {
        $result = array();
        $current_language = false;

        foreach ($blocks as $block) {
            // detect c-tags
            if (preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect b-tags
            } elseif (preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            } elseif (preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }

            switch ($block) {
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    // correctly categorize text block
                    if ($current_language) {
                        if (!isset($result[$current_language])) $result[$current_language] = '';
                        $result[$current_language] .= $block;
                        $current_language = false;
                    }
                    break;
            }
        }

        foreach ($result as $lang => $text) {
            $result[$lang] = trim($text);
        }

        return $result;
    }

    public function get_posts()
    {
        $query = new WP_Query();

        $posts = $query->query(array(
            'numberposts' => '-1'
        ));

        $result = array();

        if (!empty($posts)) {
            foreach ($posts as $post) {
                $result[$post->ID] = array(
                    'ID' => $post->ID,
                    'post_content' => $this->split($post->post_content),
                    'post_title' => $this->split($post->post_title),
                );
            }
        }

        return $result;
    }

    public function get_post($id)
    {
        $post = get_post($id);

        if (!empty($post)) {
            return array(
                'ID' => $post->ID,
                'post_content' => $this->split($post->post_content),
                'post_title' => $this->split($post->post_title),
            );
        }

        return array();
    }

    public function update_post($row)
    {
        if (is_array($row['post_title'])) {
            $row['post_title'] = $this->join($row['post_title']);
        }

        if (is_array($row['post_content'])) {
            $row['post_content'] = $this->join($row['post_content']);
        }

        wp_update_post( $row );
    }
}