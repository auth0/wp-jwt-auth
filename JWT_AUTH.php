<?php
/**
 * Plugin Name: Wordpress JWT Authentication
 * Description: Implements JWT Authentication for APIs
 * Version: 1.1.0
 * Author: Auth0
 * Author URI: https://auth0.com
 */

define('JWT_AUTH_PLUGIN_FILE', __FILE__);
define('JWT_AUTH_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));
define('JWT_AUTH_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__) ));
define('JWT_AUTH_LANG', 'jwt_auth');

class JWT_AUTH {

    public static function Init()
    {
        spl_autoload_register(array(__CLASS__, 'autoloader'));

        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", array(__CLASS__, 'wp_add_plugin_settings_link'));

        add_action( 'init', array( __CLASS__, 'add_headers' ), 99 );       

        JWT_AUTH_UsersRepo::init();
        JWT_AUTH_UserProcessor::init();
        JWT_AUTH_Settings_Section::init();
        JWT_AUTH_Admin::init();
    }

    public static function add_headers() {
        header('Access-Control-Allow-Origin:'. get_http_origin()); 
        header('Access-Control-Allow-Credentials: true'); 

        if ( 'OPTIONS' == $_SERVER['REQUEST_METHOD'] ) {
            if ( isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ) ) {
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); 
            }
            if ( isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ) ) {
                header('Access-Control-Allow-Headers: ' . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']); 
            }
            die('options');
        }
    }
    public static function send_cors_headers( $headers ) {
        $headers['Access-Control-Allow-Origin']      = get_http_origin(); // Can't use wildcard origin for credentials requests, instead set it to the requesting origin
        $headers['Access-Control-Allow-Credentials'] = 'true';
        // Access-Control headers are received during OPTIONS requests
        if ( 'OPTIONS' == $_SERVER['REQUEST_METHOD'] ) {
            if ( isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] ) ) {
                $headers['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
            }
            if ( isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] ) ) {
                $headers['Access-Control-Allow-Headers'] = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'];
            }
        }
        return $headers;
    }

    private static function autoloader($class)
    {
        $path = JWT_AUTH_PLUGIN_DIR;
        $paths = array();
        $exts = array('.php', '.class.php');

        $paths[] = $path;
        $paths[] = $path.'lib/';

        foreach($paths as $p)
            foreach($exts as $ext){
                if(file_exists($p.$class.$ext)){
                    require_once($p.$class.$ext);
                    return true;
                }
            }

        return false;
    }

    // Add settings link on plugin page
    public static function wp_add_plugin_settings_link($links) {

        $settings_link = '<a href="admin.php?page=jwta">Settings</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    public static function getPluginDirUrl()
    {
        return plugin_dir_url( __FILE__ );
    }

}

JWT_AUTH::Init();
