<?php

namespace Brazzer\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OAuth2{
    public function __construct(){

    }

    protected static function getOAuthUrl(){
        return config('admin.login.brazzer.oauthUrl');
    }

    protected static function getClientId(){
        return config('admin.login.brazzer.clientId');
    }

    protected static function getClientSecret(){
        return config('admin.login.brazzer.clientSecret');
    }

    protected static function getRedirectUri(){
        return config('admin.login.brazzer.redirectUri');
    }

    protected static function getAuthorizationUrl(){
        $query = http_build_query(array(
            'client_id'     => self::getClientId(),
            'redirect_uri'  => self::getRedirectUri(),
            'response_type' => 'code',
            'scope'         => '',
        ));
        return self::getOAuthUrl() . 'oauth/authorize?' . $query;
    }

    protected static function getOAuthUrlToken(){
        return self::getOAuthUrl() . 'oauth/token';
    }

    public static function redirect(){
        return new RedirectResponse(self::getAuthorizationUrl());
    }

    public static function getAccessToken(Request $request){
        //dd(self::getOAuthUrlToken());
        $http                               = new \GuzzleHttp\Client;
        $options['headers']['Content-Type'] = 'application/json';
        $response                           = $http->post(self::getOAuthUrlToken(), [
            'form_params' => [
                'grant_type'    => 'authorization_code',
                'client_id'     => self::getClientId(),
                'client_secret' => self::getClientSecret(),
                'redirect_uri'  => self::getRedirectUri(),
                'code'          => $request->code,
            ],
        ], $options);
        return json_decode((string) $response->getBody(), true);
    }

    protected static function refreshAccessToken($token_refresh = ''){
        $http     = new \GuzzleHttp\Client;
        $response = $http->post(self::getOAuthUrlToken(), [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $token_refresh,
                'client_id'     => self::getClientId(),
                'client_secret' => self::getClientSecret(),
                'scope'         => '',
            ],
        ]);
        return json_decode((string) $response->getBody(), true);
    }

    public static function get($ref, &$accessToken){
        $token = '';
        if($accessToken && isset($accessToken['access_token'])){
            $token = $accessToken['access_token'];
        }
        /*if($accessToken['expires_in'] < time() && isset($accessToken['refresh_token'])){
            $accessToken = self::refreshAccessToken($accessToken['refresh_token']);
            if($accessToken && isset($accessToken['access_token'])){
                $token = $accessToken['access_token'];
            }
        }*/
        if($token != ''){
            $http     = new \GuzzleHttp\Client;
            $response = $http->get(self::getOAuthUrl() . $ref, [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
            /*$response = $http->request('GET', self::getOAuthUrl() . $ref, [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);*/
            return json_decode((string) $response->getBody(), true);
        }
        return false;
    }
}