<?php

/**
 * The core plugin class.
 */

class Fwt extends FwtAbstract
{
    /**
     * Define the core functionality of the plugin.
     */
    public function __construct($container = null)
    {
        parent::__construct($container);

        $this->defineAdminHooks();
        $this->definePublicHooks();
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function defineAdminHooks()
    {
        require_once dirname( __FILE__ ) . 'admin/class-fwt-admin.php';
        $plugin = new FwtAdmin( $this->getContainer() );
        $this->getContainer()->getLoader()->add_action( 'admin_menu', $plugin, 'init_menu' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    /*private function definePublicHooks()
    {
        require_once FWT_DIR . 'public/class-fwt-public.php';
        $plugin = new Fwt_Public( $this->get_config(), $this->get_api(), $this->get_translate(), $this->get_widget_switcher(), $this->get_plugin_name(), $this->get_version() );
        $this->get_loader()->add_action( 'widgets_init', $plugin, 'define_widgets' );
        $this->get_loader()->add_action( 'the_content', $plugin, 'the_content' );
        $this->get_loader()->add_action( 'the_title', $plugin, 'the_content' );
        //$this->get_loader()->add_action( 'wp_title', $plugin, 'fwt_wp_title' );
    }*/

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->get_loader()->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    public function get_widget_switcher()
    {
        return $this->widget_switcher;
    }
}