<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    protected $request;
    protected $auth;

    public function __construct(Request $request, JWTAuth $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public function login()
    {
        $credentials = [
            'email' => $this->request->input('email'),
            'password' => $this->request->input('password'),
        ];

        $token = $this->auth->attempt($credentials);

        if ($token !== false) {
            $result = [
                'data' => [
                    'token' => $token,
                ],
            ];

            return $result;
        } else {
            $result = [
                'error' => 'invalid_credentials'
            ];

            return response($result, Response::HTTP_UNAUTHORIZED);
        }
    }

    public function token()
    {
        $token = (string)$this->auth->getToken();
        $payload = $this->auth->getPayload();

        $result = [
            'data' => [
                'token' => [
                    'token' => $token,
                    'payload' => $payload->toArray(),
                    'expires_in' => $payload->get('exp') - time(),
                ],
            ],
        ];

        return $result;
    }

    public function tokenRefresh()
    {
        $this->auth->getToken();

        $token = $this->auth->refresh();

        $result = [
            'data' => [
                'token' => $token,
            ],
        ];

        return $result;
    }
}
