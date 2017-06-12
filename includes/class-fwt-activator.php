<?php

/**
 * Fired during plugin activation
 */
class Fwt_Activator
{
    public static function activate()
    {
        add_option(FWT_OPTION_NAME);
    }
}