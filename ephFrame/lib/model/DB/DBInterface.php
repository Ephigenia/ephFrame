<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

/**
 * Interface Blueprint defining some basic rules for new
 * DAO Objects
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 23.07.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 */
interface DBInterface 
{	
	public function query($query);
		
	public function errorNo();
	
	public function errorMessage();
	
	public function connect(DBDSN $dbdsn);
	
	public function beforeConnect();
	
	public function afterConnect();
}