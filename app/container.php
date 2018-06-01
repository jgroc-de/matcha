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

/**
 * @class UserModel
 */
$container['user'] = function ($container) { 
    return new \App\Model\UserModel($container);
};

/**
 * @class FriendsModel
 */
$container['friends'] = function ($container) { 
    return new \App\Model\FriendsModel($container);
};

/**
 * @class TagModel
 */
$container['tag'] = function ($container) {
    return new \App\Model\TagModel($container);
};

/**
 * etc…
 */
$container['form'] = function ($container) { 
    return new \App\Controllers\FormChecker($container);
};

$container['validator'] = function ($container) {
    return new \App\Lib\Validator();
};

$container['debug'] = function ($container) {
    return new \App\Lib\Debug();
};

$container['mail'] = function () {
    return new \App\Lib\MailSender();
};

$container['flash'] = function () {
    return new \App\Lib\FlashMessage();
};

$container['notFoundHandler'] = function ($container) {
    return new \App\Lib\CustomError($container);
};

$container['notAllowedHandler'] = function ($container) {
    return new \App\Lib\CustomError($container);
};
