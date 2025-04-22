<?php 

namespace app;

use Slim\Factory\AppFactory;

use routes\UserRoutes;

class App {
    public static function AppRun()
    {
        $app = AppFactory::create();

        $app->addErrorMiddleware(true, true, true);

        new UserRoutes($app);

        $app->run();
    }
}