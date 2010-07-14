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
 * Abstract Factory
 * 
 * This class can be used as mother class for every factory pattern used in
 * your application and in the ephFrame framework.<br />
 * One example is the {@link FormFieldFactory} wich creates {@link FormField}s,
 * or {@link DAOFactory} which creates DAOs.<br />
 * <br />
 * In some later version of ephFrame this might help to implement
 * methods that every factory can use. (class name transforming, class
 * checking and whatnot!)
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 08.07.2007
 * @package ephFrame
 * @abstract
 * @subpackage ephFrame.lib
 */
abstract class Factory extends Object
{}