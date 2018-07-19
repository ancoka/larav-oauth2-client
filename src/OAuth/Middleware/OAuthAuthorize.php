<?php
/**
 * Created by PhpStorm.
 * File: OAuthAuthorize.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 14:24
 */
namespace Ancoka\OAuth\Middleware;

use Closure;
use Illuminate\Support\Facades\Cookie;
use Ancoka\OAuth\Facades\OAuth;
use Ancoka\OAuth\Services\Authorize;

class OAuthAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redirectUri = $request->cookie('redirect_uri');
        if (!$redirectUri) {
            $redirectUri = $request->fullUrl();
        }

        if ($request->session()->has('oauth.token')) {
            $oauth2token = $request->session()->get('oauth.token');
            $oauth2token = json_decode($oauth2token, true);
            if (!empty($oauth2token)) {
                $oauth2token = OAuth::refreshToken($oauth2token['refresh_token']);
                $request->session()->put('oauth.token', json_encode($oauth2token, JSON_UNESCAPED_UNICODE));
            }
        }

        if (!Authorize::hasLogin()) {
            return redirect(route('auth.login'))
                ->withCookies([Cookie::forever('redirect_uri', $redirectUri)]);
        }

        return $next($request);
    }
}
