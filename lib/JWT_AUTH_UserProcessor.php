<?php

class JWT_AUTH_UserProcessor {

    public static function init() {

        add_filter( 'woocommerce_api_check_authentication', array(__CLASS__, 'determine_current_user_for_wc'), 10);
        add_filter( 'determine_current_user', array(__CLASS__, 'determine_current_user'), 10);
        add_filter( 'json_authentication_errors', array(__CLASS__, 'json_authentication_errors'));

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

    protected static function getAuthorizationHeader() {
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

        return $authorization;
    }

    protected static function findUser($jwt) {
        $overrideUserRepo = JWT_AUTH_Options::get('override_user_repo');

        if ($overrideUserRepo) {
            return call_user_func(array($overrideUserRepo, 'getUser'), $jwt);
        }
        else {
            return JWT_AUTH_UsersRepo::getUser($jwt);
        }
    }

    public static function determine_current_user_for_wc($user) {
        return self::determine_current_user_generic($user, true);
    }

    public static function determine_current_user ($user) {
        return self::determine_current_user_generic($user, false);
    }
    public static function determine_current_user_generic ($user, $returnUserObj)
    {
        global $wp_json_basic_auth_error;

	    $wp_json_basic_auth_error = null;

        $authorization = self::getAuthorizationHeader();

        if ($authorization !== false) {

            try {
                $token = self::decodeJWT($authorization);
            }
            catch(Exception $e) {
                $wp_json_basic_auth_error = $e->getMessage();
                return null;
            }

            $objuser = self::findUser($token);

            if (!$objuser) {
                $wp_json_basic_auth_error = 'Invalid user';
            }

            if ($returnUserObj) {
                $user = $objuser;
            }
            else {
                $user = $objuser->ID;
            }
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

        $aud = JWT_AUTH_Options::get( 'aud' );
        $secret = JWT_AUTH_Options::get( 'secret' );
        $secret_base64_encoded = JWT_AUTH_Options::get( 'secret_base64_encoded' );

        if ($secret_base64_encoded) {
            $secret = base64_decode(strtr($secret, '-_', '+/'));
        }

        $encUser = str_replace('Bearer ', '', $authorization);

        try {
            // Decode the user
            $decodedToken = \JWT::decode($encUser, $secret, ['HS256']);

            // validate that this JWT was made for us
            if ($decodedToken->aud != $aud) {
                throw new Exception("This token is not intended for us.");
            }
        } catch(\UnexpectedValueException $e) {
            die($e->getMessage());
            throw new Exception($e->getMessage());
        }

        return $decodedToken;
    }

}
