<?php

namespace app\middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use model\users\UserModel;

class AuthTokenMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $token = $request->getHeader('Token')[0] ?? null;

        if (!$token) {
            return $this->DenyAcess('Token não econtrado!');
        }

        $userData = UserModel::GetUserByToken($token);
        $userToken = $userData['User_Token'];

        if ($token != $userToken) {
            return $this->DenyAcess('Token inválido!');
        }

        $currentTime = new \DateTime('now', new \DateTimeZone('America/Sao_Paulo'));
        $tokenExpiration = new \DateTime($userData['User_Token_Expiration'], new \DateTimeZone('America/Sao_Paulo'));

        if ($currentTime > $tokenExpiration) {
            return $this->DenyAcess('Token expirado!');
        }

        try {
            $user = UserModel::GetUserByToken($token);

            $request = $request->withAttribute('user', $user);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return $this->DenyAcess($e->getMessage());
        }
    }

    public function DenyAcess($message)
    {
        $response = new \Slim\Psr7\Response();
        $response = $response->withStatus(401);

        $response->getBody()->write(json_encode([
            'status' => 401,
            'message' => 'Unauthorized',
            'data' => $message,
        ]));

        return $response;
    }
}
