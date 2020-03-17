<?php

namespace Brazzer\Admin\Providers;

use Brazzer\Admin\Facades\Admin;
use GuzzleHttp\ClientInterface;
use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class BookingEngineSocialiteProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopeSeparator = ' ';

    protected function getAuthDomain($uri)
    {
        return config('booking_engine.url') . $uri;
    }

    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase($this->getAuthDomain('oauth/authorize'), $state);
    }

    protected function getTokenUrl()
    {
        return $this->getAuthDomain('oauth/token');
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get($this->getAuthDomain('api/me'), [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function getTokenByLogin()
    {
        $booking_engine = config('booking_engine');
        unset($booking_engine['url']);
        unset($booking_engine['redirect']);
        $response = $this->getHttpClient()->get($this->getAuthDomain('api/auth/login') . '?' . http_build_query($booking_engine));
        $data = json_decode($response->getBody()->getContents(), true);
        if ($data && isset($data['access_token'])) {
            return $data['access_token'];
        }
        return false;
    }

    public function getData($uri, $token = '')
    {
        /*if ($token == '') {
            $token = Admin::user() ? Admin::user()->getAccessToken() : '';
        }*/
        if ($token == '') {
            $token = $this->getTokenByLogin();
        }
        try {
            $response = $this->getHttpClient()->get($this->getAuthDomain($uri), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    public function putData($uri, $params = [], $token = '')
    {
        /*if ($token == '') {
            $token = Admin::user() ? Admin::user()->getAccessToken() : '';
        }*/
        if ($token == '') {
            $token = $this->getTokenByLogin();
        }
        try {
            $response = $this->getHttpClient()->put($this->getAuthDomain($uri . ''), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                'json' => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    public function postData($uri, $params = [], $token = '')
    {
        /*if ($token == '') {
            $token = Admin::user() ? Admin::user()->getAccessToken() : '';
        }*/
        if ($token == '') {
            $token = $this->getTokenByLogin();
        }
        try {
            $postKey = (version_compare(ClientInterface::VERSION, '6') === 1) ? 'form_params' : 'body';
            $response = $this->getHttpClient()->post($this->getAuthDomain($uri . ''), [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ],
                $postKey => $params,
            ]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar' => isset($user['avatar']) ? $user['avatar'] : '',
        ]);
    }

    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }
}