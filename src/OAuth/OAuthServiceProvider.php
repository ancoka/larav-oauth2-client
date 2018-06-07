<?php
/**
 * Created by PhpStorm.
 * File: OAuthServiceProvider.php
 * User: MW
 * Date: 2018/6/2
 * Time: 13:51
 */
namespace Ancoka\OAuth;

use Illuminate\Support\ServiceProvider;
use Ancoka\OAuth\Console\OAuthMakeCommand;

class OAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // 发布配置到配置目录
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('oauth_client.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                OAuthMakeCommand::class,
            ]);
        }

    }

    public function register()
    {
        $this->app->singleton('oauth', function ($app) {
            return new OAuth($app, config('oauth_client'));
        });

        $this->app->alias('oauth', 'Ancoka\OAuth\OAuth');
    }
}