<?php

/**
 * Fired during plugin activation
 */
class Fwt_Activator
{
    public static function activate()
    {
        add_option('fwt_api_key',   '');
        add_option('fwt_languages', '');
    }
}