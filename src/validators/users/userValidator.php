<?php

namespace validators\users;

use Respect\Validation\Validator as v;

class UserValidator
{
    public static function MailValidation()
    {
        return [
            'User_Email' => v::notEmpty()->email()
        ];
    }

    public static function PasswordValidation()
    {
        return [
                'User_Password' => v::notEmpty()->regex('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W)[\d\w\W]{6,}$/')
        ];
    }

    public static function UserCadaster()
    {
        return array_merge(
            self::MailValidation(),
            self::PasswordValidation(),
            [
                'User_Name' => v::notEmpty()->length(3, 100),
                'User_CPF' => v::notEmpty()->digit()->cpf()
            ]
        );
    }

    public static function UserLogin()
    {
        return array_merge(
            self::MailValidation(),
            self::PasswordValidation()
        );
    }

    public static function UserEdit()
    {
        return array_merge(
            self::MailValidation(),
            [
                'User_Name' => v::optional(v::notEmpty()->length(3, 100)),
                'User_New_Password' => v::optional(v::notEmpty()->regex('/^(?=.*\d)(?=.*[a-zA-Z])(?=.*\W)[\d\w\W]{6,}$/'))
            ]
        );
    }

    public static function ResetPassword() 
    {
        return array_merge(
            self::PasswordValidation(),
            [
                'Reset_Code' => v::notEmpty()
            ]
        );
    }
}
