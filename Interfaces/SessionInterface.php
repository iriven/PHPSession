<?php
/**
 * Created by PhpStorm.
 * User: Iriventeam
 * Date: 16/07/2019
 * Time: 09:03
 */

namespace Iriven\Plugin\Sessions\Interfaces;

interface SessionInterface
{
    public function count();
    public function all();
    public function set($key, $value);
    public function get($key, $default = null);
    public function has($key);
    public function remove($key);
    public function referer($AlternateUrl);
    public function flash();
    public function pull($key, $default = null);
    public function start(int $idle=6);
    public function saveReferer($value);
    public function getSessionId();
    public function isValid();
    public function regenerate();
    public function close();
    public function __set($key, $value);
    public function __get($key);
}


