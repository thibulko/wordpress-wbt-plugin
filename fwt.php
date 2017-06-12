<?php
/*
  Plugin Name: Future WEB Translator
  Version: 0.0.1
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

define( 'FWT_VERSION', '0.0.1' );
define( 'FWT_FILE', __FILE__ );
define( 'FWT_BASENAME', plugin_basename( FWT_FILE ) );
define( 'FWT_DIR', dirname( FWT_FILE ) );

require_once( FWT_DIR . '/inc/class.fwt.php' );
add_action( 'init', array( 'Fwt', 'init' ) );
