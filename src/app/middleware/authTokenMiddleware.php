<?php

namespace app\middleware;

use model\users\UserModel;
use Psr\Http\Message\ResponseInterface as PsrResponse;
use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use response\Response;

class authTokenMiddleware
{
    public function __invoke(PsrRequest $request, PsrResponse $response, $handler)
    {
        $token = $request->getHeader('Token')[0] ?? null;

        if (!$token) {
            return Response::Return401($response, 'Token não encontrado.');
        }

        try {
            $user = UserModel::GetUserByToken($token);

            $request = $request->withAttribute('user', $user);

            return $handler->handle($request);
        } catch (\Exception $e) {
            return Response::Return401($response, $e->getMessage());
        }
    }
}
