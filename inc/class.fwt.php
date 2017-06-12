<?php

class Fwt
{
    private static $initiated = false;
    public static $errors;

    public static $default_language = 'en';

    public static $current_language = 'ua';

    public static $languages = [
        'de', 'ua', 'pl'
    ];

    public static function init()
    {
        if (!self::$initiated) {
            self::init_consts();
            self::init_hooks();
            self::$initiated = true;
        }
    }

    private static function init_consts()
    {
        self::$errors = new WP_Error();
        define('FWT_API_URL', 'http://127.0.0.1./api/v1/');
        define('FWT_API_KEY', '');
    }

    public static function get_from_api($type, $method = 'GET', $params = [])
    {

        $url = rtrim(FWT_API_URL, '/') . '/' . $type . '/?api_key=' . FWT_API_KEY;
        return self::get_content($url);
    }

    public static function get_content($url)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $body = curl_exec($curl);
            curl_close($curl);
        } else {
            $body = file_get_contents($url);
        }

        return json_decode($body);
    }

    public static function get_project()
    {
        return self::get_from_api('project');
    }

    public static function get_project_languages()
    {
        return self::get_from_api('project/languages');
    }

    private static function init_hooks()
    {
        add_filter('the_title', array('Fwt', 'the_content'));
        add_filter('the_content', array('Fwt', 'the_content') );
    }

    public static function the_content($content)
    {
        $blocks = self::get_language_blocks($content);
        $result = self::qtranxf_split_blocks($blocks);
        //print_r($res);
        //return qtranxf_get_language_blocks($q_config['language']);
        return isset($result[self::$current_language]) ? $result[self::$current_language] : $result[self::$default_language];
    }

    public static function get_language_blocks($text)
    {
        $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        return preg_split($split_regex, $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    }

    public static function qtranxf_split_blocks($blocks, &$found = array())
    {
        global $q_config;
        $result = array();
        foreach(self::$languages as $language) {
            $result[$language] = '';
        }
        $current_language = false;
        foreach($blocks as $block) {
            // detect c-tags
            if(preg_match("#^<!--:([a-z]{2})-->$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect b-tags
            }elseif(preg_match("#^\[:([a-z]{2})\]$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
                // detect s-tags @since 3.3.6 swirly bracket encoding added
            }elseif(preg_match("#^\{:([a-z]{2})\}$#ism", $block, $matches)) {
                $current_language = $matches[1];
                continue;
            }
            switch($block){
                case '[:]':
                case '{:}':
                case '<!--:-->':
                    $current_language = false;
                    break;
                default:
                    // correctly categorize text block
                    if($current_language){
                        if(!isset($result[$current_language])) $result[$current_language]='';
                        $result[$current_language] .= $block;
                        $found[$current_language] = true;
                        $current_language = false;
                    }else{
                        foreach(self::$languages as $language) {
                            $result[$language] .= $block;
                        }
                    }
                    break;
            }
        }
        //it gets trimmed later in qtranxf_use() anyway, better to do it here
        foreach($result as $lang => $text){
            $result[$lang]=trim($text);
        }
        return $result;
    }
}