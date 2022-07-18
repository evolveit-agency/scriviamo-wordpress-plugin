<?php
/**
 * Plugin Name: Scriviamo AI - Wordpress Plugin
 * Plugin URI:  https://scriviamo.ai
 * Description: Scriviamo AI - Wordpress Plugin
 * Version:     1.0.0
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
 * Activate the plugin, add essentials
 */
 /*
function pluginprefix_activate() { 
 $file = fopen($_SERVER['DOCUMENT_ROOT']."/wp-config.php","r+")  or exit("Unable to open file!");
 
$insertPos=0;  // variable for saving //Users position
while (!feof($file)) {
    $line=fgets($file);
    if (strpos($line, "$table_prefix = 'wp_';")!==false) { 
        $insertPos=ftell($file);    // ftell will tell the position where the pointer moved, here is the new line after //Users.
        $newline =  " define('JWT_AUTH_SECRET_KEY', '".SECURE_AUTH_SALT."'); define('JWT_AUTH_CORS_ENABLE', true);";
    } else {
        $newline.=$line;   // append existing data with new data of user
    }
}

fseek($file,$insertPos);   // move pointer to the file position where we saved above 
fwrite($file, $newline);
fclose($file);
}
register_activation_hook( __FILE__, 'pluginprefix_activate' );
 */
 
 
 /**
 * Handle Cors
 */
add_action('init', 'handle_preflight');
function handle_preflight() {
    $origin = get_http_origin();
    if ($origin === 'https://aieditoriale.lndo.site' || $origin === 'https://ai-editoriale.evolveit.agency' || $origin === 'https://scriviamo.ai' || $origin === 'https://hoppscotch.io') {
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


