<?php 

// Definindo os tipos come restritos
declare(strict_types = 1);

namespace Api\Websocket;
use Exception;
use Api\Websocket\Conexao\Conn;

class Estrutura {

    protected $dados = [];
    protected $tabela;
    protected $conn;

    public function __construct(){
        foreach($_REQUEST as $chave => $value){
            $this->dados[$chave] = $value;
        }
        // Verificando se todos os valores enviados estão prenchidos
        foreach($this->dados as $chave => $value){
            if(is_null($value) || empty($value)){
                throw new Exception("Faltou vc prencher os dados associados a/ao {$chave}");
                
            } 
        }
        $this->conn = new Conn();
    }

    // Sempre vai ter pelo menos um valor cadastrado no banco (para  podermoss fazer a comparação dos dados)
    protected function Insert() {
        //var_dump($this->dados);
        //print_r($this->conn->Conn());
        $tabela = $this->dados["tabela"];
        $this->tabela = $tabela;
        $sql = "SELECT * FROM {$tabela}";
        if(!$stmt = $this->conn->Conn()->query($sql)) {
            throw new Exception("Erro ao buscar tabela  \n", 404);
            
        } else {
            //print_r($stmt->fetch());
            $colunsbanco = array_keys($stmt->fetch());
            // Arrancando o a chave primaria das colunas do banco para fazer a comparação
            array_pop($colunsbanco);
            //Usaremos o array_shift
            //var_dump($colunsbanco);
            array_pop($this->dados);
            /*
            // Vou criar uma fução para poupar linhas
            foreach($this->dados as $chave => $value){
                if(is_null($value) || empty($value)){
                    throw new Exception("Faltou vc prencher os dados associados a/ao {$chave}");
                    
                } 
            }*/
            $colunsComp = array_keys($this->dados);
            $colunas = implode("," , array_keys($this->dados)); 
            $valores = "'".implode("','" ,array_values($this->dados))."'";
            //print_r(array_diff($colunsbanco, $colunsComp));
            if($array = array_diff($colunsbanco, $colunsComp)){
                var_dump($array);
                $erro = implode("," , $array);
                // Erro se a pessoa tentar enviar dados não compativeis com a tabela acessada
                throw new Exception("Parece que a coluna {$erro} não foi prenchida ou não consta no nosso banco de dados \n", 31);
            } else {
                // Verificando se é a tabela com chaves primarias e vendo se o valor e igual ao do banco
                if($this->Chave_Estrangeira() == false){
                    throw new Exception("Erro, Chave primaria Falha \n", 128);
                    
                }
                for($i = 1; $i <= count(array_keys($this->dados)); $i++){
                    $count[] = "?" ;
                }
                $atributos = implode(",", $count);
                $sql = "INSERT INTO {$tabela} ({$colunas}) VALUES ($atributos)";
                $stmt = $this->conn->Conn()->prepare($sql);
                if($stmt->execute(array_values($this->dados))){
                    echo "funcionou \n";
                    echo $sql;
                } else {
                    throw new Exception("Erro ao executar inserção ao banco", 41);
                    
                }
                //$this->conn->Close();
                echo json_encode($colunas)."\n\n";
                echo json_encode($valores)."\n\n";
            }
        }
    }

