<?php
// DIC configuration

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// view renderer
$container['flash'] = function ($c) {
    return new \Slim\Flash\Messages();
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};

///////////////////////////
// Autre initialisations //
///////////////////////////


$confSQL = $settings['settings']['confSQL'];

try{    $bdd = new PDO('mysql:host=localhost;dbname=Springfestival;charset=utf8', 'root', '');}
catch(Exception $e){        die('Erreur : '.$e->getMessage());}

$Auth = new \Shotgun\Auth($settings['settings']['casUrl']);
