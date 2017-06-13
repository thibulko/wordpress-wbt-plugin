<?php

/**
 * The core plugin class.
 */

class Fwt
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    protected $config;

    protected $api;

    protected $switcher_widget;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct()
    {
        $this->plugin_name = 'fwt';
        $this->version = '0.0.1';
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies()
    {
        require_once FWT_DIR . 'includes/class-fwt-loader.php';
        $this->loader = new Fwt_Loader();

        require_once FWT_DIR . 'includes/class-fwt-config.php';
        $this->config = new Fwt_Config();

        require_once FWT_DIR . 'includes/class-fwt-api.php';
        $this->api = new Fwt_Api( $this->config );

        require_once FWT_DIR . 'widgets/fwt-switcher-widget.php';
        $this->switcher_widget = new Fwt_switcher_widget( $this->config );
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function define_admin_hooks()
    {
        require_once FWT_DIR . 'admin/class-fwt-admin.php';
        $plugin = new Fwt_Admin( $this->get_config(), $this->get_api(), $this->switcher_widget, $this->get_plugin_name(), $this->get_version() );
        $this->get_loader()->add_action( 'admin_menu', $plugin, 'init_menu' );
        //$this->get_loader()->add_action( 'admin_enqueue_scripts', $plugin, 'enqueue_scripts' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function define_public_hooks()
    {
        require_once FWT_DIR . 'public/class-fwt-public.php';
        $plugin = new Fwt_Public( $this->get_config(), $this->get_api(), $this->get_switcher_widget(), $this->get_plugin_name(), $this->get_version() );
        $this->get_loader()->add_action( 'widgets_init', $plugin, 'define_widgets' );
        //$this->get_loader()->add_action( 'the_content', $plugin, 'the_content' );
        //$this->get_loader()->add_action( 'the_title', $plugin, 'the_content' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->get_loader()->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }
    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

    public function get_config()
    {
        return $this->config;
    }

    public function get_api()
    {
        return $this->api;
    }

    public function get_switcher_widget()
    {
        return $this->switcher_widget;
    }
}