<?php

declare(strict_types=1);

namespace App\Infra\Router;

use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * Router
 * {id} → Qualquer valor ([^/]+)
 * {id:[0-9]+} → Apenas números
 * {slug:[a-zA-Z]+} → Apenas letras
 * {any:.+} → Qualquer coisa (permite / no meio, útil para capturar paths inteiros)
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function add(string $path, callable $callback, array $middlewares = []): void
    {
        $pattern = $this->convertToRegex($path);
        
        $this->routes[$pattern] = [
            'callback' => $callback,
            'middlewares' => $middlewares,
            'paramNames' => $this->extractParamNames($path)
        ];
    }

    private function convertToRegex(string $path): string
    {
        $pattern = preg_replace_callback('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', function ($matches) {
            $paramName = $matches[1];
            $paramRegex = $matches[2] ?? '[^/]+'; // Se não tiver regex, assume padrão "qualquer coisa até /"
            return '(?P<' . $paramName . '>' . $paramRegex . ')';
        }, $path);

        return "#^" . $pattern . "$#";
    }

    private function extractParamNames(string $path): array
    {
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)(?::([^}]+))?\}/', $path, $matches);
        return $matches[1] ?? [];
    }

    public function useMiddleware(callable $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    public function resolve(Request &$request, Response $response): void
    {
        $path = $request->server['request_uri'] ?? $request->server['path_info'] ?? '/';
        foreach ($this->routes as $pattern => $route) {
            if (preg_match($pattern, $path, $matches)) {
                $params = [];
                foreach ($route['paramNames'] as $param) {
                    if (isset($matches[$param])) {
                        $params[$param] = $matches[$param];
                    }
                }
                $middlewares = array_merge($this->middlewares, $route['middlewares']);
                $this->populateHttpVars($request, $params);
                $this->runMiddlewares($middlewares, $request, $response, $route['callback'], $params);
                return;
            }
        }
        $response->end("404 Not Found");
    }

    private function runMiddlewares(array $middlewares, Request &$request, Response $response, callable $callback, array $params): void
    {
        $middlewareChain = function ($index) use ($middlewares, $request, $response, $callback, $params, &$middlewareChain) {
            if ($index < count($middlewares)) {
                $middlewares[$index]($request, $response, function () use ($index, $middlewareChain) {
                    $middlewareChain($index + 1);
                });
            } else {
                $callback($request, $response, $params);
            }
        };
        $middlewareChain(0);
    }

    private function populateHttpVars(Request &$request, array $params): void
    {
        $_REQUEST = array_merge($_REQUEST, $params);

        if (!empty($request->get)) {
            $_REQUEST = array_merge($_REQUEST, $request->get);
        }

        if (!empty($request->post)) {
            $_REQUEST = array_merge($_REQUEST, $request->post);
        }
    }
}
