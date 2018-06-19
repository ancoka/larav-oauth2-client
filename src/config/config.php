<?php
return [
    /*
    |--------------------------------------------------------------------------
    | Client Id
    |--------------------------------------------------------------------------
    |
    | This value is the client id of your oauth service.
    */
    'client_id' => env('OAUTH_CLIENT_ID', null),

    /*
    |--------------------------------------------------------------------------
    | Client Secret
    |--------------------------------------------------------------------------
    |
    | This value is the client secret of your oauth service.
    */
    'client_secret' => env('OAUTH_CLIENT_SECRET', null),

    /*
    |--------------------------------------------------------------------------
    | Client Token
    |--------------------------------------------------------------------------
    |
    | This value is the client token of your oauth service.
    */
    'client_token' => env('OAUTH_CLIENT_TOKEN', null),

    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of your oauth service.
    */
    'domain' => env('OAUTH_DOMAIN', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of your oauth service.
    */
    'authorize_url' => env('OAUTH_AUTHORIZE_URL', '/oauth/authorize'),

    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of your oauth service.
    */
    'token_url' => env('OAUTH_TOKEN_URL', '/oauth/token'),

    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of your oauth service.
    */
    'revoke_token_url' => env('OAUTH_REVOKE_TOKEN_URL', '/oauth/revokeToken'),

    /*
    |--------------------------------------------------------------------------
    | Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of your user authorization service.
    */
    'authorization_domain' => env('AUTHORIZATION_DOMAIN', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    |
    | This value is the user permission of your user authorization service.
    */
    'permission_url' => env('AUTHORIZATION_PERMISSION_URL', '/auth/user/getUserPermissions'),

    /*
    |--------------------------------------------------------------------------
    | OAUTH CALLBACK URL
    |--------------------------------------------------------------------------
    |
    | This value is the URL of your oauth service callback.
    */
    'callback_url' => env('OAUTH_CALLBACK_RUL', '/auth/callback'),
];