<?php

class FwtAbstract
{
    protected $container;

    protected $errors;

    public function __construct($container = null)
    {
        if ($container) {
            $this->container = $container;
        }
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function addError($code = null, $message = null, $data = null)
    {
        if (null === $this->errors) {
            $this->errors = new WP_Error();
        }

        $this->errors->add($code, $message, $data);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function log($data)
    {
        if (is_array($data)) {
            print "<pre>" . print_r($data, true) . "</pre>";
        } else {
            print $data . PHP_EOL;
        }
    }
}