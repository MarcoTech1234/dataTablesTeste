<?php

use Api\Websocket\Estrutura as Crud;
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

class Datatables {
    
    protected $conn;

    public function __construct(){
        $this->conn = new Conn();
    }
    protected function Read() : void{
        $requestData = $_REQUEST;
        //$requestData = filter_var_array($requestData, FILTER_SANITIZE_STRING);
        $colunas = $requestData['columns']; //Obter as colunas vindas do resquest
        //Preparar o comando sql para obter os dados da categoria
        $sql = "SELECT * FROM equipamento WHERE 1=1";
        //Obter o total de registros cadastrados
        $resultado = $this->conn->Conn()->query($sql);
        $qtdeLinhas = $resultado->rowCount();
        //Verificando se há filtro determinado
        $filtro = $requestData['search']['value'];
        if(!empty($filtro)){
            //Montar a expressão lógica que irá compor os filtros
            //Aqui você deverá determinar quais colunas farão parte do filtro
            $sql .= " AND (id LIKE '$filtro%' ";
            $sql .= " OR nome LIKE '$filtro%') ";
        }
        //Obter o total dos dados filtrados
        $resultado = $this->conn->Conn()->query($sql);
        $totalFiltrados = $resultado->rowCount();
        //Obter valores para ORDER BY      
        $colunaOrdem = $requestData['order'][0]['column']; //Obtém a posição da coluna na ordenação
        $ordem = $colunas[$colunaOrdem]['data']; //Obtém o nome da coluna para a ordenação
        $direcao = $requestData['order'][0]['dir']; //Obtém a direção da ordenação
        //Obter valores para o LIMIT
        $inicio = $requestData['start']; //Obtém o ínicio do limite
        $tamanho = $requestData['length']; //Obtém o tamanho do limite
        //Realizar o ORDER BY com LIMIT
        $sql .= " ORDER BY $ordem $direcao LIMIT $inicio, $tamanho ";
        $resultado = $this->conn->Conn()->query($sql);
        //echo $sql;
        $resultData = array();
        while($row = $resultado->fetch()){
            $resultData[] = array_map(null, $row);
        }
        //Monta o objeto json para retornar ao DataTable
        $dados = array(
            "draw" => intval($requestData['draw']),
            "recordsTotal" => intval($qtdeLinhas),
            "recordsFiltered" => intval($totalFiltrados),
            "data" => $resultData
        );
        echo json_encode($dados);
    }

    public function View() {
        $requestData = $_REQUEST;
            // gerar a querie de insersao no banco de dados 
            $sql = "SELECT * FROM equipamento WHERE ID = ".$requestData['ID']."";
            // preparar a querie para gerar objetos de insersao no banco de dados
            
            $resultado = $this->conn->Conn()->query($sql);
            if($resultado){
            $result = array();
            while($row = $resultado->fetch()){
                $result = array_map(null, $row);
            }
            $dados = array(
                'type' => 'view',
                'mensagem' => '',
                'dados' => $result
            );
            }
            else {
                $dados = array(
                    'type' => 'error',
                        'mensagem' => 'Erro ao abrir o registro:'
                    );  
            }
        return json_encode($dados);
    
    
    }

    public function CallRead(){
        // Datatables
        echo $this->Read();
    }
    public function CallView(){
        // Datatables
        echo $this->View();
    }

}


$requestData = $_REQUEST;
// Operando com o datatables (Fase Teste)
try {
    $data = new Datatables();
    $Crud = new Crud();
    if($requestData['operacao'] == 'read') {
        $data->CallRead();
    };
    if($requestData['operacao'] == 'create') {
        $dados = $requestData;
        $dados = $Crud->Remove($dados, $requestData['operacao']);
        $dados = $Crud->Remove($dados, $requestData['id']);
        echo $Crud->CallInsert($dados);
        
    };
    if($requestData['operacao'] == 'update') {
        $dados = $requestData;
        $dados = $Crud->Remove($dados, $requestData['operacao']);
        $Crud->CallUpdate($requestData);
    };
    if($requestData['operacao'] == 'delete') {
    
    
    };
    if($requestData['operacao'] == 'view') {
        $data->CallView();
    };

} catch (Exception $e) {
    $dados = array("type" => "", "mensagem" => "");
    $del = explode(",",$e->getMessage());
    $dados = array_map(function($e){
        return $e;
    }, $del);
    print_r($dados);
    //echo json_encode(explode(",",$e->getMessage()));
    //echo "Codigo: ".$e->getCode()."\n";
    //echo "Linha: ".$e->getLine()."\n";
    //echo "Arquivo: ".$e->getFile()."\n";
}