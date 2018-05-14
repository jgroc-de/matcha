<?php
$container = $app->getContainer();

/**
 * twig container
 */
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../app/View', [
        'cache' => false, //'../tmp/cache',
        'debug' => true
    ]);
    $basePath = rtrim(str_ireplace(
        'index.php',
        '',
        $container['request']->getUri()->getBasePath()),
        '/'
    );
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    return $view;
};

/**
 * database container
 */
$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['dbCreate'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO('mysql:host=' . $db['host'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['user'] = function ($container) { 
    return new \App\Model\UserModel($container);
};

$container['form'] = function ($container) { 
    return new \App\Controllers\FormController($container);
};

$container['validator'] = function ($container) {
    return new \App\Lib\Validator();
};

$container['debug'] = function ($container) {
    return new \App\Lib\Debug();
};

$container['faker'] = function ($container) {
    return new \App\Model\FakerModel($container);
};

$container['fake'] = function () {
    $faker = Faker\Factory::create();
    return $faker;
};

$container['setup'] = function ($container) {
    return new \App\Model\SetupModel($container);
};
