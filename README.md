# DEPRECATED - This library is no longer maintained/supported.

This library was built as an example for a session in a PHP conference, and is no longer supported.

![](https://raw.githubusercontent.com/auth0/wp-jwt-auth/master/banner-1544x500.png)

#Wordpress JWT Authentication

Authenticate your APIs with JWT easily.

##Configuration
- **Aud**: represents the client id which the JWT was sent.
- **Secret**: used to verify the JWT signature
- **Base64 Secret Encoded**: it must be active if the secret is base64 encoded and needs to be decoded before checkig the signature.
- **User Property**: is the property which much match with the JWT attribute to determine the user.
- **JWT Attribute**: should match the User Property to determine the user.

##How it works

This plugin will check the headers of the requests and if there is an `Authorization` header will try to log in a user that matches. So it is as easy to check for the current user to authenticate users in your own API.

Also, it provides support for the following plugins:
- http://wp-api.org/
- http://docs.woothemes.com/document/woocommerce-rest-api/

And any other that extends `Wp Rest Api` like http://tweichart.github.io/JSON-API-for-BuddyPress/ for BuddyPress.


##Overriding the User Repository logic
The user repository is the responsible of retriving the user based on the JWT. By default, it looks in the user database to match the *User Property* and the *JWT Attribute*.

If you need to override the way the user matching is made (ie: you need to look to another table in the database) you can create your own User Repostory and match the user as you need.

To accomplish this, you need to add a filter:

```
    add_filter( 'wp_jwt_auth_get_user', array( __CLASS__, 'get_user' ),10);
```

To see an example, check the [UsersRepo](https://github.com/auth0/wp-jwt-auth/blob/master/lib/JWT_AUTH_UsersRepo.php).

> When the plugin is using a User Repository the *User Property* and *JWT Property* settings are ignored.   
