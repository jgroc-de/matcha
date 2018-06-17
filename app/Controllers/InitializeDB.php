<?php
namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class InitializeDB extends \App\Constructor
{
    public function route(Request $request, Response $response, array $args)
    {
        $db = $this->container['settings']['db'];
        $this->debug->ft_print($db);
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

