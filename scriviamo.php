<?php
/**
 * Plugin Name: Scriviamo Wordpress Plugin
 * Plugin URI:  https://scriviamo.ai
 * Description: Scriviamo Wordpress Plugin
 * Version:     2.0.0
 * Author:      Riccardo Mel
 * Author URI:  https://evolveit.agency
 * License:     GPL-3.0
 * License URI: https://scriviamo.ai
 * Text Domain: scriviamo ai
 * Domain Path: /languages
 *
 * @package scriviamo-ai
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

// Helper constants.
define( 'JWT_AUTH_PLUGIN_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
define( 'JWT_AUTH_PLUGIN_URL', rtrim( plugin_dir_url( __FILE__ ), '/' ) );
define( 'JWT_AUTH_PLUGIN_VERSION', '2.1.0' );

// Require composer.
require __DIR__ . '/vendor/autoload.php';

// Require classes.
require __DIR__ . '/class-auth.php';
require __DIR__ . '/class-setup.php';
require __DIR__ . '/class-devices.php';


add_filter( 'init', function( ) { 
	define('JWT_AUTH_SECRET_KEY', '6%gz8{8%BS*Dqi?yJ09-*VmoN5-9h/-6G!jq:fLP[z1}AxsqB8J+Ma-u]zKJ2K32'); 
	define('JWT_AUTH_CORS_ENABLE', true);
 });

new JWTAuth\Setup();
