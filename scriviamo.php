<?php
/**
 * Plugin Name: Scriviamo AI - Wordpress Plugin
 * Plugin URI:  https://scriviamo.ai
 * Description: Scriviamo AI - Wordpress Plugin
 * Version:     1.0.3
 * Author:      Scriviamo ai
 * Author URI:  https://scriviamo.ai
 * License:     GPL-3.0
 * License URI: https://scriviamo.ai
 * Text Domain: scriviamo-ai
 * Domain Path: /languages
 *
 * @package scriviamo-ai
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

 /**
 * Handle Cors
 */
add_action('init', 'handle_preflight');
function handle_preflight() {
    $origin = get_http_origin();
    if ($origin === 'https://aieditoriale.lndo.site' || $origin === 'https://ai-editoriale.evolveit.agency' || $origin === 'https://scriviamo.ai' || $origin === 'https://hoppscotch.io' || $origin === 'http://localhost:3000') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, X-WP-Nonce, Content-Type, Accept, Authorization');
        if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
            status_header(200);
            exit();
        }
    }
}


