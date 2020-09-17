<?php


namespace App\Providers;


use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        if($user && $user->type == User::PMC_USER && isset($user['access_token'])) {
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
        // check if user is active or not
        if ($user && $user['status'] != User::ACTIVE) {
            Log::warning('Account is not in active status');
            return false;
        }
        return parent::hasValidCredentials($user, $credentials);
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // in case we need to customize the user model response to client

        // if not call to parent
        return parent::user();
    }
}
