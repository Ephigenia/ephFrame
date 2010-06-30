<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('Controller') or require dirname(__FILE__).'/Controller.php';

/**
 * Console Controller
 * 
 * The console controller class is like the appcontroller for web applications
 * but for scripts that run in the command line.
 *
 * ephFrame supports a lot of cool features that you’ve never seen with php
 * and command line. You can use the full power of the {@link Console} or 
 * {@link ConsoleDrawing} class that can create ProgressBars, Windows and
 * even Histograms.
 * It also provides the power of {@link OptParse} class that handles options
 * and arguments like the Python or C integration.
 *
 * You can set up your own Console Tasks by creating a task class in a file
 * named like the task name and call it through the ephFrame console.php
 * script.
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 28.10.2008
 */
class ConsoleController extends Controller
{	
	/**
	 * instance of the optparser for this console application
	 * @var OptParse
	 */
	protected $optParse;
	
	/**
	 * Clasname of the Optparse that should be used
	 * @var string
	 */
	protected $optParseClassname = 'ephFrame.lib.console.OptParse';
	
	/**
	 * Name of class that handles the output, as defualt it's consoledrawing
	 * that can colour your output, but you can replace this by your own class
	 * @var string
	 */
	protected $consoleClassname = 'ephFrame.lib.console.ConsoleDrawing';
	
	/**
	 * Console Class
	 * @var Console
	 */
	protected $console;
	
	/**
	 * stores the with for some output messages of the application
	 * @var integer
	 */
	protected $consoleWidth = 80;
	
	public function afterConstruct() 
	{
		parent::afterConstruct();
		// load console class
		$consoleClassName = ephFrame::loadClass($this->consoleClassname);
		$this->console = new $consoleClassName();
		// load optParse
		$optParseClassName = ephFrame::loadClass($this->optParseClassname);
		$this->optParse = new $optParseClassName();
		$this->init();
		while($this->main()) {}
		$this->quit();
	}
	
	protected function init() {
		$this->showHello();
		if (!empty($this->optParse->options['help'])) {
			$this->console->out(LF);
			$this->showHelp();
			$this->quit();
		}
		return true;
	}
	
	protected function main() {
		return true;
	}
	
	public function showHello() 
	{
		return true;
	}
	
	public function showHelp() 
	{
		$this->console->out($this->optParse->usage($this->consoleWidth));
		return true;
	}
	
	public function boxedMessage($message) 
	{
		return 
			WACS_ULCORNER.str_repeat(WACS_HLINE, $this->consoleWidth-2).WACS_URCORNER.LF.
			WACS_VLINE.' '.str_pad($message, $this->consoleWidth - 3, ' ', STR_PAD_RIGHT).WACS_VLINE.LF.
			WACS_LLCORNER.str_repeat(WACS_HLINE, $this->consoleWidth-2).WACS_LRCORNER.LF;
	}
	
	public function quit() 
	{
		exit;
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exceptions
 */
class ConsoleControllerException extends ControllerException 
{}