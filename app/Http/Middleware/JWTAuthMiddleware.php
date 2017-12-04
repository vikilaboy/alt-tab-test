<?php

namespace App\Http\Middleware;

use Closure;
use Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\JWTAuth;

class JWTAuthMiddleware
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function handle($request, \Closure $next)
    {
        if (! $token = $this->auth->setRequest($request)->getToken()) {
            return $this->errorResponse('token_not_provided');
        }

        try {
            $user = $this->auth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return $this->errorResponse('token_expired', $e->getStatusCode());
        } catch (JWTException $e) {
            return $this->errorResponse('token_invalid', $e->getStatusCode());
        }

        if (! $user) {
            return $this->errorResponse('user_not_found', Response::HTTP_NOT_FOUND);
        }

//        $this->events->fire('tymon.jwt.valid', $user);

        $response = $next($request);

        return $response;
    }

    protected function errorResponse($error, $status = Response::HTTP_BAD_REQUEST) {
        return response(['error' => $error], $status);
    }
}
