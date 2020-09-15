<?php

namespace App\Providers;

use App\Auth\CustomUserProvider;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::provider('custom_user', function ($app, array $config) {
            return new CustomUserProvider($this->app['hash'], $config['model']);
        });

        Auth::extend('pmc_session', function ($app, $name, array $config) {
            $guard = new PMCSessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store']
            );
            if (method_exists($guard, 'setCookieJar')) {
                $guard->setCookieJar($this->app['cookie']);
            }
            return $guard;
        });

        Passport::routes();

        /**
         * Defining the user Roles
         */
        Gate::define('isAdmin', function ($user) {
            return $user->isAdmin();
        });

        Gate::define('isMod', function ($user) {
            return $user->isMod();
        });

        Gate::define('isPMCUser', function ($user) {
            return $user->isPMCUser();
        });

        Gate::define('isPartnerUser', function ($user) {
            return $user->isPartnerUser();
        });
    }
}
