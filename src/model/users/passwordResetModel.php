<?php

namespace model\users;

use Exception;

use model\data\Connection;
use PDO;

class PasswordResetModel
{
    public static function SetResetToken($userEmail, $token, $tokenExpiration)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                DELETE FROM password_reset
                WHERE user_Email = :user_Email
            ');

            $sql->bindValue(':user_Email', $userEmail);
            $sql->execute();

            $sql = $db->prepare('
                INSERT INTO password_reset
                (
                    user_Email,
                    token,
                    token_Expiration
                )
                VALUES
                (
                    :user_Email,
                    :token,
                    :token_Expiration
                )
            ');

            $sql->bindValue(':user_Email', $userEmail);
            $sql->bindValue(':token', $token);
            $sql->bindValue(':token_Expiration', $tokenExpiration);

            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao salver token de reset' . $err->getMessage());
        }
    }

    public static function GetResetToken($resetCode)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                SELECT 
                    token,
                    user_Email
                FROM password_reset
                WHERE token_Expiration >= NOW()       
            ');

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

    public static function DeleteResetToken($userEmail)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                DELETE FROM password_reset
                WHERE user_Email = :user_Email
            ');

            $sql->bindValue(':user_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao deletar token de reset' . $err->getMessage());
        }
    }

    public static function SetResetPassword($userPassword, $userEmail)
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
}
