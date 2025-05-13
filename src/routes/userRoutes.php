<?php

namespace routes;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

use app\middleware\AuthTokenMiddleware;

class UserRoutes
{
    public function __construct(App $app)
    {
        $app->group('/user', function (RouteCollectorProxy $group) {
            $group->group('/cadaster', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserController::class . ':UserCadaster');
            });

            $group->group('/reset', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserController::class . ':SetResetToken');

                $group->post('/change_password', \controller\users\UserController::class . ':ResetPassword');
            });

            $group->group('/login', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserController::class . ':UserLogin');
            });

            $group->group('/edit', function (RouteCollectorProxy $group) {
                $group->patch('', \controller\users\UserController::class . ':UserEdit');
            })->add(AuthTokenMiddleware::class);
        });
    }
}