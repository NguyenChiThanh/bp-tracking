<?php


namespace App\Providers;


use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Log;

class PMCSessionGuard extends SessionGuard
{

    /**
     * Determine if the user has valid access token.
     *
     * @param  mixed  $user
     * @param array $credentials
     * @return bool
     */
    protected function hasValidCredentials($user, $credentials)
    {
        // if user has access_token['expires_at'] and not expired - fireValidatedEvent and return true
        // if user has access_token['expires_at'] and expired - return false
        if($user && in_array($user['type'], ['user', 'admin']) && isset($user['access_token'])) {
            if(json_decode($user['access_token'])->expires_at > time()) {
                $this->fireValidatedEvent($user);
                Log::info('Valid access token');
                return true;
            }
            Log::info('Invalid access token');
            return false;
        }

        Log::info('No access token, login by user/pass');
        // if user has no access_token - call parent::hasValidCredentials
        return parent::hasValidCredentials($user, $credentials);
    }
}
