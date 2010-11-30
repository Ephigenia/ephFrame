<?php

namespace ephFrame\core;

use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;
use ephFrame\view\View;

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
	}
	
	protected function index()
	{
		
	}
	
	public function action($action, Array $params = array())
	{
		$this->action = $action;
		$this->callbacks->call('beforeAction');
		if (!method_exists($this, $this->action)) {
			die('ACTION NOT FOUND');
		}
		$this->callbacks->call('afterAction');
		return call_user_func_array(array($this, $this->action), $params);
	}
	
	public function __toString()
	{
		$this->callbacks->call('beforeRender');
		$this->response->body = (string) $this->view->render(
			strtolower(($this->name ?: 'app').'/'.$this->action)
		);
		$this->response->header->send();
		$this->callbacks->call('afterRender');
		return $this->response->body;
	}
}