<?php
/**
 * Created by PhpStorm.
 * File: OAuth.php
 * User: MW
 * Date: 2018/6/2
 * Time: 11:37
 */
namespace Ancoka\OAuth;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class OAuth
{
    /**
     * @var Application
     */
    public $app;

    /**
     * OAuth 配置
     * @var array
     */
    private $config = [];

    /**
     * http client
     * @var null
     */
    private static $httpClient = null;

    /**
     * 接口请求头
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    /**
     * OAuth constructor.
     * @param Application $app
     * @param array $config
     */
    public function __construct(Application $app, $config = [])
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param $name
     * @return mixed|string
     */
    public function __get($name)
    {
        return isset($this->config[$name]) ? $this->config[$name] : '';
    }

    /**
     * 生成6位随机字符串
     *
     * @return bool|string
     */
    public function getStateCode($length = 6)
    {
        $string = substr(md5(time()), 0, $length);
        return $string;
    }

    /**
     * 获取 oauth authorize 跳转链接
     *
     * @param $state
     * @return string
     */
    public function authorize($state)
    {
        $query = http_build_query($this->getAuthorizeFields($state));
        return $this->domain . $this->authorize_url . '?' . $query;
    }

    /**
     * 获取 Access Token
     *
     * @param $code
     * @return array
     */
    public function getToken($code)
    {
        $client = $this->getHttpClient();
        $data = [];
        try {
            $response = $client->request('POST', $this->token_url, [
                'headers' => $this->getHeaders(),
                'form_params' => $this->getTokenFields($code)
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            if ($result && $result['success']) {
                $data = $result['data'];
                unset($data['err']);
                if (!empty($data)) {
                    $data['expires_time'] = time() + $data['expires_in'];
                }
            }
        } catch (\Exception $e) {
            // 接口异常
        }

        return $data;
    }

    /**
     * 刷新 Access Token
     *
     * @param $token
     * @return mixed
     */
    public function refreshToken($refreshToken)
    {
        $client = $this->getHttpClient();
        $data = [];
        try {
            $response = $client->request('POST', $this->token_url, [
                'headers' => $this->getHeaders(),
                'form_params' => $this->getRefreshTokenFields($refreshToken)
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            if ($result && $result['success']) {
                $data = $result['data'];
                unset($data['err']);
                if (!empty($data)) {
                    $data['expires_time'] = time() + $data['expires_in'];
                }
            }
        } catch (\Exception $e) {
            // 接口异常
        }

        return $data;
    }

    /**
     * 删除 Access Token 并登出
     *
     * @param $token
     * @return string
     */
    public function revokeToken($token)
    {
        $query = http_build_query($this->getRevokeTokenFields($token));
        return $this->domain . $this->revoke_token_url . '?' . $query;
    }

    /**
     * 注册 OAuth 认证路由
     *
     * @return void
     */
    public function auth()
    {
        Route::get('/auth/login', '\Ancoka\OAuth\Controllers\AuthController@login')->name('auth.login');
        Route::get('/auth/callback', '\Ancoka\OAuth\Controllers\AuthController@callback')->name('auth.callback');
        Route::get('/auth/logout', '\Ancoka\OAuth\Controllers\AuthController@logout')->name('auth.logout');
    }

    /**
     * 构建获取 code 的请求参数
     *
     * @param null $state
     * @return array
     */
    protected function getAuthorizeFields($state = null)
    {
        return [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->getRedirectUrl(),
            'response_type' => 'code',
            'state' => $state
        ];
    }

    /**
     * 构建获取 token 的请求参数
     *
     * @param $code
     * @return array
     */
    protected function getTokenFields($code)
    {
        return [
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->getRedirectUrl(),
            'grant_type' => 'authorization_code',
            'code' => $code,
        ];
    }

    /**
     * 构建刷新 token 的请求参数
     *
     * @param $token
     * @return array
     */
    protected function getRefreshTokenFields($token)
    {
        return [
            'grant_type' => 'refresh_token',
            'refresh_token' => $token
        ];
    }

    /**
     * 移除 Token
     *
     * @param $token
     * @return array
     */
    protected function getRevokeTokenFields($token)
    {
        return [
            'access_token' => $token,
            'redirect_uri' => $this->getRedirectUrl(),
        ];
    }

    /**
     * 获取回调地址
     *
     * @return string
     */
    protected function getRedirectUrl()
    {
        if(Route::has($this->callback_url)) {
            return route($this->callback_url);
        } else {
            return request()->root() . '/' . ltrim($this->callback_url, '/');
        }
    }

    /**
     * 获取 http client
     *
     * @return Client|null
     */
    protected function getHttpClient()
    {
        if (!self::$httpClient) {
            self::$httpClient = new Client([
                'base_uri' => $this->domain,
                'allow_redirects' => false,
                'timeout' => "15.0",
            ]);
        }
        return self::$httpClient;
    }

    /**
     * 获取请求头
     * @return array
     */
    protected function getHeaders()
    {
        $headers = $this->headers;
        $headers['Authorization'] = $this->config['client_token'];

        return $headers;
    }
}
