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

    private $fwt_switcher_widget;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $config, $api, $fwt_switcher_widget, $plugin_name, $version )
    {
        $this->config = $config;
        $this->api = $api;
        $this->fwt_switcher_widget = $fwt_switcher_widget;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function define_widgets(){
        register_widget( 'fwt_switcher_widget' );
    }

    public function the_content($content)
    {
         return 'TEST: ' . $content;
    }
}