<?php

class WbtAdmin extends WbtAbstract
{
    public function init_menu ()
    {
        add_menu_page(
            'WBTranslator Settings',
            'WBTranslator',
            'manage_options',
            'wbt-settings',
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

            case 'export':
                $this->render('dashboard.view.php', 'export');
                break;

            case 'import':
                $this->render('dashboard.view.php', 'import');
                break;

            default:
                $this->render('dashboard');
                break;
        }
    }

    public function render($page, $controller = null)
    {
        ob_start();
        $file = dirname(__FILE__) . '/views/' . strtolower($page);

        if( (file_exists($file)) && (method_exists($this, $controller)) ){
            extract($this->$controller());

            include $file;
            $ret = ob_get_contents();
            ob_end_clean();
            echo $ret;
        }else{
            exit('Template not found!');
        }

        if (is_wp_error($this->getErrors())) {
            var_dump($this->getErrors());
        }
    }

    public function dashboard($data = [])
    {
        return array_merge($data, array(
            'wbt_default_language' => $this->container()->get('config')->getOption('default_language'),
            'wbt_languages' => $this->container()->get('config')->getOption('languages'),
            'wbt_api_key' => $this->container()->get('config')->getOption('api_key'),
            'wbt_themes' => $this->container()->get('api')->themesWithLanguages(),
        ));
    }
    
    public function export()
    {
        $messages = array();
    
        try {
            $result = $this->container()->get('api')->export();
            $messages['success'] = array('Export ' . (!empty($result) ? $result : 0) . ' abstract names.');
        } catch (\Exception $e) {
            $messages['errors'] = array('ERROR Export: ' . $e->getMessage());
        }
    
        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }
    
    public function import()
    {
        $messages = array();
        
        try {
            $result = $this->container()->get('api')->import();
            $messages['success'] = array('Import ' . (!empty($result) ? $result : 0) . ' translation values.');
        } catch (\Exception $e) {
            $messages['errors'] = array('ERROR Import: ' . $e->getMessage());
        }
        
        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }
    
    public function add_api_key()
    {
        $messages = array();
        
        if (( !empty($_POST['api_key']) )) {
            $api_key = $_POST['api_key'];

            try {
                $this->container()->get('config')->setApiKey($api_key);
                $this->container()->get('api')->init();
                $messages['success'] = array('Key was added');
            } catch (\Exception $e) {
                $messages['errors'] = array($e->getMessage());
            }
        }
        
        return $this->dashboard(array(
            'api_key' => $this->container()->get('config')->getOption('api_key'),
            'messages' => $messages,
        ));
    }
}