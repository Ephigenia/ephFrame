<?php

class Controller
{
	protected $request;
	
	protected $response;
	
	protected $params = array();
	
	public function __construct(Request $request, Array $params = array())
	{
		$this->request = $request;
		$this->response = new HTTPResponse(404);
		$this->params = array_merge_recursive($this->params, $params);
	}
}