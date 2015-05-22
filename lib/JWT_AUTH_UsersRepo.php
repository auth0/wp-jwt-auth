<?php

class JWT_AUTH_UsersRepo {

    public static function init() {
        add_filter( 'wp_jwt_auth_get_user', array( __CLASS__, 'getUser' ),10);
    }

    public static function getUser($jwt) { 
        global $wpdb;

        if ($jwt instanceof WP_User) return $jwt;

        $user_property = esc_sql(JWT_AUTH_Options::get('user_property'));
        $jwt_attribute = JWT_AUTH_Options::get('jwt_attribute');

        if (trim($user_property) == '' || trim($jwt_attribute) == '') return;

        $id = $jwt->$jwt_attribute;

        $sql = 'SELECT u.*
                FROM ' . $wpdb->users . '
                WHERE '.$user_property.' = %s';

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


}
