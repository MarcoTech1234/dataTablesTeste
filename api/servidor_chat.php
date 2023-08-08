<?php

// Vamos utilizar para iniciar o webSocket
use Api\Websocket\SistemaChat;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

// Incluir o composer
require __DIR__ . '/vendor/autoload.php';

// Armazena a instancia do Servidor, depois instancio a comunicação basica Http, 
// apos realizo as funcionalidades websocket e instancio  a classe SistemaChat que estara nessa conexão bidirecional

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SistemaChat()
        )
    ),
    8080
);

// Iniciar o servidor e começar a escutar as conexões
$server->run();

/* Explicações Video Celka (Minuto 19:13) 

    $server: variavel que irá armazenar a instancia do servidor WebSocket.

    $IoServer::factory(): método para criar uma instancia do servidor WebSocket.

    new HttpServer(): classe que fornece funcionalidade de comunicação HTTP básicas.

    new WsServer(): classe responsavel por fornecer funcionalidade WebSocket adicionais, permitindo 
a comunicação bidirecionl em tempo real.

    new SistemaChat(): classe criada para lidar com eventos relacionados ao WebSocket, como receber
mensagens, abrir conexões, fechar conexões, entre outras funcionalidades.

    8080: porta em que o servidor WebSocket será executada.

Link: https://www.youtube.com/watch?v=9DcpNEK_VL4&t=162s
*/