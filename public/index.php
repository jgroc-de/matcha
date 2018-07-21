<?php
session_start();

require '../vendor/autoload.php';

// Instatiate the app
$config = require '../app/config.php';
$app = new \Slim\App(['settings' => $config]);

// Set up dependencies
require '../app/container.php';

// Register routes
require '../app/routes.php';

// Run!
$app->run();
