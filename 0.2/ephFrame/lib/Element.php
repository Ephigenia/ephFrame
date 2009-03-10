<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright 2007+, Ephigenia M. Eichner, Kopernikusstr. 8, 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 	@license http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright Copyright 2007+, Ephigenia M. Eichner
 * 	@link http://code.ephigenia.de/projects/ephFrame/
 * 	@filesource
 */

/**
 *	A View Element
 * 	
 * 	parts of a website that repeat in different views. Render them in your
 * 	views by calling this in a view:
 * 	<code>
 * 	echo $this->element('navigation', array($navArray));
 * 	</code>
 * 
 * 	Element Names are like more restricted filenames. Allowed characters are:
 *  a-z, A-Z, 0-9, - (minus), _ (underscore) and / as seperator for having more
 * 	dimensional folder structure in /app/views/elements/ such as here:
 * 	<code>
 * 	echo $this->renderElement('sub/element');
 * 	</code>
 * 
 * 	Elements are reachable by all views. There are no restrictions, even not if
 * 	they are in a sub folder.
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 10.08.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib
 */
class Element extends View {
	
	protected function createViewFilename () {
		$this->viewFilename = VIEW_DIR.'element/'.$this->name.'.'.$this->templateExtension;
		// if apps view does not exist, try to get view from ephFrame
		if (!file_exists($this->viewFilename)) {
			$ephFrameViewFile = FRAME_ROOT.'view/element/'.$this->name.'.'.$this->templateExtension;
			if (file_exists($ephFrameViewFile)) {
				$this->viewFilename = $ephFrameViewFile;
			}
		}
		if (!file_exists($this->viewFilename)) throw new ElementFileNotFoundException($this);
		return $this->viewFilename;
	}
	
	public function beforeRender() {
		$this->data['elementName'] = str_replace('/', '_', $this->name);
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ElementException extends BasicException {}

class ElementFileNotFoundException extends BasicException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = 'Unable to find view File \''.$this->view->viewFilename.'\'';
		parent::__construct($message);
	}
}

?>