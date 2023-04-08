<?php

/*
 * This file is part of the polyas-verifier project.
 *
 * (c) Florian Moser <git@famoser.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Famoser\PolyasVerification\Receipt\RouteFactory;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
$isDevMode = 'dev' === $_SERVER['APP_ENV'];
if ($isDevMode) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: *');
    header('Access-Control-Allow-Methods: GET, PUT, OPTIONS, DELETE, POST');
    if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
        header('Allow: GET, PUT, OPTIONS, DELETE, POST');
        exit;
    }
}

if (str_starts_with($_SERVER['REQUEST_URI'], '/api')) {
    $app = AppFactory::create();
    $app->addRoutingMiddleware();

    $logger = new Logger('app');
    $streamHandler = new StreamHandler(__DIR__.'/../var/'.$_SERVER['APP_ENV'].'.log', Level::Error);
    $logger->pushHandler($streamHandler);
    $app->addErrorMiddleware($isDevMode, true, true, $logger);

    $app->group('/api', function (RouteCollectorProxy $route) {
        RouteFactory::addRoutes($route);
    });
    $app->run();
    exit;
} elseif ($isDevMode) {
    // when developing locally, use the server provided by vite
    header('Location: http://localhost:5173/');
} else {
    include 'index.html';
}
