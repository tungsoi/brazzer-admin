<?php

namespace Brazzer\Admin\Providers;

use Brazzer\Admin\Facades\Admin;
use GuzzleHttp\ClientInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class BrazzerSocialiteProvider extends AbstractProvider implements ProviderInterface{
    protected $scopeSeparator = ' ';

    protected function getAuthDomain($uri){
        return config('services.brazzer.oauthUrl') . $uri;
    }

    protected function getAuthUrl($state){
        return $this->buildAuthUrlFromBase($this->getAuthDomain('oauth/authorize'), $state);
    }

    protected function getTokenUrl(){
        return $this->getAuthDomain('oauth/token');
    }

    protected function getUserByToken($token){
        $response = $this->getHttpClient()->get($this->getAuthDomain('api/me'), [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getData($uri, $token = ''){
        if($token == ''){
            $token = Admin::user()->getAccessToken();
        }

        $response = $this->getHttpClient()->get($this->getAuthDomain($uri), [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function putData($uri, $params = [], $token = ''){
        if($token == ''){
            $token = Admin::user()->getAccessToken();
        }
        $response = $this->getHttpClient()->put($this->getAuthDomain($uri . ''), [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            'json'    => $params,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function postData($uri, $params = [], $token = ''){
        if($token == ''){
            $token = Admin::user()->getAccessToken();
        }
        $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
        $response = $this->getHttpClient()->post($this->getAuthDomain($uri . ''), [
            'headers' => [
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
            $postKey  => $params,
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function mapUserToObject(array $user){
        return (new User)->setRaw($user)->map([
            'id'     => $user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'avatar' => isset($user['avatar']) ? $user['avatar'] : '',
        ]);
    }

    protected function getTokenFields($code){
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}