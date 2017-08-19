<?php
/**
 * The plugin bootstrap file
 *
 * Plugin Name: WBTranslator
 * Version: 0.0.2
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define('WBT_API_URL', 'http://wbtranslator.com/api/project/');

/**
 * Begins execution of the plugin.
 */
function run_wbt() {
    global $wpdb;
    $wpdb->show_errors = true;

    require_once dirname( __FILE__ ) . '/includes/class-wbt-container.php';
    $container = new WbtContainer();
    $container->set('wpdb', $wpdb);

    require_once dirname( __FILE__ ) . '/includes/class-wbt.php';
    $plugin = new WBTranslator($container);
    $plugin->run();
}

run_wbt();
