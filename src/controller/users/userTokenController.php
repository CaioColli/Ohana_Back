<?php

namespace controller\users;

use DateTime;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;

use app\helpers\Mailer;
use model\users\UserModel;
use model\users\UserTokenModel;
use model\users\UserValidationModel;
use response\Response;
use validators\users\UserValidator;

class UserTokenController
{
    public function SetResetToken(PsrRequest $request, PsrResponse $response)
    {
        $data = json_decode($request->getBody()->getContents(), true);

        $usersData = UserValidationModel::GetCadastersEmails();
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

        UserTokenModel::SetToken(
            'Reset_Password',
            $data['User_Email'],
            $tokenHash,
            $date->format('Y-m-d H:i:s')
        );

        Mailer::SendResetPasswordEmail($user['User_Email'], $user['User_Name'], $tokenReset);

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

        $tokenCode = UserTokenModel::GetToken($data['Reset_Code'], 'Reset_Password');

        if (!$tokenCode) {
            return Response::Return400($response, 'Código de recuperação inválido ou expirado!');
        }

        $protectedPassword = password_hash($data['User_Password'], PASSWORD_DEFAULT);

        UserModel::SetNewPassword($protectedPassword, $tokenCode['user_Email']);
        UserTokenModel::DeleteToken($tokenCode['user_Email'], 'Reset_Password');

        return Response::Return200($response, 'Senha mudada com sucesso!');
    }

    public function SetVerifyEmailToken(PsrRequest $request, PsrResponse $response)
    {
        $user = $request->getAttribute('user');

        $tokenCode = substr(bin2hex(random_bytes(4)), 0, 7);

        $tokenHash = password_hash($tokenCode, PASSWORD_DEFAULT);

        $date = new DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $date->modify('+1 hour');

        UserTokenModel::SetToken(
            'Verify_Email',
            $user['User_Email'],
            $tokenHash,
            $date->format('Y-m-d H:i:s')
        );

        Mailer::SendVerificationEmail($user['User_Email'], $user['User_Name'], 'http://localhost:8000/user/verify_email/confirm?code=' . $tokenCode);

        return Response::Return200($response, 'Requisição enviada com sucesso!');
    }

    public function VerifyEmail(PsrRequest $request, PsrResponse $response) 
    {
        $params = $request->getQueryParams();

        if (empty($params['code'])) {
            return Response::Return400($response, 'Código não informado.');
        }

        $code = $params['code'];

        $tokenData = UserTokenModel::GetToken($code, 'Verify_Email');

        if (!$tokenData) {
            return Response::Return400($response, 'Código inválido ou expirado!');
        }

        UserModel::SetEmailVerified($tokenData['user_Email']);
        UserTokenModel::DeleteToken($tokenData['user_Email'], 'Verify_Email');

        return Response::Return200($response, 'Email verificado com sucesso!');
    }
}
