<?php
/**
 * Created by PhpStorm.
 * User: Iriventeam
 * Date: 16/07/2019
 * Time: 11:20
 */
use \Iriven\Plugin\Sessions\PHPSession;
const DS = DIRECTORY_SEPARATOR;
$autoload =  require dirname(__DIR__) . DS .'vendor'.DS.'autoload.php';
$autoload->add('Iriven\\Plugin\\Sessions\\',dirname(__DIR__).DS.'Src');

    $session = new PHPSession();
    $session->start();
    $session->flash()->warning('bonjour');
    $session->set('role', 'parent');
    //$session->close();
    $session->flash()->display();
    echo '<pre>';
    print_r($session->all());
    exit(12);
