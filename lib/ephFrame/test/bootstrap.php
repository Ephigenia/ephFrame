<?php

define('EPHFRAME_PATH', realpath(__DIR__.'/../'));
define('APP_ROOT', realpath(__DIR__));

require EPHFRAME_PATH.'/core/Library.php';
\ephFrame\core\Library::add('ephFrame',	EPHFRAME_PATH);