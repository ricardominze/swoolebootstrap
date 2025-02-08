<?php

declare(strict_types=1);

namespace App\Infra\Middleware;

use Swoole\Http\Request;
use Swoole\Http\Response;

class AuthMiddleware 
{
    public function handler(): callable
    {
        return static function (Request &$request, Response $response, callable $next) {

            $token = $request->header['authorization'] ?? null;
            if ($token !== 'Bearer meu-token-secreto') {
                $response->end("403 Forbidden");
                return;
            }
            $next();
        };
    }
}