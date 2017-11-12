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


$confSQL_shotgun = $settings['settings']['confSQL_global']['confSQL_shotgun'];
$confSQL_Auth = $settings['settings']['confSQL_global']['confSQL_Auth'];
$confSQL_payicam = $settings['settings']['confSQL_global']['confSQL_payicam'];

try{    $bdd_Auth = new PDO('mysql:host='.$confSQL_Auth['sql_host'].';dbname='.$confSQL_Auth['sql_db'].';charset=utf8',$confSQL_Auth['sql_user'], $confSQL_Auth['sql_pass'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));} // WTF
catch(Exception $e){        die('Erreur : '.$e->getMessage());};

try{    $bdd = new PDO('mysql:host='.$confSQL_shotgun['sql_host'].';dbname='.$confSQL_shotgun['sql_db'].';charset=utf8',$confSQL_shotgun['sql_user'], $confSQL_shotgun['sql_pass'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));} // WTF
catch(Exception $e){        die('Erreur : '.$e->getMessage());};


try{    $bdd_payicam = new PDO('mysql:host='.$confSQL_payicam['sql_host'].';dbname='.$confSQL_payicam['sql_db'].';charset=utf8',$confSQL_payicam['sql_user'], $confSQL_payicam['sql_pass'],array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));} // WTF
catch(Exception $e){        die('Erreur : '.$e->getMessage());};

$Auth = new \Shotgun\Auth($settings['settings']['casUrl']);
