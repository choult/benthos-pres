<?php

require_once(__DIR__ . '/vendor/autoload.php');

// setup logger

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Formatter\JsonFormatter;

function getLogger(string $name, string $path): Logger
{
    $log = new Logger($name);
    $formatter = new JsonFormatter();
    $handler = new StreamHandler($path, Logger::DEBUG);
    $handler->setFormatter($formatter);
    $log->pushHandler($handler);

    return $log;
}
