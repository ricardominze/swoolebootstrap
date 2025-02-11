<?php

declare(strict_types=1);

namespace App\Api;

use Swoole\Http\Request;
use Swoole\Http\Response;
use App\Infra\Router\Router;
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
    $router->add('/account/taxes', $this->taxes());
    $router->add('/account/deposit', $this->deposit());
    $router->add('/account/transfer', $this->transfer());
    $router->add('/account/withdraw', $this->withdraw());
    $router->add('/account/{id:[0-9]+}/balance', $this->balance());
  }

  public function get(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'GET') {
        $response->status(404);
        $response->end("404 - Not found");
      }

      $account = $this->accountService->get((int) $_REQUEST['id']);

      $response->end(\json_encode($account));
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

      $dados = \json_decode($request->getContent(), true);
      $account = new Account(null, (int) $dados['id_customer'], $dados['type_account'], (float) $dados['balance'], (int) $dados['status']);

      $account = $this->accountService->open($account);

      $response->end(\json_encode($account));
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

      $dados = \json_decode($request->getContent(), true);

      $account = $this->accountService->get((int) $dados['id']);

      $err = $this->accountService->close($account);

      $msg = $err ? $err->getMessage() : "Success, Account Closed";

      $response->end(\json_encode(['message'=>$msg]));
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

      $dados = \json_decode($request->getContent(), true);

      $account = $this->accountService->get((int) $dados['id']);

      $err = $this->accountService->taxes($account);

      $msg = $err ? $err->getMessage() : "Success, Payment Taxes OK";

      $response->end(\json_encode(['message'=>$msg]));
    };
  }

  public function balance(): callable
  {
    return function (Request &$request, Response $response) {

      $response->header("Content-Type", "application/json; charset=utf-8");

      if ($request->getMethod() != 'GET') {
        $response->status(404);
        $response->end("404 - Not found");
      }

      $balance = $this->accountService->balance((int) $_REQUEST['id']);
      
      $response->end(\json_encode(['balance' => $balance]));
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

      $dados = \json_decode($request->getContent(), true);

      $account = $this->accountService->get((int) $dados['id_account']);

      $err = $this->accountService->deposit($account, (float) $dados['amount']);

      $msg = $err ? $err->getMessage() : "Success, Account Deposit";

      $response->end(\json_encode(['message' => $msg]));
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
