<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PMCLoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.pmc_login');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $canLogin = $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember')
        );

        if ($canLogin) {
            return true;
        }

        try {
            $accessToken = $this->getPMCAccessToken($request);
            $accessToken['expires_at'] = time() + $accessToken['expires_in'];

            // reg a user with access_token
            $userData = [
                'name' => $request->input($this->username()),
                'email' => $request->input($this->username()),
                'password' => $request->input('password'),
                'access_token' => json_encode($accessToken)
            ];

            $user = $this->create($userData);

            $this->guard()->login($user);
            return true;
        } catch (\Exception $exception) {
            Log::error($exception);
        }

        return false;
    }

    /**
     * @param $request
     * @return array|null
     */
    protected function getPMCAccessToken($request)
    {
        $client = new \GuzzleHttp\Client();
        $url = 'https://api.pharmacity.io:8443/users/login';
        $options = [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'json' => [
                'employee_id' => $request->input($this->username()),
                'password' => $request->input('password')
            ]
        ];

        $response = $client->post($url, $options);
        $contents = json_decode($response->getBody()->getContents(), true);

        return $contents['data'];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 'user',
            'access_token'=> $data['access_token']
        ]);

        return $user;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }
}
