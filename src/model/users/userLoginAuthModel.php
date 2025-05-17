<?php

namespace model\users;

use Exception;

use model\data\Connection;

class UserLoginAuthModel
{
    public static function SetLoginToken($userEmail, $userToken, $userTokenExpiration)
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
}
