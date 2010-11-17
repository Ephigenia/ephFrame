<?php

require __DIR__.'/../config/bootstrap.php';

$response = \ephFrame\core\Dispatcher::run();
echo $response;