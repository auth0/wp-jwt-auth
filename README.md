#Wordpress JWT Authentication

This plugin targets to add JWT authentication to Wordpress API's provided by plugins.

##Configuration
- **Aud**: represents the client id which the JWT was sent.
- **Secret**: used to verify the JWT signature
- **Base64 Secret Encoded**: it must be active if the secret is base64 encoded and needs to be decoded before checkig the signature.
- **User Property**: is the property which much match with the JWT attribute to determine the user.
- **JWT Attribute**: should match the User Property to determine the user.
- **User Repository**: by default it should be empty. If you want to override the way the user matching is made (ie: you need to look to another table in the database) you can create your own User Repostory and match the user as you need. The user repository should expose one static method called `getUser` that receives the decoded JWT and should receive a `WP_User` object.

> When the plugin is using a User Repository the *User Property* and *JWT Property* settings are ignored.
