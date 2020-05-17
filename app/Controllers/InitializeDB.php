<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class InitializeDB extends Route
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $db = $this->container['settings']['db'];
        $pdo = new \PDO('mysql:host=' . $db['host'], $db['user'], $db['pass']);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->exec('DROP DATABASE IF EXISTS ' . $db['dbname']);
        $pdo->exec('CREATE DATABASE ' . $db['dbname']);
        $pdo->exec('USE ' . $db['dbname']);
        $file = file_get_contents(__DIR__ . '/../../database/matcha.sql');
        $this->db->exec($file);
    }
}
