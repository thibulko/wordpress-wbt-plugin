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
                $this->render('dashboard.view.php', 'initAction');
                break;

            case 'export':
                $this->render('dashboard.view.php', 'exportAction');
                break;

            case 'import':
                $this->render('dashboard.view.php', 'importAction');
                break;

            case 'types':
                $this->render('dashboard.view.php', 'typesAction');
                break;

            case 'refresh':
                $this->render('dashboard.view.php', 'refreshAction');
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
    }

    public function dashboard($data = [])
    {
        $types = $this->container()->get('config')->getOption('types');

        return array_merge($data, array(
            'wbt_default_language' => $this->container()->get('config')->getOption('default_language'),
            'wbt_languages' => $this->container()->get('config')->getOption('languages'),
            'wbt_api_key' => $this->container()->get('config')->getOption('api_key'),
            'types' => (!empty($types) ? $types : array()),
        ));
    }

    public function exportAction()
    {
        $messages = array();

        $this->container()->get('api')->init();

        try {
            $result = $this->container()->get('api')->export();
            $remote = $this->client()->remote('/');

            if (!empty($remote['data']['languages'])) {
                foreach ($result as $k => $v) {
                    $messages['success'][] = "Export: $k - $v";
                }
            } else {
                throw new Exception('You need to add the languages to translate into in your wbtranslator.com dashboard.');
            }
        } catch (\Exception $e) {
            $messages['errors'] = array('ERROR Export: ' . $e->getMessage());
        }

        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }

    public function importAction()
    {
        $messages = array();

        $this->container()->get('api')->init();

        try {
            $result = $this->container()->get('api')->import();
            $remote = $this->client()->remote('/');

            if (!empty($remote['data']['languages'])) {
                foreach ($result as $k => $v) {
                    $messages['success'][] = "Import: $k - $v";
                }
            } else {
                throw new Exception('You need to add the languages to translate into in your wbtranslator.com dashboard.');
            }

        } catch (\Exception $e) {
            $messages['errors'] = array('ERROR Import: ' . $e->getMessage());
        }

        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }

    public function initAction()
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

    public function typesAction()
    {
        $messages = array();

        if (( !empty($_POST['types']) )) {
            $types = array_values(array_intersect(self::$types, $_POST['types']));
            $this->container()->get('config')->setOption('types', $types);
            $messages['success'] = array('Types was updated');
        }

        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }

    public function refreshAction()
    {
        $this->container()->get('api')->init();

        $messages['success'] = array('Data was refreshed');

        return $this->dashboard(array(
            'messages' => $messages,
        ));
    }


}
