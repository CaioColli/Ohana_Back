<?php

namespace model\users;

use Exception;

use model\data\Connection;
use PDO;

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

    public static function UserData($userEmail)
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

            return $sql->fetch();
        } catch (Exception $err) {
            throw new Exception('Erro ao realizar login');
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
                    Email_Verified,
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

    public static function UserEdit($userID, $userName, $userEmail, $userNewPassword)
    {
        try {
            $db = Connection::getConnection();

            $sql = $db->prepare('
                UPDATE users
                    SET User_Name = :User_Name,
                    User_Email = :User_Email,
                    User_Password = :User_Password
                WHERE User_ID = :User_ID
            ');

            $sql->bindValue(':User_ID', $userID);
            $sql->bindValue(':User_Name', $userName);
            $sql->bindValue(':User_Email', $userEmail);
            $sql->bindValue(':User_Password', $userNewPassword);
            $sql->execute();

            $sql = $db->prepare('
                UPDATE users
                    SET Email_Verified = 0
                WHERE User_ID = :User_ID
            ');

            $sql->bindValue(':User_ID', $userID);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao tentar editar usuário' . $err->getMessage());
        }
    }

    public static function GetUserImage($userID)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                SELECT User_Image
                FROM users
                WHERE User_ID = :User_ID
            ');

            $sql->bindValue(':User_ID', $userID);
            $sql->execute();

            return $sql->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $err) {
            throw new Exception('Erro ao tentar resgatar o caminho da imagem do usuário');
        }
    }

    public static function UserPostImage($userID, $patch)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                UPDATE users
                SET User_Image = :User_Image
                WHERE User_ID = :User_ID
            ');

            $sql->bindValue(':User_ID', $userID);
            $sql->bindValue(':User_Image', $patch);
            $sql->execute();

            return true;
        } catch (Exception $err) {
            throw new Exception('Erro ao salvar caminho da imagem no banco de dados' . $err->getMessage());
        }
    }

    public static function UserDelete($userID)
    {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                DELETE FROM users
                WHERE User_ID = :User_ID
            ');

            $sql->bindValue(':User_ID', $userID);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao tentar deletar usuário' . $err->getMessage());
        }
    }

    public static function UserLogout($userID) {
        try {
            $db = Connection::GetConnection();

            $sql = $db->prepare('
                UPDATE users
                    SET User_Token = NULL,
                        User_Token_Expiration = NULL
                WHERE User_ID = :User_ID 
            ');
            
            $sql->bindValue(':User_ID', $userID);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao tentar fazer logout' . $err->getMessage());
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
                WHERE User_Email = :User_Email
            ');

            $sql->bindValue(':User_Email', $userEmail);
            $sql->execute();
        } catch (Exception $err) {
            throw new Exception('Erro ao mudar a senha senha');
        }
    }
}
