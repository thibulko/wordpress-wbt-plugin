<?php

/**
 * The core plugin class.
 */

require_once dirname( __FILE__ ) . '/class-wbt-abstract.php';

class WBTranslator extends WbtAbstract
{
    /**
     * Define the core functionality of the plugin.
     *
     * @param null $container
     */
    public function __construct($container = null)
    {
        parent::__construct($container);

        $this->init();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        //$this->defineWidgets();
    }

    private function init()
    {
        // Config
        require_once dirname( __FILE__ ) . '/class-wbt-config.php';
        $this->container()->set('config', new WbtConfig());

        // Loader
        require_once dirname( __FILE__ ) . '/class-wbt-loader.php';
        $this->container()->set('loader', new WbtLoader());

        // HttpClient
        require_once dirname( __FILE__ ) . '/class-wbt-http-client.php';
        $client = new WbtHttpClient($this->container());
        $this->container()->set('client', $client);

        // Translator
        require_once dirname( __FILE__ ) . '/class-wbt-translator.php';
        $translator = new WbtTranslator();
        $translator->setLanguages($this->container()->get('config')->getLanguages());
        $this->container()->set('translator', $translator);

        // Api
        require_once dirname( __FILE__ ) . '/class-wbt-api.php';
        $this->container()->set('api', new WbtApi($this->container()));
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function defineAdminHooks()
    {
        require_once dirname( __FILE__ ) . '/../admin/class-wbt-admin.php';
        
        $plugin = new WbtAdmin($this->container());
        $this->container()->get('loader')->add_action( 'admin_menu', $plugin, 'init_menu' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function definePublicHooks()
    {
        require_once dirname( __FILE__ ) . '/../public/class-wbt-public.php';
        
        $plugin = new WbtPublic($this->container());
        $this->container()->get('loader')->add_action( 'the_content', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'the_title', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'wp_list_categories', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'the_tags', $plugin, 'the_content' );
    }

    /*private function defineWidgets()
    {
        require_once dirname( __FILE__ ) . '/../widgets/class-wbt-widget-switcher.php';
        $switcher = new WbtWidgetSwitcher($this->container());
        $this->container()->get('loader')->add_action( 'widgets_init', $switcher, 'init' );
    }*/

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->container()->get('loader')->run();
    }
}