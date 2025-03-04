<?php

namespace App\Infra\Util;

class SwooleDictionary
{
  public static array $dictionary =
  [
    'abort_count'           => 'Contagem de conexoes que foram fechadas de forma anormal ou abortadas',
    'accept_count'          => 'Numero de conexoes aceitas pelo servidor ate o momento',
    'close_count'           => 'Numero de conexoes que foram fechadas apos o termino de uma requisicao',
    'concurrency'           => 'Numero de conexoes simultaneas ativas no servidor',
    'connection_num'        => 'Numero de conexoes ativas no servidor no momento',
    'coroutine_num'         => 'Numero de corrotinas ativas no servidor',
    'coroutine_peek_num'    => 'Numero de corrotinas em espera aguardando para serem processadas',
    'dispatch_count'        => 'Contagem de requisicoes despachadas para os workers',
    'idle_worker_num'       => 'Numero de workers inativos no momento',
    'max_fd'                => 'O maior file descriptor (FD) utilizado pelo servidor',
    'min_fd'                => 'O menor file descriptor (FD) utilizado pelo servidor',
    'pipe_packet_msg_id'    => 'Identificador de mensagem de pacotes no pipeline do servidor',
    'request_count'         => 'Numero total de requisicoes recebidas',
    'response_count'        => 'Numero total de respostas enviadas',
    'session_round'         => 'Numero de rodadas de sessao realizadas (indicador de ciclos de atendimento)',
    'start_time'            => 'Timestamp que indica quando o servidor Swoole foi iniciado (em segundos desde a epoca Unix)',
    'task_worker_num'       => 'Numero de workers dedicados a tarefas assincronas',
    'total_recv_bytes'      => 'Total de bytes recebidos pelo servidor',
    'total_send_bytes'      => 'Total de bytes enviados pelo servidor',
    'user_worker_num'       => 'Numero de workers dedicados a tarefas especificas do usuario',
    'worker_concurrency'    => 'Numero de conexoes simultaneas que um worker pode processar',
    'worker_dispatch_count' => 'Numero de requisicoes despachadas para cada worker',
    'worker_num'            => 'Numero total de workers configurados no servidor Swoole',
    'worker_request_count'  => 'Numero de requisicoes processadas por cada worker',
    'worker_response_count' => 'Numero de respostas enviadas por cada worker',
  ];
}
