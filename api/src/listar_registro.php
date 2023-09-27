<?php

// Incluir o arquivo com conexão com banco de dados
use Api\Websocket\Conexao\Conn;

$url = 'vendor/autoload.php';
// Verificador de URL simples
if (file_exists($url)) {
    // Verificando se estou usando o caminho Web socket para ser chamado no servidor
    // ou se um objeto js esta enviando seus dados para esse Crud
} else {
    $url = '../vendor/autoload.php';
}
require $url;

$conn = new Conn();

// Quantidade de registros que deve ser retornado
$limit = 10;

// Iniciar a partir do registro
$offset = filter_input(INPUT_GET, "offset", FILTER_SANITIZE_NUMBER_INT);

// Recuperar os registros do banco de dados
$query_mensagens = "SELECT msg.id, msg.mensagem
        FROM chamada AS msg
        /*WHERE msg.id = 100*/
        ORDER BY msg.id DESC
        LIMIT :limit 
        OFFSET :offset";

// Preparar a QUERY recuperar os mensagens
$result_mensagens = $conn->Conn()->prepare($query_mensagens);

// Substituir os links da QUERY pelo valores
$result_mensagens->bindParam(':limit', $limit, PDO::PARAM_INT);
$result_mensagens->bindParam(':offset', $offset, PDO::PARAM_INT);

// Executar a QUERY recuperar os mensagens
$result_mensagens->execute();

// Recuperar a quantidade de registros retornado do banco de dados
$qtd_mensagens = $result_mensagens->rowCount();

// Recuperar quantidade de registros na tabela
$query_total_mensagens = "SELECT count(id) AS total_registros FROM chamada";

// Preparar a QUERY recuperar os mensagens
$result_total_mensagens = $conn->Conn()->prepare($query_total_mensagens);

// Executar a QUERY recuperar os mensagens
$result_total_mensagens->execute();

// Ler o total de registro
$row_total_mensagem = $result_total_mensagens->fetch(PDO::FETCH_ASSOC);

// Acessa o IF quando encontrar registro no banco de dados
if (($result_mensagens) and ($result_mensagens->rowCount() != 0)) {

    // Variável para receber os dados
    $dados = "";

    // Ler as mensagens retornada do banco de dados
    $dados = $result_mensagens->fetchAll(PDO::FETCH_ASSOC);

    // Criar o array de retorno
    $retorno = ['status' => true, 'dados' => $dados, 'qtd_mensagens' => $qtd_mensagens, 'total_mensagem' => $row_total_mensagem['total_registros']];

} else {
    // Criar o array de retorno
    $retorno = ['status' => false, 'msg' => "Isso é tudo. Você viu todas as mensagens!"];
}

// Retornar objeto de dados para o JavaScript
echo json_encode($retorno);