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


/**
 * https://github.com/rcastera/Session-Class
 *
 * This is a Session class for you guessed it, managing sessions.
 * Back in the day, when there weren't many open source frameworks on the market, developers had to code things from scratch to achieve certain functionality.
 * This class's inception, is the from one of those instances. With this class you can easily time-out your authenticated users after a specified interval.

Features
Protects against fixation attacks by regenerating the ID periodically.
Prevents session run conditions caused by rapid concurrent connections (such as when Ajax is in use).
Locks a session to a user agent and ip address to prevent theft.
Supports users behind proxies by identifying proxy headers in requests.
supports PHP objects vars storage
 * */
//<script src="https://gist.github.com/iriven/0463b61de959be2fe11f2ade387fca65.js"></script>