<?php

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

use App\Infra\Router\Router;
use App\Api\CustomerController;
use App\Core\Domain\Customer\Service\CustomerService;
use App\Infra\Adapter\CustomerRepository;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$http = new Server($_ENV['SERVER_HOST'], $_ENV['SERVER_PORT']);
$router = new Router();

//Middlewares Globais
//$router->useMiddleware((new App\Infra\Middleware\AuthMiddleware())->handler());

//Database
$db = new \PDO($_ENV['DBSTRING'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

//Handlers

$customerController = new CustomerController(new CustomerService(new CustomerRepository($db)));
$customerController->MakeHandlers($router);

//Ativa todos os hooks de coroutinas antes de iniciar o servidor
Swoole\Runtime::enableCoroutine(SWOOLE_HOOK_ALL);

$http->set([
  'worker_num' => 2,
  'enable_coroutine' => true,
]);

$http->on('start', function () {
    echo "Swoole http server is started at http://{$_ENV['SERVER_HOST']}:{$_ENV['SERVER_PORT']}".PHP_EOL;
});

$http->on('request', function (Request $request, Response $response) use ($router) {

    $callable = $router->resolve($request, $response);

    if ($callable === null) {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->status(404);
        $response->end("404 - Not found");
    } else {
        $callable($request, $response);
    }
});

$http->start();