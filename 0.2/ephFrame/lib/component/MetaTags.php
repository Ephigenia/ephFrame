<?php

// load needed class
ephFrame::loadClass('ephFrame.lib.HTMLTag');

/**
 * 	Class for handling Meta Tags
 * 
 * 	@author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * 	@since 11.05.2008
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@uses HTMLTag
 */
class MetaTags extends Hash implements Renderable {
	
	public $data = array(
		'keywords' => array(),
		'description' => '',
		'robots' => array('index', 'follow'),
		'imagetoolbar' => 'no',
		'MSSmartTagsPreventParsing' => false
	);
	
	public $metaEquivNames = array('content-type', 'content-language', 'pragma');
	
	public function startup() {
		$this->controller->set('MetaTags', $this);
		return parent::startup();
	}
	
	public function renderMetaTag($key, $value) {
		if (empty($value)) {
			return '';
		}
		$tagAttributes = array();
		if (in_array($value, $this->metaEquivNames)) {
			$tagAttributes['http-equiv'] = $key;
		} else {
			$tagAttributes['name'] = $key;
		}
		switch(gettype($value)) {
			case 'boolean':
				$tagAttributes['content'] = ($value ? 'true' : 'false');
				break;
			case 'array':
				$tagAttributes['content'] = implode(', ', $value);
				break;
			default:
				$tagAttributes['content'] = $value;
				break;
		}
		$metaTag = new HTMLTag('meta', $tagAttributes);
		return $metaTag->render();
	}
	
	public function render() {
		if (!$this->beforeRender()) return '';
		$rendered = '';
		foreach($this->data as $key => $value) {
			$rendered .= $this->renderMetaTag($key, $value).LF;
		}
		return $this->afterRender($rendered);
	}
	
}

?>