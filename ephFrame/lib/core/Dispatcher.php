<?php

class Dispatcher
{
	public function dispatch($url)
	{
		return new Controller();
	}
}