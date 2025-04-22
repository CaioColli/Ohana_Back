<?php

namespace model\data;

use PDO;

class Connection
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

            return $pdo;
        } catch (\Exception $err) {
            die('Erro ao tentar se conectar com o banco de dados: ' . $err->getMessage());
        }
    }
}
