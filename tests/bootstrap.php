<?php

declare(strict_types=1);

use Ray\WebFormModule\FakeSessionHandler;

/**
 * This file is part of the Ray.WebFormModule package.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$handler = new FakeSessionHandler();
session_set_save_handler($handler, true);
