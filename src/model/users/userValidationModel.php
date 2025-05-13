<?php

namespace model\users;

use Exception;

use model\data\Connection;
use PDO;

class UserValidationModel
{
    public static function GetCadastersEmails()
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                SELECT 
                    User_Name,
                    User_Email
                FROM users
            ');

            $sql->execute();
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $err) {
            throw new Exception('Erro ao recuperar emails' . $err->getMessage());
        }
    }
}
