<?php
/**
 * Created by PhpStorm.
 * File: OAuthFacade.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 14:24
 */

namespace Ancoka\OAuth\Facades;

use Illuminate\Support\Facades\Facade;

class OAuth extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'oauth';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes()
    {
        static::$app->make('oauth')->auth();
    }
}