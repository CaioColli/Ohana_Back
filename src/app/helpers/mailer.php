<?php

namespace app\helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    public static function SendEmail($email, $name, $token) 
    {
        $mail = new PHPMailer(true);
        
        try {
            $mail->isSMTP();
            $mail->SMTPAuth = true;

            $mail->Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            
            $mail->SMTPSecure = 'tls';

            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;

            $mail->setFrom($_ENV['MAIL_USERNAME'], 'OhanaTravel');
            $mail->addAddress($email, $name);
            
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Recuperar senha';
            $mail->Body = "
                <div>
                    <header style='margin-bottom: 1.5rem'>
                        <h1 style='font-size: 2rem; margin: 0; color:#ed5e32'>Olá {$name}.</h1>
                        <p style='margin: 0'>
                            Recebemos sua solicitação para recuperar sua senha de acesso a nossa plataforma.
                        </p>
                    </header>

                    <div>
                        <p style='margin: 0 0 16px 0'>
                            Seu código para recuperar sua senha de acesso é: <span style='font-size: 1.5rem; font-weight: bold; color:#ed5e32'>{$token}</span>
                        </p>

                        <p style='margin: 0 0 8px 0'>
                            Caso não solicitou a mudança de sua senha de acesso, por favor ignore este email.
                        </p>

                        <p style='margin: 0'>
                            Atenciosamente,<br>
                            Equipe OhanaTravel
                        </p>
                    </div>
                </div>
            ";
            $mail->AltBody = "Seu código de restauração é: {$token}";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception('Erro ao enviar email' . $e->getMessage());
        }
    }
}