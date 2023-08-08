<?php

namespace Api\Websocket\Conexao;

use PDO;
use PDOException;

    class Conn {

        protected $conn;

        // configurações da conexão do banco 
        private $dbname = "tcc";
        private $hostname = "localhost";
        private $username =  "root";
        private $pwd = "";

        // Os modos de atributos do banco de dados
        public $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_EMPTY_STRING,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]; 
        
        public function __construct() {
            // nao vai fazer nada ao ser instanciado
        }

        public function Conn() {
            try {
                if (!isset($this->conn)) {
                    $this->conn = new PDO('mysql:host=' .$this->hostname. ';dbname=' .$this->dbname, $this->username, $this->pwd, $this->options);
                    //echo "Teste de conexao";
                }
            } catch (PDOException $e) {
                echo "Erro: " . $e->getMessage() . "<br>";
                echo "Erro na linha: " . $e->getLine() . "<br>";
                die();
            }
        return $this->conn;
        }
    }