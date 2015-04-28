<?php
/**
 * Plugin Name: Wordpress JWT Authentication
 * Description: Implements JWT Authentication for APIs
 * Version: 1
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

        add_filter( 'determine_current_user', array(__CLASS__, 'determine_current_user'), 10);
        add_filter( 'json_authentication_errors', array(__CLASS__, 'json_authentication_errors'));
        $plugin = plugin_basename(__FILE__);
        add_filter("plugin_action_links_$plugin", array(__CLASS__, 'wp_add_plugin_settings_link'));

        JWT_AUTH_Settings_Section::init();
        JWT_AUTH_Admin::init();

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

    public static function json_authentication_errors ( $error )
    {
    	// Passthrough other errors
    	if ( ! empty( $error ) ) {
    		return $error;
    	}

    	global $wp_json_basic_auth_error;

    	return $wp_json_basic_auth_error;
    }
    public static function determine_current_user ($user)
    {
        global $wp_json_basic_auth_error;

	    $wp_json_basic_auth_error = null;

        $authorization = false;

        if (function_exists('getallheaders'))
        {
            $headers = getallheaders();

            if (isset($headers['Authorization'])) {
                $authorization = $headers['Authorization'];
            }
        }
        elseif (isset($_SERVER["Authorization"])){
            $authorization = $_SERVER["Authorization"];
        }

        if ($authorization !== false) {

            try {
                $token = self::decodeJWT($authorization);
            }
            catch(Exception $e) {
                $wp_json_basic_auth_error = $e->getMessage();
                return null;
            }

            $jwt_attribute = JWT_AUTH_Options::get('jwt_property');

            $objuser = self::findAuth0User($token->$jwt_attribute);

            if (!$objuser) {
                $wp_json_basic_auth_error = 'Invalid user';
            }

            $user = $objuser->ID;
        }

        $wp_json_basic_auth_error = true;

        return $user;
    }

    protected static function decodeJWT($authorization)
    {
        require_once JWT_AUTH_PLUGIN_DIR . 'lib/php-jwt/Exceptions/BeforeValidException.php';
        require_once JWT_AUTH_PLUGIN_DIR . 'lib/php-jwt/Exceptions/ExpiredException.php';
        require_once JWT_AUTH_PLUGIN_DIR . 'lib/php-jwt/Exceptions/SignatureInvalidException.php';
        require_once JWT_AUTH_PLUGIN_DIR . 'lib/php-jwt/Authentication/JWT.php';

        $client_id = WP_Auth0_Options::get( 'client_id' );
        $client_secret = WP_Auth0_Options::get( 'client_secret' );

        $encUser = str_replace('Bearer ', '', $authorization);

        $secret = base64_decode(strtr($client_secret, '-_', '+/'));

        try {
            // Decode the user
            $decodedToken = \JWT::decode($encUser, $secret, ['HS256']);
            // validate that this JWT was made for us
            if ($decodedToken->aud != $client_id) {
                throw new CoreException("This token is not intended for us.");
            }
        } catch(\UnexpectedValueException $e) {
            throw new Exception($e->getMessage());
        }

        return $decodedToken;
    }

    // Add settings link on plugin page
    public static function wp_add_plugin_settings_link($links) {

        $settings_link = '<a href="admin.php?page=jwta">Settings</a>';
        array_unshift($links, $settings_link);

        return $links;
    }

    private static function findUser($id) {
        global $wpdb;

        $override_user_query = JWT_AUTH_Options::get('override_user_query');

        if (!$override_user_query)
        {
            $user_property = esc_sql(JWT_AUTH_Options::get('user_property'));
            $sql = 'SELECT u.*
                    FROM ' . $wpdb->users . '
                    WHERE '.$user_property.' = %s';
        }
        else
        {
            $sql = $override_user_query;
        }

        $userRow = $wpdb->get_row($wpdb->prepare($sql, $id));

        if (is_null($userRow)) {
            return null;
        }elseif($userRow instanceof WP_Error ) {
            self::insertAuth0Error('findAuth0User',$userRow);
            return null;
        }
        $user = new WP_User();
        $user->init($userRow);
        return $user;
    }

    public static function getPluginDirUrl()
    {
        return plugin_dir_url( __FILE__ );
    }

}

JWT_AUTH::Init();
