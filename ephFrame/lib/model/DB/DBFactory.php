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
 * Database-Access-Object (DAO) Factory
 * 
 * This class is used to create new DAO Objects for different Databases.
 * 
 * Establishing a database connection to a mysql database
 * <code>
 * $MySQLDAO = $DAOFactory::create('mysql');
 * $MySQLDAO->connect(new DSN('mysql://hasen:bau@localhost:3306/karottenvorrat'););
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 19.05.2007
 * @package ephFrame
 * @subpackage ephFrame.lib.model.DB
 * @version 0.2
 */
class DBFactory
{
	/**
	 * Creates a new DAO Object of the given type and returns it
	 * 
	 * @throws DBFactoryDBTypeNotFoundException
	 * @param string $type
	 * @return DAO
	 */	
	public function create($type) 
	{
		if (strtolower($type) == 'mysql') $type = 'MySQL';
		$classPath = 'ephFrame.lib.model.DB.'.$type.'.'.$type;
		try {
			Library::load($classPath);
		} catch (ephFrameLoadError $e) {
			throw $e;
			throw new DBFactoryDBTypeNotFoundException($type);
		}
		$DAOObject = new $type();
		return $DAOObject;
	}	
}

/**
 * @packge ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBFactoryException extends BasicException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class DBFactoryDBTypeNotFoundException extends DBFactoryException 
{
	public function __construct($type) 
	{
		parent::__construct(sprintf('No Database Connection Handler for "%s" found.', $type));
	}
}