<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \GuzzleHttp\Client as GuzzleClient;

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
        Log::info('canLogin ' . $canLogin);

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
                'access_token' => json_encode($accessToken)
            ];
            $user = $this->updateOrCreatePMCUser($userData);
            Log::info('user ' . $user);

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
        $client = new GuzzleClient();
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
     * Create a new PMC user instance after get access token via PMC auth
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function updateOrCreatePMCUser(array $data)
    {
        try {
            $user = User::updateOrCreate(
                [
                    'email' => $data['email']
                ],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => '',
                    'type' => 'user',
                    'access_token' => $data['access_token']
                ]
            );
        }catch (\Exception $exception) {
            Log::warning($exception->getMessage());
            return null;
        }

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
