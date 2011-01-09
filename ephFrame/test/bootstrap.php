<?php

require __DIR__.'/../core/Library.php';
define('APP_ROOT', realpath(dirname('../').'/test/'));

\ephFrame\data\Connections::add('test', array(
	'dsn' => 'sqlite:'.__DIR__.'/fixtures/test.db',
	'adapter' => 'ephFrame\data\source\adapter\MySQL',
));