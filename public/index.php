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

// Development helper: ensure upload limits are large enough for hero GIFs
// Note: This tries to increase limits at runtime for the built-in PHP server / dev environment.
@ini_set('upload_max_filesize', '25M');
@ini_set('post_max_size', '26M');
@ini_set('memory_limit', '512M');

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