    protected function Update(){

        // Verificando se todos os atributos estão presentes
        $tabela = $this->dados["tabela"];
        $this->tabela = $tabela;
        $id = ($this->dados["id"] != null || !empty($this->dados["id"]) ? " WHERE ID = {$this->dados["id"]}" : throw new Exception("Id não localizado", 95));
        $sql = "SELECT * FROM {$tabela} {$id}";
        //echo $sql;
        if(!$stmt = $this->conn->Conn()->query($sql)) {
            throw new Exception("Erro ao buscar tabela \n", 99);
            
        } else {
            // Pegando as colunas do banco para verificar se todos os dados foram entregues
            $dadosbanco = $stmt->fetch();
            $colunsbanco = array_keys($dadosbanco);
            foreach($colunsbanco as $chave){
                if(array_key_exists($chave, $this->dados)){
                    $dadosbanco[$chave] = $this->dados[$chave];
                }
            }
            //print_r($this->dados);
            // Apagando o valor da "tabela" a ser alterada
            array_pop($this->dados);
            // Adicionando todos os valores que não foram enviados para serem atualizados
            $this->dados = $dadosbanco;
            // Pegando os valores das chaves enviadas para ser comparadas
            $colunsComp = array_keys($this->dados);
            // Visualizar depois do codigo
            $valores = implode("," ,array_values($this->dados));
            //print_r(array_diff($colunsbanco, $colunsComp));
            // Verificando se os dados enviados são pertinentes a tabela em questão, garantindo que uma tabela não pertinente entre no banco
            if($array = array_diff($colunsbanco, $colunsComp)){
                var_dump($array);
                $erro = implode("," , $array);
                // Erro se a pessoa tentar enviar dados não compativeis com a tabela acessada
                throw new Exception("Parece que a coluna {$erro} não foi prenchida ou não consta no nosso banco de dados \n", 123);
            } else {
                // Verificando se é a tabela com chaves primarias e vendo se o valor e igual ao do banco
                if($this->Chave_Estrangeira() == false){
                    throw new Exception("Erro, Chave primaria Falha \n", 128);
                    
                }
                $colunas = array_keys($this->dados);
                foreach($colunas as $chave => $value){
                    $colunas[$chave] .= " = ?";
                }
                // Estou tirando a chave primaria, pois vamos coloca-la em uma parte diferente (WHERE)
                array_pop($colunas);
                $campos = implode(",", $colunas);
                /* Pegando o ultimo valor que será depois de termos tirados a coluna "tabela"
                A chave primaria que vamos usar na procura WHERE*/
                $ultimoChave = array_key_last($this->dados);
                // Preparando a pesquisa WHERE para usarmos prepare statement
                $ultimoChave .= "= ?";
                $sql = "UPDATE {$tabela} SET {$campos} WHERE {$ultimoChave}";
                // Preparando os objetos
                $stmt = $this->conn->Conn()->prepare($sql);
                // Verificação de dados
                //print_r(array_values($this->dados));
                //print_r($campos);
                //echo $sql;
                //print_r(array_values($this->dados));
                if($stmt->execute(array_values($this->dados))){
                    echo "\n funcionou \n";
                } else {
                    throw new Exception("Erro ao executar inserção ao banco \n", 41);
                    
                }
                //$this->conn->Close();
                echo json_encode($colunas)."\n\n";
                echo json_encode($valores)."\n\n";
            }
        }
    }

