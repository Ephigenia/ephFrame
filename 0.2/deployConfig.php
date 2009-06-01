<?php

$deployDir = '../deploy/';
$exclude = array('*.project/*', '*.cache/*', '*.settings/*', '.DS_Store', '*.svn/*', '*.structure.json');
$version = trim(file_get_contents(dirname(__FILE__).'/ephFrame/VERSION.txt'));
$codeMoreSleepTarget = '/Users/Ephigenia/Sites/code.moresleep.net/trunc/html/ephFrame/';

/**
 *	This is the deploy configuration for ephFrame framwork using ephDeploy
 *  {@link http://code.ephigenia.de/projects/ephDeploy/}
 *
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 06.09.2008
 */
$config = array(

	'name' => 'ephFrame v.'.$version,
	
	'default' => '',
	
	'cls'	=> true,

	'targets' => array(

		'publish' => array(
			'description' => 'Create a new release for code.moresleep.net',
			'tasks' => array(
				// create zip for code.moresleep.net
				array('Zip', array(
					'src'			=> './',
					'target'		=> $codeMoreSleepTarget.'download/ephFrame_%date%.zip',
					'silent'		=> true,
					'exclude'		=> $exclude
				)),
				array('Copy', array(
					'src' => 'changelog.txt',
					'target' => $codeMoreSleepTarget.'download/changelog.txt'
				)),
				array('CreateDir', array(
					'dir' => $codeMoreSleepTarget.'doc/phpdoc/'
				)),
				array('PHPDocumentor', array(
					'src' => './',
					'target' => $codeMoreSleepTarget.'/doc/phpdoc/',
					'template' => 'HTML:frames:DOM/nms',
					'title' => 'ephFrame Documentation',
					'defaultPackage' => 'ephFrame'
				))
			)
		),
		
		'docu' => array(
			'description' => 'Create documentation using PHPDocumentor',
			'tasks' => array(
				array('PHPDocumentor', array(
					'src' => './',
					'target' => '../doc/phpdocumentor',
					'template' => 'HTML:frames:DOM/nms',
					'title' => 'ephFrame Documentation',
					'defaultPackage' => 'ephFrame'
				))
			)
		),
		
		'sloccount' => array(
			'description' => 'Create sloc count report',
			'tasks'	=> array(
				array('CreateDir', array(
					'dir'		=> $deployDir.'sloc/'
				)),
				array('SLOCCount', array(
					'src'		=> './',
					'target'	=> $deployDir.'sloc/sloccount_report_%date%.txt'
				))
			)
		),
		
		'UnitTest' => array(
			'description' => 'Run Unit Test',
			'tasks' => array(
				array('UnitTest', array(
					'src' => './test/ephFrame.php'
					//,'silent' => true
				))
			)
		),
		
		'archive' => array(
			'description' => 'Create snapshot of current state in a zip',
			'tasks' => array(
				array('CreateDir', array(
					'dir'		=> $deployDir.'archive/'
				)),
				array('Zip', array(
					'src' => './',
					'silent' => true,
					'target' => $deployDir.'/archive/ephFrame_%date%.zip',
					'exclude' => $exclude
				))
			)
		),
		
		'deploy' => array(
			'depends' => array('UnitTest', 'archive', 'sloccount'),
			'description' => 'Create a new release ZIP',
			'tasks' => array(
				array('Zip', array(
					'src' => './',
					'target' => $deployDir.'ephFrame_'.$version.'.zip',
					'exclude' => $exclude,
					'silent' => true
				))
			)
		)
		
	) // targets
	
); // config

?>