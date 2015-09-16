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

This plugin targets to add a easy way to authenticate your APIs using JWT.

Also, it provides a basic way to match the users and allow you to extend base on your needs easily with a filter.

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
