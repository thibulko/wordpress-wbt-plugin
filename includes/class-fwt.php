<?php

/**
 * The core plugin class.
 */

require_once dirname( __FILE__ ) . '/class-fwt-abstract.php';

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
        $this->defineWidgets();
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function defineAdminHooks()
    {
        require_once dirname( __FILE__ ) . '/../admin/class-fwt-admin.php';
        $plugin = new FwtAdmin($this->getContainer());
        $this->getContainer()->getLoader()->add_action( 'admin_menu', $plugin, 'init_menu' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function definePublicHooks()
    {
        require_once dirname( __FILE__ ) . '/../public/class-fwt-public.php';
        $plugin = new FwtPublic($this->getContainer());
        $this->getContainer()->getLoader()->add_action( 'the_content', $plugin, 'the_content' );
        $this->getContainer()->getLoader()->add_action( 'the_title', $plugin, 'the_content' );
    }

    private function defineWidgets()
    {
        require_once dirname( __FILE__ ) . '/../widgets/class-fwt-widget-switcher.php';
        $switcher = new FwtWidgetSwitcher($this->getContainer());
        $this->getContainer()->getLoader()->add_action( 'widgets_init', $switcher, 'init' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->getContainer()->getLoader()->run();
    }
}