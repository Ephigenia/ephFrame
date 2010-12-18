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
		$this->callbacks->add('beforeAction', array($this, 'beforeAction'));
		$this->callbacks->add('afterAction', array($this, 'afterAction'));
		$this->callbacks->add('beforeRender', array($this, 'beforeRender'));
		$this->callbacks->add('afterRender', array($this, 'afterRender'));
	}
	
	protected function index()
	{
		
	}
	
	public function action($action, Array $params = array())
	{
		$this->action = $action;
		$this->callbacks->call('beforeAction');
		if (method_exists($this, $this->action)) {
			call_user_func_array(array($this, $this->action), $params);
		}
		$this->callbacks->call('afterAction');
		return $this;
	}
	
	public function __toString()
	{
		$this->callbacks->call('beforeRender');
		$this->response->body = (string) $this->view->render('all', 
			strtolower($this->name ?: 'app').'/'.$this->action
		);
		$this->response->header->send();
		$this->callbacks->call('afterRender');
		return $this->response->body;
	}
}