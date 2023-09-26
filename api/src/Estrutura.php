<?php 


// Definindo os tipos como restritos
declare(strict_types = 1);

namespace Api\Websocket;
use Exception;
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

class Estrutura {

    protected $dados = [];
    protected $tabela;
    protected $conn;

    public function __construct(){
        $this->conn = new Conn();
    }

    // Sempre vai ter pelo menos um valor cadastrado no banco (para  podermoss fazer a comparação dos dados)
    protected function Insert() {
        //print_r($this->conn->Conn());
        $tabela = $this->dados["tabela"];
        $this->tabela = $tabela;
        $sql = "SELECT * FROM {$tabela}";
        if(!$stmt = $this->conn->Conn()->query($sql)) {
            $dados = array(
                "type" => 'error',
                "mensagem" => 'Erro na conexao do banco ou acesso a tabela'
            );
            json_encode($dados);
            throw new Exception(implode(",",$dados), 404);
        } else {

            $colunsbanco = array_keys($stmt->fetch());
            // Arrancando o a chave primaria das colunas do banco para fazer a comparação
            array_pop($colunsbanco);
            //Usaremos o array_shift
            //var_dump($colunsbanco);
            array_pop($this->dados);
            $colunsComp = array_keys($this->dados);
            $colunas = implode("," , array_keys($this->dados)); 
            $valores = "'".implode("','" ,array_values($this->dados))."'";

            if($array = array_diff($colunsbanco, $colunsComp)){
                //var_dump($array);
                $erro = implode("," , $array);
                // Erro se a pessoa tentar enviar dados não compativeis com a tabela acessada
                $msg =  "Parece que a coluna {$erro} não foi prenchida ou não consta no nosso banco de dados";
                $dados = array(
                    "type" => 'error',
                    "mensagem" => $msg
                );
                json_encode($dados);
                throw new Exception(implode(",",$dados), 404);
            } else {
                // Verificando se é a tabela com chaves primarias e vendo se o valor e igual ao do banco
                $this->Chave_Estrangeira();

                for($i = 1; $i <= count(array_keys($this->dados)); $i++){
                    $count[] = "?" ;
                }
                $atributos = implode(",", $count);
                $sql = "INSERT INTO {$tabela} ({$colunas}) VALUES ($atributos)";
                $stmt = $this->conn->Conn()->prepare($sql);
                if($stmt->execute(array_values($this->dados))){
                    $dados = array(
                        'type' => 'success',
                        'mensagem' => 'Registro salvo com sucesso!'
                    );
                    echo json_encode($dados);
                } else {
                    $dados = array(
                        "type" => 'error',
                        "mensagem" => 'Erro ao conectar a(ao) banco'
                    );
                    json_encode($dados);
                    throw new Exception(implode(",",$dados), 41);
                }
                //$this->conn->Close();
                //echo json_encode($colunas)."\n\n";
                //echo json_encode($valores)."\n\n";
                //echo json_encode($dados);
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
            $dados = array(
                "type" => 'error',
                "mensagem" => 'Erro na conexao do banco ou acesso a tabela'
            );
            json_encode($dados);
            throw new Exception(implode(",",$dados), 99);
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
                $msg = "Dado {$erro} não consta na tabela de envio";
                $dados = array(
                    "type" => 'error',
                    "mensagem" => $msg
                );
                // Erro se a pessoa tentar enviar dados não compativeis com a tabela acessada
                json_encode($dados);
                throw new Exception(implode(",",$dados), 123);
            } else {
                // Verificando se é a tabela com chaves primarias e vendo se o valor e igual ao do banco
                $this->Chave_Estrangeira();

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

                if($stmt->execute(array_values($this->dados))){
                    //echo "\n funcionou \n";
                    $dados = array(
                        'type' => 'success',
                        'mensagem' => 'Registro salvo com sucesso!'
                    );
                    echo json_encode($dados);
                } else {
                    $dados = array(
                        "type" => 'error',
                        "mensagem" => 'Erro ao conectar a(ao) banco'
                    );
                    json_encode($dados);
                    throw new Exception(implode(",",$dados), 41);
                }
                //$this->conn->Close();
                //echo json_encode($colunas)."\n\n";
                //echo json_encode($valores)."\n\n";
                //echo json_encode($dados);
            }
        }
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
        //$arrayAll = array("nome" => 'marco', "total_ch_cargo" => '2', "id" => '2',"tabela" => "pessoa"); Valor teste
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
                    //echo "{$sql}\n";
                    $stmt = $this->conn->Conn()->prepare($sql);
                    if($stmt->execute()){
                        if($stmt->rowCount() > 0){
                            return $stmt->fetchAll();
                        } else {
                            $msg = "Desculpe mais o valor {$valor} do {$ColEstrang} da Tabela {$tabela} não existe";
                            $dados = array(
                                "type" => 'error',
                                "mensagem" => $msg
                            );
                            json_encode($dados);
                            throw new Exception(implode(",",$dados), 41);
                        }
                    } else {
                        $msg = "Erro ao procurar chave estrangeira da tabela {$tabela}";
                        $dados = array(
                            "type" => 'error',
                            "mensagem" => $msg
                        );
                        json_encode($dados);
                        throw new Exception(implode(",",$dados), 41);
                        
                    }
                } elseif(!in_array("CH", $array)) {
                    //echo "não";
                }
                
        }
        return True;
        //$this->conn->Close();
    }

    public function Remove(Array $dados, $valor) {
        $indice = array_search($valor, $dados);
        //echo $indice;
        $array = array_diff($dados, [$valor]);
        return $array;
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

    // Chadamas de Funções de Set valores (Estou usando isso por causa da forma de envio de dados do Chat)

    public function SetDados(Array $dados){
        // Verificando se todos os valores enviados estão prenchidos
        foreach($dados as $chave => $value){
            if(is_null($value) || empty($value)){
                $dados = array(
                    "type" => 'error',
                    "mensagem" => "Faltou vc prencher os dados associados a/ao {$chave}"
                );
                // Erro se a pessoa tentar enviar dados não compativeis com a tabela acessada
                json_encode($dados);
                throw new Exception(implode(",",$dados), 123);
            } 
        }        
        $this->dados = $dados;
    }
}
