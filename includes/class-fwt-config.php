<?php

class Fwt_Config
{
    public function set_option($name, $value = null)
    {
        $options = $this->get_options();
        $options[$name] = $value;
        $this->set_options($options);
    }

    public function set_options($value = [])
    {
        update_option(FWT_OPTION_NAME, json_encode($value));
    }

    public function get_option($name, $default = null)
    {
        return $this->get_options($name, $default);
    }

    public function get_options($name = null, $default = null)
    {
        $options = get_option(FWT_OPTION_NAME);

        if ( (!empty($options)) && ( !is_array($options) ) ) {
            $options = json_decode($options, true);

            if (null !== $name) {
                if (isset($options[$name])) {
                    return $options[$name];
                }

                return $default;
            }

            return $options;
        }

        return $default;
    }

    public function get_languages()
    {
        $result = [];

        $default_language = $this->get_option('default_language');
        $languages = $this->get_option('languages');

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