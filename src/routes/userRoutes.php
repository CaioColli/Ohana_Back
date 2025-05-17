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

            $group->group('/login', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserController::class . ':UserLogin');
            });

            $group->group('/verify_email', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserTokenController::class . ':SetVerifyEmailToken')->add(AuthTokenMiddleware::class);

                $group->get('/confirm', \controller\users\UserTokenController::class . ':VerifyEmail');
            });

            $group->group('/reset', function (RouteCollectorProxy $group) {
                $group->post('', \controller\users\UserTokenController::class . ':SetResetToken');

                $group->post('/change_password', \controller\users\UserTokenController::class . ':ResetPassword');
            });

            $group->group('/edit', function (RouteCollectorProxy $group) {
                $group->patch('', \controller\users\UserController::class . ':UserEdit');
            })->add(AuthTokenMiddleware::class);

            $group->group('/delete', function (RouteCollectorProxy $group) {
                $group->delete('', \controller\users\UserController::class . ':UserDelete');
            })->add(AuthTokenMiddleware::class);
        });
    }
}