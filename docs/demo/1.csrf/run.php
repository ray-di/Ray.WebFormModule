<?php
/**
 * This file is part of the Ray.WebFormModule package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
$loader = require dirname(__DIR__) . '/autoload.php';
$loader->addPsr4('Ray\WebFormModule\\', __DIR__);
use Aura\Input\Exception\CsrfViolation;
use Ray\Di\Injector;
use Ray\WebFormModule\Controller;
use Ray\WebFormModule\MyModule;

/** @var $controller Controller */
$controller = (new Injector(new MyModule))->getInstance(Controller::class);

try {
    $controller->createUser(['name' => 'bear', 'message' => 'hello']);
} catch (CsrfViolation $e) {
    echo 'Anti CSRF works !' . PHP_EOL;
    exit;
}
echo 'Anti CSRF DOES NOT works !' . PHP_EOL;
//$works = $controller->response['body'] == 'create bear';
//echo ($works ? 'It works!' : 'It DOES NOT work!') . PHP_EOL;
