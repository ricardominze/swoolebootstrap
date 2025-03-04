<?php

declare(strict_types=1);

namespace App\Api;

use Swoole\Coroutine;
use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Infra\Router\Router;
use App\Infra\Telemetry\Telemetry;
use App\Core\Valueobject\Address;
use App\Core\Domain\Customer\Entity\Customer;
use App\Core\Domain\Customer\Service\CustomerService;

use function Co\go;
use function Co\defer;

class CustomerController
{
  private CustomerService $customerService;

  public function __construct(CustomerService $customerService)
  {
    $this->customerService = $customerService;
  }

  public function makeHandlers(Router &$router): void
  {
    $router->add('/customer/index', $this->index());
    $router->add('/customer/{id:[0-9]+}', $this->get());
    $router->add('/customer/save', $this->save());
  }

  public function index(): callable 
  {
    return function (Request &$request, Response $response) {
    
      go(function(){

        defer(function(){
          echo "\nterminou o primeiro";
        });

        echo "\nEnviando e-mail";
        Coroutine::sleep(5); //Simula uma operação assíncrona
        echo "\n\nE-mail enviado!";

      });

      go(function(){

        defer(function(){
          echo "\nterminou o segundo";
        });

        echo "\nPreparando pedido";
        Coroutine::sleep(10); //Simula uma operação assíncrona
        echo "\n\nPedido finalizado!";
      });

      $request->getContent();
      $response->end("atividade concluida!");
    };
  }

  public function get(): callable
  {
    return function (Request &$request, Response $response) {

      $tracer = Telemetry::getTracer('CUSTOMER');
      $span = $tracer->spanBuilder('/customer/{id:[0-9]+}')->startSpan();
      
      try
      {
          $span->setAttribute('http.method', 'GET');
          $span->setAttribute('net.protocol.version', '1.1');
      }
      finally
      {
          $span->end();
      }

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'GET') {
        $response->status(404);
        $response->end("404 - Not found");
      }

      $dados = $this->customerService->get((int) $_REQUEST['id']);

      $response->end(\json_encode($dados));
    };
  }

  public function save(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }

      $post = \json_decode($request->getContent(), true);

      $customer = new Customer((int) $post['id'], $post['name'], new Address($post['city'], $post['street'], $post['zipcode']));
      $dados = $this->customerService->save($customer);

      $response->end(\json_encode($dados));
    };
  }
}
