<?php
/**
 * The admin-specific functionality of the plugin.
 */

class Fwt_Admin
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

    /**
     * Initialize the class and set its properties.
     */
    public function __construct( $config, $api, $plugin_name, $version )
    {
        $this->config = $config;
        $this->api = $api;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function init_menu ()
    {
        add_options_page(
            'Translation Settings',
            'FWT Settings',
            'manage_options',
            'fwt-settings',
            array($this, 'route')
        );
    }

    public function route()
    {
        switch ($_GET['action']){
            case 'dashboard':
                $this->render('dashboard');
                break;

            case 'add_key':
                $this->add_api_key();
                break;
                
            case 'sync':
                $this->sync();
                break;

            default: 
                $this->render('dashboard');
                break;
        }
    }

    public function render($page)
    {
        $file = FWT_DIR . 'admin/views/' . strtolower($page) . '.view.php';
        
        if(file_exists($file)){
            include $file;
        }else{
            exit(' Template not found!');
        }
    }

    public function add_api_key()
    {
        if( !empty($_POST['api_key']) ){
            $this->config->set_option('api_key', $_POST['api_key']);
        }
    }

    public function sync()
    {
        $this->api->sync();
        $this->api->refresh();
    }
}