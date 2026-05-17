<?php

declare(strict_types=1);

use Ray\WebFormModule\FakeSessionHandler;

require dirname(__DIR__) . '/vendor/autoload.php';

$handler = new FakeSessionHandler();
session_set_save_handler(
    [$handler, 'open'],
    [$handler, 'close'],
    [$handler, 'read'],
    [$handler, 'write'],
    [$handler, 'destroy'],
    [$handler, 'gc'],
);
