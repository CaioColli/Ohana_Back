<?php

namespace controller\users;

use app\helpers\Mailer;
use DateTime;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Ramsey\Uuid\Uuid;

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

    public function SetResetToken(PsrRequest $request, PsrResponse $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $usersData = UserModel::GetCadastersEmails();
        $user = null;

        foreach ($usersData as $u) {
            if ($u['User_Email'] === $data['User_Email']) {
                $user = $u;
                break;
            }
        }

        $rules = UserValidator::MailValidation();

        if (!$rules['User_Email']->validate($data['User_Email'])) {
            return Response::Return400($response, 'O campo é obrigatório e deve ser um email válido!');
        }

        if (!$user) {
            return Response::Return400($response, 'Esse email não possui cadastro');
        }

        $tokenReset = substr(bin2hex(random_bytes(4)), 0, 7);

        $tokenHash = password_hash($tokenReset, PASSWORD_DEFAULT);

        $date = new DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $date->modify('+1 hour');

        UserModel::SetResetToken(
            $data['User_Email'],
            $tokenHash,
            $date->format('Y-m-d H:i:s')
        );

        Mailer::SendEmail($user['User_Email'], $user['User_Name'], $tokenReset);

        return Response::Return200($response, 'Código de recuperação enviado para o email: ' . $data['User_Email']);
    }

    public function ResetPassword(PsrRequest $request, PsrResponse $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $rules = UserValidator::ResetPassword();

        if (!$rules['Reset_Code']->validate($data['Reset_Code'])) {
            return Response::Return400($response, 'Por favor, insira o código de recuperação!');
        }

        if (!$rules['User_Password']->validate($data['User_Password'])) {
            return Response::Return400($response, 'Para uma nova senha é obrigatório que ela contenha 6 caracteres contento 1 letra e 1 caractere especial!');
        }

        $tokenCode = UserModel::GetResetToken($data['Reset_Code']);

        if (!$tokenCode) {
            return Response::Return400($response, 'Código de recuperação inválido ou expirado!');
        }

        $protectedPassword = password_hash($data['User_Password'], PASSWORD_DEFAULT);

        UserModel::SetResetPassword($protectedPassword, $tokenCode['user_Email']);
        UserModel::DeleteResetToken($tokenCode['user_Email']);

        return Response::Return200($response, 'Senha mudada com sucesso!');
    }

    public function UserLogin(PsrRequest $request, PsrResponse $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $dataBaseLogin = UserModel::UserData($data['User_Email']);

        $userToken = $dataBaseLogin['User_Token'];

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

        UserModel::SetUserToken(
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
}
