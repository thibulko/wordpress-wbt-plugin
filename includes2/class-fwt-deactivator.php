<?php

/**
 * Fired during plugin deactivation.
 */
class Fwt_Deactivator
{
    public static function deactivate()
    {
        delete_option(FWT_OPTION_NAME);
    }
}