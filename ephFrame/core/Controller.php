<?php

namespace ephFrame\core;

use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;
use ephFrame\view\View;
use ephFrame\HTTP\Header;

class Controller
{
	protected $request;
	
	protected $response;
	
	protected $view;
	
	protected $params = array();
	
	protected $action = 'index';
	
	protected $callbacks;
	
	public function __construct(Request $request, Array $params = array())
	{
		$this->request = $request;
		$this->response = new Response();
		$this->view = new View();
		$this->params = array_merge_recursive($this->params, $params);
		$this->name = preg_replace('@(.+)\\\(\w*)Controller$@', '\\2', get_class($this));
		$this->callbacks = new \ephFrame\core\CallbackHandler();
		$this->callbacks->add('beforeAction', array($this, 'beforeAction'));
		$this->callbacks->add('afterAction', array($this, 'afterAction'));
		$this->callbacks->add('beforeRender', array($this, 'beforeRender'));
		$this->callbacks->add('afterRender', array($this, 'afterRender'));
	}
	
	protected function index()
	{
		return true;
	}
	
	public function action($action, Array $params = array())
	{
		$this->action = $action;
		$this->callbacks->call('beforeAction');
		$this->callbacks->call($this->action, $params);
		if (method_exists($this, $this->action)) {
			$result = call_user_func_array(array($this, $this->action), $params);
		} else {
			$result = true;
		}
		$this->callbacks->call('afterAction');
		return $result;
	}
	
	public function beforeAction()
	{
		if (isset($this->params['type'])) {
			$this->view->type = $this->params['type'];
		}
		if (empty($this->response->header->{'Content-Type'})) {
			switch($this->view->type) {
				case 'html':
				case 'rss':
				case 'atom':
				case 'xml':
				case 'txt':
				case 'js':
				case 'json':
					$this->response->header->{'Content-Type'} = \ephFrame\util\MimeType::get($this->view->type).'; charset: UTF-8';
					break;
			}
		}
	}
	
	public function beforeRender()
	{
		// setting some default variables
		$this->view->data += array(
			'controller' => substr(strrchr(get_class($this), '\\'), 1, -10),
			'action' => $this->action,
			'baseUri' => \ephFrame\core\Router::base(),
			'Router' => \ephFrame\core\Router::getInstance(),
		);
	}
	
	public function afterRender()
	{
		
	}
	
	public function __toString()
	{
		$this->callbacks->call('beforeRender');
		$this->response->body = $this->view->render('all', strtolower($this->name ?: 'app').'/'.$this->action);
		$this->callbacks->call('afterRender');
		$this->response->header->send();
		return $this->response->body;
	}
}

class ControllerException extends \Exception {}