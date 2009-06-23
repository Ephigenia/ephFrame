<?php

/**
 * 	ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * 	Copyright (c) 2007+, Ephigenia M. Eichner
 * 						 Kopernikusstr. 8
 * 						 10245 Berlin
 *
 * 	Licensed under The MIT License
 * 	Redistributions of files must retain the above copyright notice.
 * 
 * 	@license		http://www.opensource.org/licenses/mit-license.php The MIT License
 * 	@copyright		copyright 2007+, Ephigenia M. Eichner
 * 	@link			http://code.ephigenia.de/projects/ephFrame/
 * 	@version		$Revision$
 * 	@modifiedby		$LastChangedBy$
 * 	@lastmodified	$Date$
 * 	@filesource		$HeadURL$
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
	
	public function __construct($name, Array $data = array()) {
		return parent::__construct($name, null, $data);
	}
	
	protected function createViewFilename () {
		$knownPart = $this->name.'.'.$this->templateExtension;
		if (!empty($this->theme)) {
			if (!is_dir($this->dir.'theme'.DS.$this->theme.DS)) {
				throw new ThemeNotFoundException($this);
			}
			$filenames[] = $this->dir.'theme'.DS.$this->theme.DS.'element'.DS.$knownPart;
		}
		$filenames[] = $this->dir.'element'.DS.$knownPart;
		$filenames[] = FRAME_ROOT.'view/element/'.$this->name.'.'.$this->templateExtension;
		foreach($filenames as $this->filename) {
			if (file_exists($this->filename)) { $found = true; break; }
		}
		if (empty($found)) {
			$this->filename = $filenames[0];
			throw new ElementFileNotFoundException($this);
		}
		if (!file_exists($this->filename)) throw new ElementFileNotFoundException($this);
		return $this->filename;
	}
	
	public function beforeRender() {
		$this->data['elementName'] = str_replace('/', '_', $this->name);
		$this->data['elementBaseName'] = basename($this->name);
		return true;
	}
	
	public function afterRender($rendered) {
		// show element names if registry var is turned to on, note that you
		// must have DEBUG > 1
		if (Registry::get('debug.showElementName') && Registry::get('DEBUG') > DEBUG_PRODUCTION) {
			$rendered = '<div class="elementName">'.$this->name.'</div><div class="element">'.$rendered.'</div>';
		}
		return $rendered;
	}
	
}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ElementException extends BasicException {}

/**
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class ElementFileNotFoundException extends BasicException {
	public function __construct(View $view) {
		$this->view = $view;
		$message = 'Unable to find element File in \''.$this->view->filename.'\'.';
		parent::__construct($message);
	}
}

?>