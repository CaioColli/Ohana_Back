<?php 

namespace App;

use Slim\Factory\AppFactory;

class App {
    public static function AppRun()
    {
        $app = AppFactory::create();

        $app->addErrorMiddleware(true, true, true);

        $app->run();
    }
}