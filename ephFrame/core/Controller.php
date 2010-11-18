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
	
	protected $data = array();
	
	protected $action = 'index';
	
	public function __construct(Request $request, Array $params = array())
	{
		$this->request = $request;
		$this->response = new Response(404);
		$this->view = new View();
		$this->params = array_merge_recursive($this->params, $params);
	}
	
	public function index()
	{
	}
	
	public function action($action, Array $params = array())
	{
		$this->action = $action;
		return call_user_func_array(array($this, $this->action), $params);
	}
	
	public function __toString()
	{
		$this->response->body = (string) $this->view->render($this->action);
		return $this->response->body;
	}
}