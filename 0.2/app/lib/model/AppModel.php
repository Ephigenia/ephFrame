<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright	copyright 2007+, Ephigenia M. Eichner
 * @link		http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @filesource
 */

ephFrame::loadClass('ephFrame.lib.model.Model');

/**
 * The appmodel should be the parent class for every model used in the
 * application. So you can include basic methods that should be accessible
 * from every model in the application
 *
 * @package app
 * @subpackage app.lib.models
 */
class AppModel extends Model {
	
}