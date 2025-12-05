<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Ray\WebFormModule\FakeSessionHandler;

$handler = new FakeSessionHandler();
session_set_save_handler($handler, true);
