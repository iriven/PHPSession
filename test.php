<?php
/**
 * Created by PhpStorm.
 * User: Iriventeam
 * Date: 16/07/2019
 * Time: 11:20
 */

use \Iriven\Plugin\Sessions\Session;

spl_autoload_register(function($className) {
    $ds=DIRECTORY_SEPARATOR ;
    $FQNS = __DIR__. $ds .str_replace('\\', $ds, $className) . '.php';
    $fakeNS='Iriven'.$ds.'Plugin'.$ds.'Sessions\\';
    $file= str_replace($fakeNS, '', $FQNS);
    if(file_exists($file))
        require $file;
    return false;
});


    $session = new Session();
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