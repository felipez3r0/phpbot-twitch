<?php
namespace App;

class MysqlClient
{
    static $host = DB_HOST;
    static $user = DB_USER;
    static $passw = DB_PASSWORD;
    static $database = DB_NAME;

    public $db;

    public function __construct(){
        $this->db = mysqli_connect(self::$host,self::$user,self::$passw,self::$database);

        if(!$this->db){
            echo "Erro ao conectar no Mysql.".PHP_EOL;
            echo "Erro: ".mysqli_connect_error();
        }
    }
}