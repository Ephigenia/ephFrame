<?php

namespace ephFrame\core;

class ErrorController extends Controller
{
	public function handleException(\Exception $exception)
	{
		$this->view->layout = 'error';
		$this->view->data['Exception'] = $exception;
		$this->response->status = \ephFrame\HTTP\StatusCode::NOT_FOUND;
		$this->action('error404');
		return true;
	}
}