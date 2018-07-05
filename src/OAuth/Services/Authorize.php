<?php
/**
 * Created by PhpStorm.
 * File: Authorize.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 17:35
 */
namespace Ancoka\OAuth\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;

class Authorize
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $domain;

    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    private $permissionUrl;

    /**
     * @var null
     */
    private static $instance = null;

    /**
     * Authorize constructor.
     */
    private function __construct()
    {
        $this->domain = config('oauth_client.authorization_domain');
        $this->permissionUrl = config('oauth_client.permission_url');
    }

    /**
     * Authorize instance
     *
     * @return Authorize|null
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Authorize login
     *
     * @return bool
     */
    public static function login()
    {
        $client = new Client([
            'base_uri' => self::instance()->domain,
            'allow_redirects' => false,
            'timeout' => "15.0",
        ]);

        $isLogin = false;
        try {
            $response = $client->request('GET', self::instance()->permissionUrl, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => self::getAuthorizationToken(),
                ],
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            if ($result && $result['success']) {
                $userInfo = $result['data'];
                unset($userInfo['err']);
                if (!empty($userInfo)) {
                    $isLogin = true;
                    Session::put('oauth.userInfo', json_encode($userInfo, JSON_UNESCAPED_UNICODE));
                }
            }
        } catch (\Exception $e) {
            // @TODO
        }

        return $isLogin;
    }

    /**
     * Authorize logout
     */
    public static function logout()
    {
        // @TODO
    }

    /**
     * Authorized user information
     *
     * @return array|mixed
     */
    public static function user()
    {
        $userInfo = [];
        if (self::hasLogin()) {
            $userInfo = json_decode(Session::get('oauth.userInfo'), true);
        }
        return $userInfo;
    }

    /**
     * user has login
     *
     * @return bool
     */
    public static function hasLogin()
    {
        return (boolean)Session::has('oauth.userInfo');
    }

    /**
     * Authorization Token
     *
     * @return string
     */
    protected static function getAuthorizationToken()
    {
        $token = '';
        if (Session::has('oauth.token')) {
            $oauth2token = Session::get('oauth.token');
            $oauth2token = json_decode($oauth2token);

            $token = ucfirst($oauth2token->token_type) . ' ' . $oauth2token->access_token;
        }

        return $token;
    }

    private function __clone()
    {
    }
}