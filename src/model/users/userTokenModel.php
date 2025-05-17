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
                DELETE FROM user_tokens
                WHERE user_Email = :user_Email AND type = :type
            ');

            $sql->bindValue(':user_Email', $userEmail);
            $sql->bindValue(':type', $type);
            $sql->execute();

            $sql = $db->prepare('
                INSERT INTO user_tokens
                (
                    type,
                    user_Email,
                    token,
                    token_Expiration
                )
                VALUES
                (
                    :type,
                    :user_Email,
                    :token,
                    :token_Expiration
                )
            ');

            $sql->bindValue(':type', $type);
            $sql->bindValue(':user_Email', $userEmail);
            $sql->bindValue(':token', $token);
            $sql->bindValue(':token_Expiration', $tokenExpiration);

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
                    token,
                    user_Email
                FROM user_tokens
                WHERE token_Expiration >= NOW()    
                 AND type = :type   
            ');

            $sql->bindValue(':type', $type);
            $sql->execute();

            $tokens = $sql->fetchAll(PDO::FETCH_ASSOC);

            foreach ($tokens as $token) {
                if (password_verify($resetCode, $token['token'])) {
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
                DELETE FROM user_tokens
                WHERE user_Email = :user_Email AND type = :type
            ');

            $sql->bindValue(':type', $type);
            $sql->bindValue(':user_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao deletar token de reset' . $err->getMessage());
        }
    }

    public static function SetNewPassword($userPassword, $userEmail)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                UPDATE users
                    SET User_Password = :User_Password
                WHERE User_Email = :User_Email
            ');

            $sql->bindValue(':User_Password', $userPassword);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro a mudar a senha senha' . $err->getMessage());
        }
    }

    public static function SetEmailVerified($userEmail)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                UPDATE users
                    SET Email_Verified = 1
                WHERE user_Email = :user_Email
            ');

            $sql->bindValue(':user_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao mudar a senha senha');
        }
    }
}
