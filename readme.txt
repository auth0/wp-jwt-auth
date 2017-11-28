=== Wordpress JWT Authentication ===
Tags: login, oauth, authentication, jwt, api, token
Tested up to: 4.1
Requires at least: 4.0
License: MIT
License URI: https://github.com/auth0/wp-jwt-auth/blob/master/LICENSE.md
Stable tag: trunk
Contributors: glena, auth0

Authenticate your APIs with JWT easily.

== Description ==

DEPRECATED - This library is no longer maintained/supported.

This plugin targets to add a easy way to authenticate your APIs using JWT.

Also, it provides a basic way to match the users and allow you to extend base on your needs easily with a filter.

How it works

This plugin will check the headers of the requests and if there is an `Authorization` header will try to log in a user that matches. So it is as easy to check for the current user to authenticate users in your own API.

Also, it provides support for the following plugins:
- http://wp-api.org/
- http://docs.woothemes.com/document/woocommerce-rest-api/

And any other that extends `Wp Rest Api` like http://tweichart.github.io/JSON-API-for-BuddyPress/ for BuddyPress.

== Installation ==

1. Install from the WordPress Store or upload the entire `wp-jwt-aith` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Access to the plugin settings and configure the keys and how should it match the users.

- Aud: represents the client id which the JWT was sent.
- Secret: used to verify the JWT signature
- Base64 Secret Encoded: it must be active if the secret is base64 encoded and needs to be decoded before checkig the signature.
- User Property: is the property which much match with the JWT attribute to determine the user.
- JWT Attribute: should match the User Property to determine the user.

or extend it implementing a filter:

add_filter( 'wp_jwt_auth_get_user', 'get_user',10);

function get_user($jwt) {
  ...
}

To see an example, check the UsersRepo (https://github.com/auth0/wp-jwt-auth/blob/master/lib/JWT_AUTH_UsersRepo.php).
