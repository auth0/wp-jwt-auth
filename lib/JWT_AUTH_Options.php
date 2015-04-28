<?php

class JWT_AUTH_Options {
    const OPTIONS_NAME = 'jwt_auth_settings';
    private static $_opt = null;

    public static function get_options(){
        if(empty(self::$_opt)){
            $options = get_option( self::OPTIONS_NAME, array());

            if(!is_array($options))
                $options = self::defaults();

            $options = array_merge( self::defaults(), $options );

            self::$_opt = $options;
        }
        return self::$_opt;
    }

    public static function get( $key, $default = null ){
        $options = self::get_options();

        if(!isset($options[$key]))
            return apply_filters( 'wp_auth0_get_option', $default, $key );
        return apply_filters( 'wp_auth0_get_option', $options[$key], $key );
    }

    public static function set( $key, $value ){
        $options = self::get_options();
        $options[$key] = $value;
        self::$_opt = $options;
        update_option( self::OPTIONS_NAME, $options );
    }

    private static function defaults(){
        return array(
            'version' => 1,
            'aud' => '',
            'secret' => '',
            'user_property' => 'id',
            'jwt_attribute' => 'sub',
            'override_user_query' => false,
        );
    }
}
