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
	
	public function __construct(Request $request, Array $params = array())
	{
		$this->request = $request;
		$this->response = new Response();
		$this->view = new View();
		$this->params = array_merge_recursive($this->params, $params);
		$this->name = preg_replace('@(.+)\\\(\w*)Controller$@', '\\2', get_class($this));
	}
	
	public function index()
	{
		
	}
	
	public function action($action, Array $params = array())
	{
		$this->action = $action;
		if (!method_exists($this, $this->action)) {
			die('ACTION NOT FOUND');
		}
		return call_user_func_array(array($this, $this->action), $params);
	}
	
	public function beforeRender()
	{
		$this->view->data += array(
			'action' => $this->action,
			'controller' => $this->name,
		);
		return true;
	}
	
	public function __toString()
	{
		$this->beforeRender();
		$this->response->body = (string) $this->view->render(
			(!empty($this->name) ? $this->name : 'app').'/'.$this->action);
		$this->response->header->send();
		return $this->response->body;
	}
}