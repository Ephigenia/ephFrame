<?php

use \ephFrame\core\Router;
use \ephFrame\core\Route;

$router = Router::getInstance();
$router->addRoutes(array(
	new Route('/{:page}', array('action' => 'display', 'controller' => 'app\lib\controller\Controller')),
	'root' => new Route('/', array('action' => 'index', 'controller' => 'app\lib\controller\Controller')),
));