<?php

/**
 * The public-facing functionality of the plugin.
 */

class Fwt_Public
{
    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    private $config;

    private $api;

    private $translate;

    private $fwt_widget_switcher;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $config, $api, $translate, $fwt_widget_switcher, $plugin_name, $version )
    {
        $this->config = $config;
        $this->api = $api;
        $this->translate = $translate;
        $this->fwt_widget_switcher = $fwt_widget_switcher;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function define_widgets()
    {
        register_widget( $this->fwt_widget_switcher );
    }

    public function the_content($content)
    {
        $current_language = !empty($_GET['lang']) ? $_GET['lang'] : 'ua';

        $content = $this->translate->split($content);

        if (!is_array($content)) {
            return $content;
        }

        return isset($content[$current_language]) ? $content[$current_language] : $content[key($content)];
    }
}