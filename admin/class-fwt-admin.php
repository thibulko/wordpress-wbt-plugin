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
        $route = (empty($_GET['action'])) ? 'dashboard' : $_GET['action'];

        switch ($route){
            case 'dashboard':
                $this->render('dashboard.view.php', 'dashboard');
                break;

            case 'add_key':
                $this->render('dashboard.view.php', 'add_api_key');
                break;

            case 'add_api_key':
                $this->add_api_key();
                break;

            case 'export':
                $this->api->export();
                break;

            case 'import':
                $this->api->import();
                break;

            default:
                $this->render('dashboard');
                break;
        }
    }

    public function render($page, $controller)
    {
        ob_start();
        $file = FWT_DIR . 'admin/views/' . strtolower($page);

        if( (file_exists($file)) && (method_exists($this, $controller)) ){
            if($this->$controller()){
                extract($this->$controller());
            }
            
            include $file;
            $ret = ob_get_contents();
            ob_end_clean();
            echo $ret;
        }else{
            exit(' Template not found!');
        }
    }

    public function dashboard()
    {
        return array(
            'fwt_languages' => $this->config->get_languages(),
            'api_key' => $this->config->get_option('api_key')
        );
    }

    public function add_api_key()
    {
        $languages = $this->config->get_languages();

        if ( ( !empty($_POST['api_key'])  ) ) {
            $api_key = $_POST['api_key'];

            $this->config->set_option('api_key', $api_key);
            $this->api->init();

            return array(
                'fwt_languages' =>$languages,
                'api_key' => $api_key,
                'success' => array('Key was added')
            );
        } else {
            return array(
                'fwt_languages' => $languages,
                'api_key' => $this->config->get_option('api_key'),
                'errors' => array('Please set API key')
            );

        }        
    }
}