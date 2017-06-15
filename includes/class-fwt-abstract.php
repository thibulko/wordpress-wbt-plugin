<?php

class FwtAbstract
{
    protected $container;

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
}