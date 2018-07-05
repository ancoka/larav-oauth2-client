<?php
/**
 * Created by PhpStorm.
 * File: OAuth.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 11:37
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
     * OAuth configuration
     *
     * @var array
     */
    private $config = [];

    /**
     * HTTP client
     * @var null
     */
    private static $httpClient = null;

    /**
     * Default request headers
     *
     * @var array
     */
    private $headers = [
        'Content-Type' => 'application/x-www-form-urlencoded',
    ];

    /**
     * OAuth constructor.
     *
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
     * Generate random string
     *
     * @return bool|string
     */
    public function getStateCode($length = 6)
    {
        $string = substr(md5(time()), 0, $length);
        return $string;
    }

    /**
     * Get oauth authorize jump link
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
     * Get access token
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
            // @TODO
        }

        return $data;
    }

    /**
     * Refresh access token
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
            // @TODO
        }

        return $data;
    }

    /**
     * Revoke access token
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
     * Register OAuth Authentication route
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
     * Get authorize code request params
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
     * Get refresh access token request params
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
     * Get revoke access token request params
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
     * Get callback url
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
     * Get http client
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
     * Get request headers
     *
     * @return array
     */
    protected function getHeaders()
    {
        $headers = $this->headers;
        $headers['Authorization'] = $this->config['client_token'];

        return $headers;
    }
}
