<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
use Doctrine\Common\Annotations\AnnotationRegistry;

$autoloader = dirname(dirname(__DIR__)) . '/vendor/autoload.php';
if (! file_exists($autoloader)) {
    echo "Composer autoloader not found: $autoloader" . PHP_EOL;
    echo "Please issue 'composer install' and try again." . PHP_EOL;
    exit(1);
}
/* @var $loader \Composer\Autoload\ClassLoader */
$loader = require $autoloader;
AnnotationRegistry::registerLoader([$loader, 'loadClass']);

return $loader;
