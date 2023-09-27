<?php

namespace Api\Websocket;

use Exception;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\MessageComponentInterface;
use Api\Websocket\Estrutura;


class SistemaChat implements MessageComponentInterface
{
    protected $cliente;
    protected $date;
    protected $save;
    private $tabela;

    public function __construct()
    {
        // Iniciando a instancia que salva todos os dados no banco de dados
        $this->save = new Estrutura();
        $this->tabela = "chamada";
        // Iniciar o objetos que deve armazenar os clientes conectados
        $this->cliente = new \SplObjectStorage();
        date_default_timezone_set('America/Sao_Paulo');
        $this->date = date('Y-m-d H:i:s', time());
    }

    // Abrir conexão para o novo cliente
    public function onOpen(ConnectionInterface $conn)
    {
        // Adicionar o cliente na lista
        $this->cliente->attach($conn);
        echo "Nova conexão: {$conn->resourceId}. \n\n";
    }

    // Enviar mensagens para os usuário conectados e pro banco
    public function onMessage(ConnectionInterface $from, $msg)
    {
        //
        $mgsT = json_decode($msg);
        $this->Salvar($mgsT);
        // Percorrer a lista de usuários conectados
        foreach($this->cliente as $cliente) {
            // Não enviar a mensagem para o usuário que enviou a mensagem
            if($from !== $cliente) {
                // Enviar as mensagems para os usuários
                $cliente->send($msg);
            }
        }

        echo "Usuário {$from->resourceId} enviou uma mensagem. \n\n";
    }

    // Desconectar o cliente do websocket
    public function onClose(ConnectionInterface $conn)
    {
        // Fechar a conexão e retirar o cliente da lista
        $this->cliente->detach($conn);

        echo "Usuário {$conn->resourceId} desconectou. \n\n";
    }

    // Função que será chamada caso ocorra algum erro no websocket
    public function onError(ConnectionInterface $conn, Exception $e)
    {
        // Fechar conexão do cliente
        $conn->close();

        echo "Ocorreu um erro: {$e->getMessage()} \n\n";
    }

    public function Salvar($mgsT){
                // Com isso aq eu posso usar qualquer função do nosso crud atraves de mensagens do chat
        $dados = array("mensagem" => $mgsT->mensagem, "id_CH_equipamento" => $mgsT->id_CH_equipamento, "date" => $this->date, "tabela" => $this->tabela);

        try{
            // Funções da Chamada
            $this->save->CallInsert($dados);
            $dados = array("status" => $mgsT->Status,"id" => $mgsT->id_CH_equipamento, "tabela" => "equipamento");
            $this->save->CallUpdate($dados);

        } catch (Exception $e) {

            // Mostrando Erros de Chamada
            echo "Mensagem: ".$e->getMessage()."\n";
            echo "Codigo: ".$e->getCode()."\n";
            echo "Linha: ".$e->getLine()."\n";
            echo "Arquivo: ".$e->getFile()."\n";
        }
    }
}
