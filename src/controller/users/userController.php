<?php

namespace controller\users;

use DateTime;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Ramsey\Uuid\Uuid;

use model\users\UserLoginAuthModel;
use response\Response;
use validators\users\UserValidator;
use model\users\UserModel;

class UserController
{
    public function UserCadaster(PsrRequest $request, PsrResponse $response)
    {
        try {
            $data = json_decode($request->getBody()->getContents(), true);

            $rules = UserValidator::UserCadaster();

            if (!$rules['User_Name']->validate($data['User_Name'])) {
                return Response::Return422($response, 'O campo é obrigatório e deve conter de 3 a 100 caracteres!');
            }

            if (!$rules['User_Email']->validate($data['User_Email'])) {
                return Response::Return422($response, 'O campo é obrigatório e deve ser um email válido!');
            }

            if (!$rules['User_CPF']->validate($data['User_CPF'])) {
                return Response::Return422($response, 'O campo é obrigatório e deve ser um CPF válido!');
            }

            if (!$rules['User_Password']->validate($data['User_Password'])) {
                return Response::Return422($response, 'O campo é obrigatório e deve conter de no mínimo 6 caracteres contento 1 letra e 1 caractere especial!');
            }

            $protectedPassword = password_hash($data['User_Password'], PASSWORD_DEFAULT);

            UserModel::UserCadaster(
                $data['User_Name'],
                $data['User_Email'],
                $data['User_CPF'],
                $protectedPassword
            );

            return Response::Return201($response, 'Cadastro realizado com sucesso!');
        } catch (\Exception $err) {
            return Response::Return400($response, $err->getMessage());
        }
    }

    public function UserLogin(PsrRequest $request, PsrResponse $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $dataBaseLogin = UserModel::UserData($data['User_Email']);

        // $userToken = $dataBaseLogin['User_Token'];

        $rules = UserValidator::UserLogin();

        if (!$rules['User_Email']->validate($data['User_Email'])) {
            return Response::Return400($response, 'O campo é obrigatório e deve ser um email válido!');
        } elseif ($dataBaseLogin['User_Email'] != $data['User_Email']) {
            return Response::Return400($response, 'Esse email não possui cadastro');
        }

        if (!$rules['User_Password']->validate($data['User_Password'])) {
            return Response::Return400($response, 'O campo é obrigatório.');
        } elseif (!password_verify($data['User_Password'], $dataBaseLogin['User_Password'])) {
            return Response::Return400($response, 'Senha ou Email incorreto!');
        }

        $userToken = Uuid::uuid4()->toString();

        $date = new DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $date->modify('+24 hours');

        UserLoginAuthModel::SetLoginToken(
            $data['User_Email'],
            $userToken,
            $date->format('Y-m-d H:i:s')
        );

        return Response::Return200($response, $userToken);
    }

    public function UserEdit(PsrRequest $request, PsrResponse $response)
    {
        $user = $request->getAttribute('user');

        $data = json_decode($request->getBody()->getContents(), true);

        $rules = UserValidator::UserEdit();

        if (empty($data['User_Password'])) {
            return Response::Return400($response, 'é obrigatório inserir a senha para efeturar alterações!');
        }

        if (!password_verify($data['User_Password'], $user['User_Password'])) {
            return Response::Return400($response, 'Senha incorreta!');
        }

        if (!$rules['User_Name']->validate($data['User_Name'])) {
            return Response::Return422($response, 'O campo é obrigatório e deve conter de 3 a 100 caracteres!');
        }

        if (!$rules['User_Email']->validate($data['User_Email'])) {
            return Response::Return422($response, 'O campo é obrigatório e deve ser um email válido!');
        }

        if (!$rules['User_New_Password']->validate($data['User_New_Password'])) {
            return Response::Return422($response, 'O campo é obrigatório e deve conter de no mínimo 6 caracteres contento 1 letra e 1 caractere especial!');
        }

        $userName = $data['User_Name'] ?? $user['User_Name'];
        $userEmail = $data['User_Email'] ?? $user['User_Email'];

        if (empty($data['User_New_Password'])) {
            $protectedPassword = $user['User_Password'];
        } else {
            $protectedPassword = password_hash($data['User_New_Password'], PASSWORD_DEFAULT);
        }

        UserModel::UserEdit(
            $user['User_ID'],
            $userName,
            $userEmail,
            $protectedPassword
        );

        return Response::Return200($response, 'Perfil editado com sucesso!');
    }

    public function UserDelete(PsrRequest $request, PsrResponse $response)
    {
        $user = $request->getAttribute('user');

        $data = json_decode($request->getBody()->getContents(), true);

        $rules = UserValidator::PasswordValidation();

        if(!$rules['User_Password']->validate($data['User_Password'])) {
            return Response::Return400($response, 'O campo é obrigatório para deletar conta.');
        }

        if (!password_verify($data['User_Password'], $user['User_Password'])) {
            return Response::Return400($response, 'Senha incorreta!');
        }

        UserModel::UserDelete($user['User_ID']);

        return Response::Return200($response, 'Conta deletada com sucesso!');
    }
}
