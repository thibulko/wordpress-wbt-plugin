<?php

class FwtAdmin extends FwtAbstract
{
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
                $this->render('dashboard.view.php', 'add_key');
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

    protected function render($page, $controller = null)
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
            exit(' Template not found!');
        }

        if (is_wp_error($this->getErrors())) {
            var_dump($this->getErrors());
        }
    }

    protected function dashboard()
    {
        return array(
            'fwt_languages' => $this->container()->get('config')->getLanguages(),
            'api_key' => $this->container()->get('config')->getOption('api_key')
        );
    }

    protected function export()
    {
        $cnt = $this->container()->get('api')->export();

        return array(
            'fwt_languages' => $this->container()->get('config')->getLanguages(),
            'api_key' => $this->container()->get('api')->init($_POST['api_key']),
            'success' => array('Export ' . $cnt . ' translation values.')
        );
    }

    protected function import()
    {
        $cnt = $this->container()->get('api')->import();

        return array(
            'fwt_languages' => $this->container()->get('config')->getLanguages(),
            'api_key' => $this->container()->get('api')->init($_POST['api_key']),
            'success' => array('Import ' . $cnt . ' translation values.')
        );
    }

    protected function add_key()
    {
        if ((!empty($_POST['api_key']))) {
            $api_key = $_POST['api_key'];
            $this->container()->get('api')->init($_POST['api_key']);

            return array(
                'fwt_languages' => $this->container()->get('config')->getLanguages(),
                'api_key' => $api_key,
                'success' => array('Key was added')
            );
        }

        return array(
            'fwt_languages' => $this->container()->get('config')->getLanguages(),
            'api_key' => $this->container()->get('config')->getOption('api_key'),
            'errors' => array('Please set API key')
        );
    }
}