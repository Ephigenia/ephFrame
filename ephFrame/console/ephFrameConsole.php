<?php

class ephFrameConsole extends ConsoleController 
{
	public function init()
	{
		
	}
}

/*
?>
$jobName = preg_replace('@[^A-Z0-9a-z_-]@i', '', @$argv[1]);
$jobFilename = dirname(__FILE__).'/'.basename($jobName).'.php';

if (empty($jobName)) die('No job-name specified.'.LF);
if (!is_file($jobFilename)) die(sprintf('Unable to find job file or file not readable: %s', $jobFilename).LF);

require $jobFilename;
$jobClassname = Inflector::camellize($jobName, true).'Controller';
$job = new $jobClassname(new HTTPRequest());*/