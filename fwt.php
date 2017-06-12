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

define( 'FWT_DIR', realpath(dirname( __FILE__ )) . '/' );

/**
 * The code that runs during plugin activation.
 */
function activate_fwt() {
    require_once FWT_DIR . 'includes/class-fwt-activator.php';
    Fwt_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_fwt() {
    require_once FWT_DIR . 'includes/class-fwt-deactivator.php';
    Fwt_Deactivator::deactivate();
}

/**
 * Begins execution of the plugin.
 */
function run_fwt() {
    require_once FWT_DIR . 'includes/class-fwt.php';
    $plugin = new Fwt();
    $plugin->run();
}

register_activation_hook( FWT_DIR, 'activate_fwt' );
register_deactivation_hook( FWT_DIR, 'deactivate_fwt' );
run_fwt();
