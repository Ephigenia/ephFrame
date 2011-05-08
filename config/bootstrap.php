<?php

namespace app;

define('APP_ROOT', realpath(dirname(__DIR__)));
require APP_ROOT.'/vendor/ephFrame/core/Library.php';
\ephFrame\core\Library::add('App', APP_ROOT);

require __DIR__.'/config.php';
require __DIR__.'/routes.php';