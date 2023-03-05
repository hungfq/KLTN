<?php

namespace App\Http\Middleware;

use Closure;
use Sentry\State\Scope;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Authenticate extends BaseMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $this->authenticate($request);

        $user = $this->auth->user();
        if ( $user->status === 'IA' ) {
            return $this->responseError('Your account is inactive, please contact admin!');
        }

        $payload = $this->auth->getPayload()->toArray();
        if ( !(data_get($payload, 'token_type') == 'internal' && data_get($payload, 'sub') == $user->id) ) {
            return $this->responseError('Invalid token!', 402);
        }

        $route = $request->route();
        $permissionName = data_get($route, '1.as');

        if ( !$user->can($permissionName) ) {
            throw new \Exception('Permission denies.');
        }

        return $next($request);
    }

    protected function responseError($message = '', $code = 400)
    {
        $message = $message ?: 'Something went wrong';

        $error = [
            'error' => [
                'message' => $message
            ]
        ];

        return response($error, $code);
    }
}