<?php

class Fwt_Container
{
    protected $api;

    protected $config;

    protected $translate;

    public function get_config()
    {
        if (null === $this->config) {
            require_once FWT_DIR . 'includes2/class-fwt-config.php';
            $this->config = new Fwt_Api($this);
        }
        return $this->config;
    }

}