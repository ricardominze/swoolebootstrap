<?php

require __DIR__ . '/vendor/autoload.php';

use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Infra\Telemetry\Telemetry;
use Prometheus\Storage\InMemory;

use App\Infra\Router\Router;
use App\Api\AccountController;
use App\Infra\Adapter\AccountRepository;
use App\Core\Domain\Account\Service\AccountService;
use App\Api\CustomerController;
use App\Infra\Adapter\CustomerRepository;
use App\Core\Domain\Customer\Service\CustomerService;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//SERVER
$server = new Server($_ENV['SERVER_HOST'], $_ENV['SERVER_PORT']);

//ROUTER
$router = new Router();

//TELEMETRY
Telemetry::create('SWOOLEBOOTSTRAP', '1.0', 'dev', 'development');
Telemetry::configTracer('http://localhost:4318/v1/traces', 'application/json');
Telemetry::configPrometheus(new InMemory());
Telemetry::start();

//MIDDLEWARE
//$router->useMiddleware((new App\Infra\Middleware\AuthMiddleware())->handler());

//DATABASE
$db = new \PDO($_ENV['DBSTRING'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

//HANDLERS
$accountController = new AccountController(new AccountService(new AccountRepository($db)));
$accountController->makeHandlers($router);
$customerController = new CustomerController(new CustomerService(new CustomerRepository($db)));
$customerController->makeHandlers($router);

//Ativa todos os hooks de coroutinas antes de iniciar o servidor
Swoole\Runtime::enableCoroutine(SWOOLE_HOOK_ALL);
    
$server->set([
  'worker_num' => 2,
  'enable_coroutine' => true,
]);

$server->on('start', function () {
    echo "Swoole http server is started at http://{$_ENV['SERVER_HOST']}:{$_ENV['SERVER_PORT']}".PHP_EOL;
});

$server->on('request', function (Request $request, Response $response) use ($server, $router) {

    $path = $request->server['request_uri'] ?? $request->server['path_info'] ?? '/';

    if($path == '/metrics') {

        $swooleStats = $server->stats();
        foreach ($swooleStats as $key => $value) {
            if(in_array($key, ['abort_count','accept_count','close_count','dispatch_count','request_count' ,'response_count','session_round', 'worker_dispatch_count','worker_request_count','worker_response_count' ])) {
                Telemetry::getPrometheusRegistry()->getOrRegisterHistogram('swoole', $key, App\Infra\Util\SwooleDictionary::$dictionary[$key], [$key], [0.1, 0.5, 1, 2, 10])->observe($value, [$key]);
            } else {
                Telemetry::getPrometheusRegistry()->getOrRegisterGauge('swoole', $key, App\Infra\Util\SwooleDictionary::$dictionary[$key])->set($value);
            }
        }
        $response->header("Content-Type", "text/plain; version=0.0.4");
        $response->end(Telemetry::getPrometheusOut());
    }

    $callable = $router->resolve($request, $response);

    if ($callable === null) {
        $response->header("Content-Type", "text/html; charset=utf-8");
        $response->status(404);
        $response->end("404 - Not found");
    } else {
        $callable($request, $response);
    }
});

$server->start();
