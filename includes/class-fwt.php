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

        $this->init();
        $this->defineAdminHooks();
        $this->definePublicHooks();
        $this->defineWidgets();
    }

    private function init()
    {
        // Config
        require_once dirname( __FILE__ ) . '/class-fwt-config.php';
        $this->container()->set('config', new FwtConfig());

        // Loader
        require_once dirname( __FILE__ ) . '/class-fwt-loader.php';
        $this->container()->set('loader', new FwtLoader());

        // HttpClient
        require_once dirname( __FILE__ ) . '/class-fwt-http-client.php';
        $client = new FwtHttpClient();
        if (defined('API_URL')) {
            $client->setBaseUrl(API_URL);
        }
        $this->container()->set('client', $client);

        // Translator
        require_once dirname( __FILE__ ) . '/class-fwt-translator.php';
        $translator = new FwtTranslator();
        $translator->setLanguages($this->container()->get('config')->getLanguages());
        $this->container()->set('translator', $translator);

        // Api
        require_once dirname( __FILE__ ) . '/class-fwt-api.php';
        $this->container()->set('api', new FwtApi($this->container()));
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     */
    private function defineAdminHooks()
    {
        require_once dirname( __FILE__ ) . '/../admin/class-fwt-admin.php';
        $plugin = new FwtAdmin($this->container());
        $this->container()->get('loader')->add_action( 'admin_menu', $plugin, 'init_menu' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     */
    private function definePublicHooks()
    {
        require_once dirname( __FILE__ ) . '/../public/class-fwt-public.php';
        $plugin = new FwtPublic($this->container());
        $this->container()->get('loader')->add_action( 'the_content', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'the_title', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'wp_list_categories', $plugin, 'the_content' );
        $this->container()->get('loader')->add_action( 'the_tags', $plugin, 'the_content' );
    }

    private function defineWidgets()
    {
        require_once dirname( __FILE__ ) . '/../widgets/class-fwt-widget-switcher.php';
        $switcher = new FwtWidgetSwitcher($this->container());
        $this->container()->get('loader')->add_action( 'widgets_init', $switcher, 'init' );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run()
    {
        $this->container()->get('loader')->run();
    }
}