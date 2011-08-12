<?php

use 
	\ephFrame\core\Router,
	\ephFrame\core\Route
	;

$router = Router::getInstance();
$router->addRoutes(array(
	new Route('/:page', array('action' => 'display', 'controller' => 'app\controller\Controller')),
	'root' => new Route('/', array('action' => 'index', 'controller' => 'app\controller\Controller')),
));