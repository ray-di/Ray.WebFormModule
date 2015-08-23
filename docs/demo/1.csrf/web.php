<?php

use Ray\Di\Injector;
use Ray\FormModule\Controller;
use Ray\FormModule\MyModule;

load: {
    /* @var $loader \Composer\Autoload\ClassLoader */
    $loader = require dirname(__DIR__) . '/autoload.php';
    $loader->addPsr4('Ray\FormModule\\' ,__DIR__);
}

/** @var $controller Controller */
$controller = (new Injector(new MyModule()))->getInstance(Controller::class);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = $controller->createUser($_POST)->response;
} else {
    $response = $controller->indexAction()->response;
}

include __DIR__ . '/html.php';

exit(0);
