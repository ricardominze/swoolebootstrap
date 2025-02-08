<?php

declare(strict_types=1);

namespace App\Api;

use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Infra\Router\Router;
use App\Core\Valueobject\Address;
use App\Core\Domain\Account\Entity\Account;
use App\Core\Domain\Account\Service\AccountService;

class AccountController
{
  private AccountService $accountService;

  public function __construct(AccountService $accountService)
  {
    $this->accountService = $accountService;
  }

  public function makeHandlers(Router &$router): void
  {
    $router->add('/account/{id:[0-9]+}', $this->get());
    $router->add('/account/open', $this->open());
    $router->add('/account/close', $this->close());
    $router->add('/account/taxes', $this->withdraw());
    $router->add('/account/balance', $this->balance());
    $router->add('/account/deposit', $this->deposit());
    $router->add('/account/transfer', $this->transfer());
    $router->add('/account/withdraw', $this->withdraw());
  }

  public function get(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'GET') {
        $response->status(404);
        $response->end("404 - Not found");
      }

      $dados = $this->accountService->get((int) $_REQUEST['id']);

      $response->end(\json_encode($dados));
    };
  }

  public function open(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function close(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function taxes(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function balance(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function deposit(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function transfer(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

  public function withdraw(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'POST') {
        $response->status(404);
        $response->end("404 - Not found");
      }
      // $response->end(\json_encode($dados));
    };
  }

}
