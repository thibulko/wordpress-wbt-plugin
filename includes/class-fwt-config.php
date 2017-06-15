<?php

class FwtConfig extends FwtAbstract
{
    const OPTION_NAME = 'fwt_project_params';

    const VERSION = '0.0.1';

    const PLUGIN_NAME = 'fwt';

    protected $options;

    public function getVersion()
    {
        return self::VERSION;
    }

    public function getPluginName()
    {
        return self::PLUGIN_NAME;
    }

    public function setOptions($value = [])
    {
        update_option(self::OPTION_NAME, json_encode($value));
    }

    public function setOption($name, $value = null)
    {
        $options = $this->getOptions();
        $options[$name] = $value;
        $this->setOptions($options);
    }

    public function getOption($name, $default = null)
    {
        $options = $this->getOptions();

        if (isset($options[$name])) {
            return $options[$name];
        }
        return $default;
    }

    public function getOptions()
    {
        if (null === $this->options) {
            $this->options = json_decode(get_option(self::OPTION_NAME), true);
        }
        return $this->options;
    }

    public function getLanguages()
    {
        $result = [];

        $default_language = $this->getOption('default_language');
        $languages = $this->getOption('languages');

        if (!empty($default_language)) {
            $result[$default_language['id']] = $default_language;
        }

        if (!empty($languages)) {
            foreach ($languages as $language) {
                $result[$language['id']] = $language;
            }
        }
    
        return $result;
    }
}