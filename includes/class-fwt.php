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
        $this->define_api_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies()
    {
        require_once FWT_DIR . 'includes/class-fwt-loader.php';
        $this->loader = new Fwt_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function define_admin_hooks()
    {
        require_once FWT_DIR . 'admin/class-fwt-admin.php';
        $plugin = new Fwt_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->get_loader()->add_action( 'admin_menu', $plugin, 'init_menu' );
        //$this->get_loader()->add_action( 'admin_enqueue_scripts', $plugin, 'enqueue_scripts' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function define_public_hooks()
    {
        require_once FWT_DIR . 'public/class-fwt-public.php';
        $plugin = new Fwt_Public( $this->get_plugin_name(), $this->get_version() );
        $this->get_loader()->add_action( 'the_content', $plugin, 'the_content' );
        $this->get_loader()->add_action( 'the_title', $plugin, 'the_content' );
    }

    /**
     * Register all of the hooks related to the api functionality of the plugin.
     */
    private function define_api_hooks()
    {
        require_once FWT_DIR . 'api/class-fwt-api.php';
        $plugin = new Fwt_Api($this->get_plugin_name(), $this->get_version());
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
}