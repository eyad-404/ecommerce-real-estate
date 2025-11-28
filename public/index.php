<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Check if the application is in maintenance mode
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Load Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the application
$app = require_once __DIR__ . '/../bootstrap/app.php';

/** @var Kernel $kernel */
$kernel = $app->make(Kernel::class);

// Capture the incoming request
$request = Request::capture();

// Handle the request through the Kernel
$response = $kernel->handle($request);

// Send the response back to the browser
$response->send();

// Run terminable middleware (after sending response)
$kernel->terminate($request, $response);
