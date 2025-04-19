<?php

namespace App\routes;

use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class UserRoutes
{
    public function __construct(App $app)
    {
        $app->group('/user', function (RouteCollectorProxy $group) {
            $group->group('/cadaster', function (RouteCollectorProxy $group) {
        
            });

            $group->group('/login', function (RouteCollectorProxy $group) {
        
            });

            $group->group('', function (RouteCollectorProxy $group) {
        
            });
        });
    }
}