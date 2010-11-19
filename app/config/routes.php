<?php

use \ephFrame\core\Router;
use \ephFrame\core\Route;

Router::addRoute(
	new Route('/{:page}', array('action' => 'display', 'controller' => 'app\lib\controller\Controller'))
);
Router::addRoute(
	new Route('/', array('action' => 'display', 'controller' => 'Controller'))
);