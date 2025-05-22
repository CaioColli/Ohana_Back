<?php

namespace model\data;

use PDO;

class Connection
{
    public static function GetConnection()
    {
        try {
            static $host = $_ENV['DB_HOST'];
            static $port = $_ENV['DB_PORT'];
            static $database = $_ENV['DB_DATABASE'];
            static $user = $_ENV['DB_USER'];
            static $password = $_ENV['DB_PASSWORD'];

            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (\Exception $err) {
            die('Erro ao tentar se conectar com o banco de dados: ' . $err->getMessage());
        }
    }
}
