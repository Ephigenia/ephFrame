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
 * @modifiedby		$LastChangedBy: moresleep.net $
 * @lastmodified	$Date: 2009-08-05 14:01:22 +0200 (Wed, 05 Aug 2009) $
 * @filesource		$HeadURL: svn+ssh://moresleep.net/home/51916/data/ephFrame/0.2/ephFrame/lib/model/behavior/PositionableBehavior.php $
 */

/**
 * Field-Increasing Behavior
 *
 * Easy to use behavior that can increase/decrease single fields by x if used.
 * 
 * The following example will increase the field 'views' with 1 and save the
 * model data right away. It will only save the single field and will not do
 * any validation:
 * <code>
 * $model->hit('views', 1);
 * </code>
 *
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @package ephFrame
 * @subpackage ephFrame.lib.models.behaviors
 * @since 2009-08-21
 */
class HitCountBehavior extends ModelBehavior
{
	public function hit($field, $i = 1)
	{
		if (!$this->model->hasField($field)) {
			return false;
		}
		return $this->model->saveField($field, $this->model->get($field) + $i);
	}
}