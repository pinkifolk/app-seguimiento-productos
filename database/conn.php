<?php

class Database{
    private static $instancia = null;
    private $conn;
    private $host = "localhost";
    private $user = "usuarios";
    private $pass = "clave";
    private $db = "basededatos";
    
    
    private function __construct(){
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->conn->connect_error) {
            die("Error en la conexion a la base de datos". $this->conn->connect_error);
        } 
        $this->conn->set_charset("utf8");
    }
    public static function obtenerInstancia(){
        if(!self::$instancia){
            self::$instancia = new Database();
        }
        return self::$instancia;
    }
    
    public function obtenerConexion(){
        return $this->conn;
    }
}

