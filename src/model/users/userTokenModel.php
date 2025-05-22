<?php

namespace model\users;

use Exception;

use model\data\Connection;
use PDO;

class UserTokenModel
{
    public static function SetToken($type, $userEmail, $token, $tokenExpiration)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                DELETE FROM tokens
                WHERE User_Email = :User_Email AND Type = :Type
            ');

            $sql->bindValue(':User_Email', $userEmail);
            $sql->bindValue(':Type', $type);
            $sql->execute();

            $sql = $db->prepare('
                INSERT INTO tokens
                (
                    Type,
                    User_Email,
                    Token,
                    Token_Expiration
                )
                VALUES
                (
                    :Type,
                    :User_Email,
                    :Token,
                    :Token_Expiration
                )
            ');

            $sql->bindValue(':Type', $type);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->bindValue(':Token', $token);
            $sql->bindValue(':Token_Expiration', $tokenExpiration);

            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao salver token de reset' . $err->getMessage());
        }
    }

    public static function GetToken($resetCode, $type)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                SELECT 
                    Token,
                    User_Email
                FROM tokens
                WHERE Token_Expiration >= NOW()    
                 AND Type = :Type   
            ');

            $sql->bindValue(':Type', $type);
            $sql->execute();

            $tokens = $sql->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tokens as $token) {
                if (password_verify($resetCode, $token['Token'])) {
                    return $token;
                }
            }

            return false;
        } catch (Exception $err) {
            throw new Exception('Erro ao recuperar token de reset da senha' . $err->getMessage());
        }
    }

    public static function DeleteToken($userEmail, $type)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                DELETE FROM tokens
                WHERE User_Email = :User_Email AND Type = :Type
            ');

            $sql->bindValue(':Type', $type);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao deletar token de reset' . $err->getMessage());
        }
    }
}
