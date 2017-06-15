<?php
/**
 * The plugin bootstrap file
 *
 * Plugin Name: Future WEB Translator
 * Version: 0.0.1
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Begins execution of the plugin.
 */
function run_fwt() {
    require_once dirname( __FILE__ ) . 'includes/class-fwt.php';
    $plugin = new Fwt();
    $plugin->run();
}

run_fwt();
