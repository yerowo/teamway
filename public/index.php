<?php

use App\Controller\ShiftsClass;
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
    $response->getBody()->write(file_get_contents(__DIR__ . '/index.html'));
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

// Create Shift
$app->post('/shifts/create', function (Request $request, Response $response, $args) {
    $request_data = $request->getParsedBody();
    $errorMsg = "";
    if (empty($request_data['worker_id'])) {
        $errorMsg = "Worker ID is required";
    } elseif (empty($request_data['start_time'])) {
        $errorMsg = "Start Time is required";
    } elseif (empty($request_data['start_date'])) {
        $errorMsg = "Date is required";
    }

    $startTime = $request_data['start_time'];
    if (!in_array($startTime, ['0:00', '8:00', '16:00'])) {
        $errorMsg = "Start time must be between '0:00', '8:00', '16:00'";
    }

    $worker = (new ShiftsClass($request_data['worker_id']));
    if (!$worker->exists()) {
        $errorMsg = "Invalid worker id";
    }

    if (!empty($errorMsg)) {
        $response_data['error'] = true;
        $response_data['message'] = $errorMsg;
        $response->getBody()->write(json_encode($response_data));
        return $response
            ->withHeader('Content-type', 'application/json')
            ->withStatus(400);
    }

    $action = $worker->addShift($request_data['start_date'], $request_data['start_time']);
    if ($action['status'] == 'success') {
        $response_data['error'] = false;
        $response_data['message'] = $action['message'];
        $response_data['data']['worker_id'] = $action['worker_id'];
        $response_data['data']['shift_id'] = $action['data'];
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

// Get shifts
$app->get('/shifts/get', function (Request $request, Response $response, $args) {
    $response_data['error'] = false;
    $response_data['message'] = 'success';
    $response_data['data'] = (new WorkerManager())->getAllShifts();
    $response->getBody()->write(json_encode($response_data));
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(200);
});

$app->run();
