<?php

namespace validators\users;

use Respect\Validation\Validator as v;

class UserValidator
{
    public static function UserCadaster()
    {
        return [
            'User_Name' => v::notEmpty()->length(3, 100),
            'User_Email' => v::notEmpty()->email(),
            'User_CPF' => v::notEmpty()->digit()->cpf(),
            'User_Password' => v::notEmpty()->regex('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W)[\d\w\W]{6,}$/')
        ];
    }

    public static function UserLogin()
    {
        return [
            'User_Email' => v::notEmpty()->email(),
            'User_Password' => v::notEmpty()
        ];
    }

    public static function UserEdit()
    {
        return [
            'User_Name' => v::optional(v::notEmpty()->length(3, 100)),
            'User_Email' => v::optional(v::notEmpty()->email()),
            'User_New_Password' => v::optional(v::notEmpty()->regex('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W)[\d\w\W]{6,}$/'))
        ];
    }
}
