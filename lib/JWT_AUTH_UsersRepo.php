<?php

class JWT_AUTH_UsersRepo {

    public static function getUser($id) {
        global $wpdb;

        $user_property = esc_sql(JWT_AUTH_Options::get('user_property'));

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
