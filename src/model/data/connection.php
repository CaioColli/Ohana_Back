<?php

namespace App\Model\Data;

use PDO;

class connection
{
    private static $host = 'localhost';
    private static $port = '3306';
    private static $database = 'ohana';
    private static $user = 'root';
    private static $password = '31053105Caio@';

    public static function GetConnection()
    {
        try {
            $pdo = new PDO("mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$database, self::$user, self::$password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "Conexão realizada com sucesso!";
        } catch (\Exception $err) {
            die('Erro ao tentar se conectar com o banco de dados: ' . $err->getMessage());
        }
    }
}
