<?php

use Composer\Autoload\ClassLoader;

/**
 * @var ClassLoader $loader
 */
$loader = require dirname(__DIR__) . '/vendor/autoload.php';
$loader->unregister();
$loader->register(true);
