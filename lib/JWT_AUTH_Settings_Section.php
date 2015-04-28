<?php

class JWT_AUTH_Settings_Section {

    public static function init(){
        add_action( 'admin_menu', array(__CLASS__, 'init_menu') );
    }

    public static function init_menu(){
        add_menu_page( __('JWT Auth', JWT_AUTH_LANG), __('JWT Auth', JWT_AUTH_LANG), 'manage_options', 'jwta',
            array('JWT_AUTH_Admin', 'render_settings_page'),
            JWT_AUTH::getPluginDirUrl() . 'assets/img/jwticon.png',
            82 );

        add_submenu_page('jwta', __('JWT Auth Settings', JWT_AUTH_LANG), __('Settings', JWT_AUTH_LANG), 'manage_options', 'jwta', array('JWT_AUTH_Admin', 'render_settings_page') );
    }
}
