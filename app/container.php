<?php
$container = $app->getContainer();

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../app/View', [
        'cache' => false //'../tmp/cache'
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO('mysql:dbname=' . $db['dbname'] . ';host=' . $db['host'], $db['user'], $db['pass']);
    $pdo->setAttribute(PdO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PdO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container['user'] = function ($container) { 
    return new \App\Model\UserModel($container);
};

$container['form'] = function ($container) { 
    return new \App\Controllers\FormController($container);
};
