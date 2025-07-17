<?php

namespace Moinframe\Loop;

use Kirby\Http\Response;

class Middleware
{
    /**
     * Authentication middleware
     * @param callable $next The next action to execute
     * @return callable Middleware function
     */
    public static function auth(callable $next): callable
    {
        return function () use ($next) {

            // Check if loop is enabled
            if (!Options::enabled()) {
                return Response::json([
                    'status' => 'error',
                    'message' => 'Loop is disabled',
                    'code' => 'DISABLED'
                ], 403);
            }

            $csrfToken = kirby()->request()->header('X-CSRF-Token');
            if (csrf($csrfToken) !== true) {
                return Response::json([
                    'status' => 'error',
                    'message' => t('moinframe.loop.csrf.invalid'),
                    'code' => 'CSRF_INVALID'
                ], 403);
            }


            if (Options::public() === false && kirby()->user() === null) {
                return Response::json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                    'code' => 'UNAUTHORIZED'
                ], 401);
            }


            return $next(...func_get_args());
        };
    }
}
