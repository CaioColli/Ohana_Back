<?php

namespace routes;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

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

            $group->group('', function (RouteCollectorProxy $group) {
        
            });
        });
    }
}