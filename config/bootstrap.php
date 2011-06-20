<?php

namespace app;

define('COMPILE_START', microtime(true));
define('APP_ROOT', realpath(dirname(__DIR__)));

// get maybe set APPLICATION_ENV variable otherwise set it to 'production'
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// intitialize ephFrame 0.3 with it's libs and stuff
define('EPHFRAME_PATH', APP_ROOT.'/vendor/ephFrame');
if (!include EPHFRAME_PATH.'/core/Library.php') {
	$message = 
		'ephFrame core could not be found. Check the value of EPHFRAME_PATH in '.
	 	'config/bootstrap.php. It should point to the directory containing your '.
		'ephFrame directory.';
	die(trigger_error($message, E_USER_ERROR));
}

\ephFrame\core\Library::add('ephFrame', EPHFRAME_PATH);
\ephFrame\core\Library::add('app', APP_ROOT);

require __DIR__.'/config.php';
require __DIR__.'/routes.php';