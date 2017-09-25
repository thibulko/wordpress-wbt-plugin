<?php

class WbtConfig extends WbtAbstract
{
    const OPTION_NAME = 'wbt_options';

    const PLUGIN_NAME = 'wbt';
    
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
        return json_decode(get_option(self::OPTION_NAME), true);
    }

    public function getLanguages()
    {
        $result = [];

        $default_language = $this->getOption('default_language');
        $languages = $this->getOption('languages');

        if (!empty($default_language)) {
            $result[$default_language['code']] = $default_language;
        }

        if (!empty($languages)) {
            foreach ($languages as $language) {
                $result[$language['code']] = $language;
            }
        }

        return $result;
    }
    
    public function getApiKey()
    {
        return $this->getOption('api_key');
    }
    
    public function setApiKey($api_key)
    {
        return $this->setOption('api_key', $api_key);
    }
}