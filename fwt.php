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

define('API_URL', 'http://fnukraine.pp.ua/api/v2/');

/**
 * Begins execution of the plugin.
 */
function run_fwt() {
    global $wpdb;
    $wpdb->show_errors = true;

    require_once dirname( __FILE__ ) . '/includes/class-fwt-container.php';
    $container = new FwtContainer();
    $container->set('wpdb', $wpdb);

    require_once dirname( __FILE__ ) . '/includes/class-fwt.php';
    $plugin = new Fwt($container);
    $plugin->run();
}

run_fwt();
