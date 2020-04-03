<?php

namespace Brazzer\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class BrazzerServiceProvider extends ServiceProvider{

    public function boot(){
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('booking_engine', function($app) use ($socialite){
            $config = $app['config']['booking_engine'];
            return $socialite->buildProvider(BookingEngineSocialiteProvider::class, $config);
        });
        $socialite->extend('brazzer', function($app) use ($socialite){
            $config = $app['config']['services.brazzer'];
            return $socialite->buildProvider(BrazzerSocialiteProvider::class, $config);
        });
        $socialite->extend('azure', function($app) use ($socialite){
            $config = $app['config']['services.azure'];
            return $socialite->buildProvider(AzureSocialiteProvider::class, $config);
        });
    }

    public function register(){
        $this->mergeConfigFrom(__DIR__ . '/../../config/services.php', 'services');
    }
}