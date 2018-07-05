<?php
/**
 * Created by PhpStorm.
 * File: AuthController.php
 * User: Ancoka <imancoka@gmail.com>
 * Created on 2018/6/2 16:50
 */
namespace Ancoka\OAuth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Ancoka\OAuth\Facades\OAuth;
use Ancoka\OAuth\Services\Authorize;

class AuthController extends Controller
{
    /**
     * OAuth login
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login(Request $request)
    {
        $state = OAuth::getStateCode();
        $url = OAuth::authorize($state);
        $request->session()->put('oauth.state', $state);
        return redirect($url);
    }

    /**
     * OAuth callback
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function callback(Request $request)
    {
        $requestUrl = $request->cookie('redirect_uri', $request->root());
        $code = $request->get('code', null);
        $state = $request->get('state', null);

        if (!$code) {
            return redirect(route('auth.login'));
        } elseif(empty($state) || (
            $request->session()->has('oauth.state') &&
            $request->session()->get('oauth.state') !== $state
        )) {
            if ($request->session()->has('oauth.state')) {
                $request->session()->forget('oauth.state');
            }

            return redirect(route('auth.login'));
        } else {
            if (!Authorize::hasLogin()) {
                if (!$request->session()->has('oauth.token')) {
                    $tokenInfo = OAuth::getToken($code);
                    $request->session()->put('oauth.token', json_encode($tokenInfo, JSON_UNESCAPED_UNICODE));
                }

                $isLogin = Authorize::login();
                if (!$isLogin) {
                    $request->session()->forget('oauth.state');
                    $request->session()->forget('oauth.token');
                    return redirect(route('auth.login'));
                }
            }
        }

        return redirect($requestUrl)->withCookies([Cookie::forget('redirect_uri')]);
    }

    /**
     * OAuth logout
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        if ($request->session()->has('oauth.token')) {
            $oauth2token = $request->session()->get('oauth.token');
            $oauth2token = json_decode($oauth2token);

            $request->session()->flush();
            $logoutUrl = OAuth::revokeToken($oauth2token->access_token);
            return redirect($logoutUrl);
        }

        $request->session()->flush();
        return redirect(route('auth.login'));
    }
}