<?php
use Ancoka\OAuth\Services\Authorize;

if (!function_exists('auth_user')) {
    /**
     * 获取登录信息
     * @return array|mixed
     */
    function auth_user()
    {
        return Authorize::user();
    }
}

if (!function_exists('is_login')) {
    /**
     * 判断是否登陆
     * @return bool
     */
    function is_login()
    {
        return Authorize::hasLogin();
    }
}
