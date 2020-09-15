<?php


namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

class CustomUserProvider extends EloquentUserProvider
{

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        // in case we need to customize how we retrieve user from diff source

        // just call parent method for now
        return parent::retrieveById($identifier);
    }

}
