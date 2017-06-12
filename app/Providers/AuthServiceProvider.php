<?php

namespace App\Providers;

use App\Extensions\Auth\NoDatabaseSessionGuard;
use App\Extensions\Auth\NoDatabaseUserProvider;
use App\Like;
use App\Message;
use App\Policies\LikePolicy;
use App\Policies\MessagePolicy;
use Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Message::class => MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerProviders();

        $this->registerGuards();

        $this->registerGates();
    }

    protected function registerProviders()
    {
        Auth::provider('no_base', function ($app, array $config) {
            return new NoDatabaseUserProvider($app['hash'], $config['model']);
        });
    }

    protected function registerGuards()
    {
        Auth::extend('no_base', function ($app, $name, array $config) {
            // Return an instance of Illuminate\Contracts\Auth\Guard...
            return new NoDatabaseSessionGuard($name, Auth::createUserProvider($config['provider']), $this->app['session.store']);
        });
    }

    protected function registerGates()
    {
        Gate::define('messages.delete', 'MessagePolicy@delete');
        Gate::define('messages.like', 'MessagePolicy@like');
    }
}
