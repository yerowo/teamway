<?php

use App\Controller\ShiftsController;
use App\Controller\WorkerManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/vendor/autoload.php';


$app = AppFactory::create();
$app->addErrorMiddleware(false, true, true);
$app->addBodyParsingMiddleware();


$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
if ($scriptDir == "/") $scriptDir = "";
$app->setBasePath($scriptDir);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("It Works!");
    return $response;
});

$app->run();
