<?php
/**
 * Created by PhpStorm.
 * File: OAuthServiceProvider.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 13:51
 */
namespace Ancoka\OAuth;

use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * The application's commands.
     *
     * @var array
     */
    protected $commands = [
        \Ancoka\OAuth\Console\OAuthMakeCommand::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'oauth.authorize' => \Ancoka\OAuth\Middleware\OAuthAuthorize::class,
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        // publish application config.
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('oauth_client.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('oauth', function ($app) {
            return new OAuth($app, config('oauth_client'));
        });

        $this->app->alias('oauth', 'Ancoka\OAuth\OAuth');

        $this->registerRouteMiddleware();
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }
}