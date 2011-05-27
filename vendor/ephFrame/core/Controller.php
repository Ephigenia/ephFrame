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
	
	public $view;
	
	public $params = array();
	
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
		$this->callbacks->add('init', array($this, 'init'));
		$this->callbacks->add('beforeAction', array($this, 'beforeAction'));
		$this->callbacks->add('afterAction', array($this, 'afterAction'));
		$this->callbacks->add('beforeRender', array($this, 'beforeRender'));
		$this->callbacks->add('afterRender', array($this, 'afterRender'));
		$this->callbacks->call('init');
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
				case 'markdown':
					$this->response->header->{'Content-Type'} = 'text/html; charset: UTF-8';
					break;
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
		$this->view->controller = substr(strrchr(get_class($this), '\\'), 1, -10);
		$this->view->action = $this->action;
		$this->view->baseUri = \ephFrame\core\Router::base();
		$this->view->Router = \ephFrame\core\Router::getInstance();
	}
	
	public function afterRender()
	{
		
	}
	
	public function redirect($url, $status = \ephFrame\HTTP\StatusCode::FOUND, $exit = true)
	{
		$this->response = new \ephFrame\HTTP\Response($status, new \ephFrame\HTTP\Header(array(
			'location' => $url,
		)));
		$this->response->send();
		if ($exit) {
			exit;
		}
	}
	
	public function __toString()
	{
		$this->callbacks->call('beforeRender', array($this));
		$this->response->body = $this->view->render('all', strtolower($this->name ?: 'app').'/'.$this->action);
		$this->callbacks->call('afterRender', array($this));
		$this->response->header->send();
		return $this->response->body;
	}
}

class ControllerException extends \Exception {}