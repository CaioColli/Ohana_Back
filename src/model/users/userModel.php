<?php

namespace model\users;

use Exception;

use model\data\Connection;

class UserModel
{
    public static function UserCadaster($userName, $userEmail, $userCPF, $userPassword)
    {
        try {
            $db = Connection::getConnection();

            $sql = $db->prepare('
                INSERT INTO users
                (
                    User_Name,
                    User_Email,
                    User_CPF,
                    User_Password
                )
                VALUES
                (
                    :User_Name,
                    :User_Email,
                    :User_CPF,
                    :User_Password
                )
            ');

            $sql->bindValue(':User_Name', $userName);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->bindValue(':User_CPF', $userCPF);
            $sql->bindValue(':User_Password', $userPassword);

            $sql->execute();
        } catch (\Exception $err) {
            if ($err->getCode() == 23000) {
                $msg = $err->getMessage();

                if (str_contains($msg, 'User_Email')) {
                    throw new Exception('Este e-mail já está cadastrado');
                }

                if (str_contains($msg, 'User_CPF')) {
                    throw new Exception('Este CPF já está cadastrado');
                }

                throw new Exception('Dados duplicados. Já existe um registro com essas informações.');
            }

            throw new Exception('Erro ao cadastrar usuário');
        }
    }

    public static function UserLogin($userEmail)
    {
        try {
            $db = Connection::getConnection();

            $sql = $db->prepare('
                SELECT 
                    User_Email,
                    User_Password
                FROM users
                    WHERE User_Email = :User_Email
            ');

            $sql->bindValue(':User_Email', $userEmail);
            $sql->execute();

            if ($sql->rowCount() > 0) {
                return $sql->fetch();
            }
        } catch (Exception $err) {
            throw new Exception('Erro ao realizar login');
        }
    }

    public static function SetUserToken($userEmail, $userToken, $userTokenExpiration) 
    {
        try {
            $db = Connection::getConnection();

            $sql = $db->prepare('
                UPDATE users
                    SET User_Token = :User_Token,
                    User_Token_Expiration = :User_Token_Expiration
                WHERE User_Email = :User_Email
            ');

            $sql->bindValue(':User_Token', $userToken);
            $sql->bindValue(':User_Token_Expiration', $userTokenExpiration);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao realizar login' . $err->getMessage());
        }
    }

    public static function GetUserByToken($token)
    {
        try {
            $db = Connection::getConnection();

            $sql = $db->prepare('
                SELECT 
                    User_ID,
                    User_Name,
                    User_Email,
                    User_CPF,
                    User_Password,
                    User_Token,
                    User_Token_Expiration
                FROM users
                    WHERE User_Token = :User_Token
            ');

            $sql->bindValue(':User_Token', $token);
            $sql->execute();

            return $sql->fetch();
        } catch (Exception $err) {
            throw new Exception('Erro ao receber dados do usuário via token' . $err->getMessage());
        }
    }
}
