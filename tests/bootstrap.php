<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
use Doctrine\Common\Annotations\AnnotationRegistry;

/* @var $loader \Composer\Autoload\ClassLoader */
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

$handler = new \Ray\WebFormModule\FakeSessionHandler();
session_set_save_handler(
    [$handler, 'open'],
    [$handler, 'close'],
    [$handler, 'read'],
    [$handler, 'write'],
    [$handler, 'destroy'],
    [$handler, 'gc']
);
