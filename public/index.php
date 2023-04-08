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

// Create Worker
$app->post('/workers/create', function (Request $request, Response $response, $args) {
    $request_data = $request->getParsedBody();

    $errorMsg = "";
    if (empty($request_data['first_name'])) {
        $errorMsg = "First name is required";
    } elseif (empty($request_data['last_name'])) {
        $errorMsg = "Last name is required";
    }
    if (!empty($errorMsg)) {
        $response_data['error'] = true;
        $response_data['message'] = $errorMsg;
        $response->getBody()->write(json_encode($response_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }
    $action = WorkerManager::addWorker($request_data['first_name'], $request_data['last_name']);
    if ($action['status'] == 'success') {
        $response_data['error'] = false;
        $response_data['message'] = $action['message'];
        $response_data['data']['worker_id'] = $action['worker_id'];
        $response->getBody()->write(json_encode($response_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(200);
    } else {
        $response_data['error'] = true;
        $response_data['message'] = $action['message'];
        $response->getBody()->write(json_encode($response_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(500);
    }
    return $response;
});

// Get Worker
$app->get('/workers/get', function (Request $request, Response $response, $args) {
    $response_data['error'] = false;
    $response_data['message'] = 'success';
    $response_data['data'] = (new WorkerManager())->getAllWorkers();
    $response->getBody()->write(json_encode($response_data));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->run();
