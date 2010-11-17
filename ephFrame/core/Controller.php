<?php

namespace ephFrame\core;

use ephFrame\HTTP\Request;
use ephFrame\HTTP\Response;

class Controller
{
	protected $request;
	
	protected $response;
	
	protected $params = array();
	
	public function __construct(Request $request, Array $params = array())
	{
		$this->request = $request;
		$this->response = new Response(404);
		$this->params = array_merge_recursive($this->params, $params);
	}
	
	public function __toString()
	{
		return $this->response;
	}
}