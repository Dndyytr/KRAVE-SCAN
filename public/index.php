<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Debug: Real Exception Caught</h1>';
    echo '<p><strong>Message:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
    echo '<p><strong>File:</strong> '.htmlspecialchars($e->getFile()).' on line '.$e->getLine().'</p>';
    echo '<h2>Stack Trace:</h2>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
    if ($previous = $e->getPrevious()) {
        echo '<h2>Previous Exception:</h2>';
        echo '<p><strong>Message:</strong> '.htmlspecialchars($previous->getMessage()).'</p>';
        echo '<p><strong>File:</strong> '.htmlspecialchars($previous->getFile()).' on line '.$previous->getLine().'</p>';
        echo '<pre>'.htmlspecialchars($previous->getTraceAsString()).'</pre>';
    }

    // Also output to PHP system log (Vercel console)
    error_log('DEBUG EXCEPTION: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
    if ($previous) {
        error_log('PREVIOUS EXCEPTION: '.$previous->getMessage().' in '.$previous->getFile().':'.$previous->getLine());
    }
}
