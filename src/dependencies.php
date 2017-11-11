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
$confSQL_Auth = $settings['settings']['confSQL_Auth'];

try{    $bdd_Auth = new PDO('mysql:host='.$confSQL_Auth['sql_host'].';dbname='.$confSQL_Auth['sql_db'].';charset=utf8',$confSQL_Auth['sql_user'], $confSQL_Auth['sql_pass']);} // WTF
catch(Exception $e){        die('Erreur : '.$e->getMessage());};

try{    $bdd = new PDO('mysql:host='.$confSQL['sql_host'].';dbname='.$confSQL['sql_db'].';charset=utf8',$confSQL['sql_user'], $confSQL['sql_pass']);} // WTF
catch(Exception $e){        die('Erreur : '.$e->getMessage());};

$Auth = new \Shotgun\Auth($settings['settings']['casUrl']);