    // Faltou arrumar esse Read --------------------------------------X (Tenho que ver o tipo de DataTables que utilizaremos)
    protected function Read(){
        $requestData = $_REQUEST;
        $colunas = $requestData['columns']; //Obter as colunas vindas do resquest
        //Preparar o comando sql para obter os dados da categoria
        $sql = "SELECT * FROM equipamentos WHERE 1=1 ";
        //Obter o total de registros cadastrados
        $resultado = $this->conn->Conn()->query($sql);
        $qtdeLinhas = $resultado->rowCount();
        //Verificando se há filtro determinado
        $filtro = $requestData['search']['value'];
        if( !empty( $filtro ) ){
            //Montar a expressão lógica que irá compor os filtros
            //Aqui você deverá determinar quais colunas farão parte do filtro
            $sql .= " AND (ID LIKE '$filtro%' ";
            $sql .= " OR NOME LIKE '$filtro%') ";
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
        $resultData = array();
        while($row = $resultado->fetch()){
            $resultData[] = array_map('utf8_encode', $row);
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
            $sql = "SELECT * FROM CLIENTE WHERE ID = ".$requestData['ID']."";
            // preparar a querie para gerar objetos de insersao no banco de dados
            
            $resultado = $this->conn->Conn()->query($sql);
            if($resultado){
            $result = array();
            while($row = $resultado->fetch()){
                $result = array_map('utf8_encode', $row);
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
        echo json_encode($dados);
    
    
    }

    protected function Delete(){
        // Pegando a tabela do banco
        $tabela = $this->dados["tabela"];
        array_pop($this->dados);
        foreach($this->dados as $chave => $valor){
        // Operador ternario
        $where = ($valor != null || !empty($valor) ? " WHERE {$chave} = {$valor}" : "");
        $sql = "DELETE FROM {$tabela} {$where}";
        $stmt = $this->conn->Conn()->prepare($sql);
        if(!$stmt->execute()){
            throw new Exception("Não foi possivel realizar a exclusão no banco", 104);
            
        } else {
            echo "delete realizado com sucesso";
        }

        }
    }
    // Função de verificação de chave Estrangeira
    public function Chave_Estrangeira(){
        //$arrayAll = array("nome" => 'marco', "total_id_cargo" => '2', "id" => '2',"tabela" => "pessoa"); Valor teste
        $padrao = '/[^a-zA-Z0-9\s]/';
        $arrayAll = $this->dados;

        foreach($arrayAll as $chave => $valor){
            $coluna = preg_replace($padrao, ' ',$chave);
            $array = explode(" ", $coluna);
            //print_r($array);
            //$tabela = $arrayAll[array_key_last($arrayAll)];
            $tabela = $this->tabela;
            //echo $tabela;
                if(in_array("CH", $array)){
                    // Vou filtar a chave para pegar o nome da coluna da tabela que vem a chave estrangeira
                    $copArray = $array;
                    $NewArray = array_pop($copArray);
                    // Ou essa opção de chave de tabela estrangeira ou  a outra comentado
                    //$ColEstrang = implode("_",array_diff($array, [$NewArray]));
                    $NewArray = array_diff($array, [$NewArray]);
                    $indice = array_key_first($NewArray);
                    $ColEstrang = $NewArray[$indice];
                    // $arry[2] => pega a tabela da chave estrangeira /
                    $sql = "SELECT {$tabela}.* FROM {$tabela} INNER JOIN {$array[2]} ON {$valor} = {$array[2]}.{$ColEstrang}";
                    //echo "{$sql}\n"; analisando a formato do codigo
                    $stmt = $this->conn->Conn()->prepare($sql);
                    if($stmt->execute()){
                        if($stmt->rowCount() > 0){
                            return $stmt->fetchAll();
                            return True;
                        } else {
                            throw new Exception("Desculpe mais o valor {$valor} do {$ColEstrang} da Tabela {$tabela} não existe", 139);
                            return False;
                            
                        }
                    } else {
                        throw new Exception("Erro ao procurar chave estrangeira da tabela {$tabela}", 145);
                        
                    }
                } elseif(!in_array("CH", $array)) {
                    //echo "não";
                }
                
        }
        return True;
        //$this->conn->Close();
    }

    // Chamadas de Funções

    public function CallUpdate(Array $dados){
        $this->SetDados($dados);
        echo $this->Update();
    }
    public function CallInsert(Array $dados) {
        $this->SetDados($dados);
        echo $this->Insert();
    }
    public function CallRead(Array $dados){
        $this->SetDados($dados);
        echo $this->Read();
    }

    // Chadamas de Funções de Set valores (Estou usando isso por causa da forma de envio de dados do Chat)

    public function SetDados(Array $dados){
        // Verificando se todos os valores enviados estão prenchidos
        //print_r($teste);
        foreach($dados as $chave => $value){
            if(is_null($value) || empty($value)){
                throw new Exception("Faltou vc prencher os dados associados a/ao {$chave} \n\n");
                
            } 
        }
        $this->dados = $dados;
    }
}

$requestData = $_REQUEST;
print_r($requestData);
try {
    $teste = new Estrutura();
    if($requestData['operacao'] == 'read') {


    };
    if($requestData['operacao'] == 'create') {
    
    
    };
    if($requestData['operacao'] == 'update') {
    
    
    };
    if($requestData['operacao'] == 'delete') {
    
    
    };
    if($requestData['operacao'] == 'view') {
    
    
    };

} catch (Exception $e) {
    
    echo "Mensagem: ".$e->getMessage()."\n";
    echo "Codigo: ".$e->getCode()."\n";
    echo "Linha: ".$e->getLine()."\n";
    echo "Arquivo: ".$e->getFile()."\n";
}

/*
$array = array( "aolo1" =>1,"aolo2" =>2,"aolo3" =>"alo");

$number = count(array_keys($array));

echo $number;*/