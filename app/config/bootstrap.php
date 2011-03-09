<?php

namespace app;

require __DIR__.'/../../ephFrame/core/Library.php';

define('APP_ROOT', realpath(dirname(__DIR__)));
\ephFrame\core\Library::add('App', APP_ROOT);

require __DIR__.'/config.php';
require __DIR__.'/routes.php';