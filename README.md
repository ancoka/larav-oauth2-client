# larav-oauth2-client
larav oauth2 client 是一个针对 Laravel framework 实现的一个简单的 进行 OAuth 认证的扩展包。



## 安装

1. 环境要求 ```PHP >= 5.6 ```  并且 ```laravel >= 5.4``` 

2. 安装 larav oauth2 client，只需要添加下面内容到你的 composer.json 文件。然后执行 ```composer update```：

   ```
   "ancoka/larav-oauth2-client": "1.0.*"
   ```

   或者直接执行：

   ```
   composer require "ancoka/larav-oauth2-client:1.0.*"
   ```

   

3. 打开 ```config/app.php``` 文件添加下面内容到 ```providers``` 数组：

   ```
   Ancoka\OAuth\OAuthServiceProvider::class,
   ```

   

4. 同样在 ```config/app.php``` 文件中添加下面内容到 ```aliases``` 数组：

   ```
   'OAuth' => Ancoka\OAuth\Facades\OAuth::class,
   ```

   

5. 运行如下命令发布扩展包内配置文件 ```config/oauth_client.php```

   ```
   php artisan vendor:publish
   ```

   

6. 使用中间件，你需要添加如下内容：

   ```
   'oauth.authorize' => Ancoka\OAuth\Middleware\OAuthAuthorize::class,
   ```

   到 ```app/Http/Kernel.php ``` 文件 ```routeMiddleware``` 数组。

   

## 使用

#### 路由

生成 OAuth 认证路由，执行以下内容：

```
php artisan make:oauth
```

会在 ```routes/web.php``` 文件中添加 ```OAuth::routes()``` 。

#### 中间件

你可以使用中间件拦截需要进行身份验证的所有路由，类似以下内容：

```
Route::middleware(['oauth.authorize'])->group(function () {
    Route::get('/', 'IndexController@index')->name('home');
});
```



## License 

MIT