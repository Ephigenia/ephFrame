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

class_exists('HTMLTag') or require dirname(__FILE__).'/../HTMLTag.php';

/**
 * Class for handling Meta Tags
 * 
 * @author Marcel Eichner // Ephiagenia <love@ephigenia.de>
 * @since 11.05.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses HTMLTag
 */
class MetaTags extends Hash implements Renderable 
{
	public $data = array(
		'keywords' => array(''),
		'description' => '',
		'robots' => array('index', 'follow'),
		'imagetoolbar' => 'no',
		'MSSmartTagsPreventParsing' => true,
		'content-type' => 'text/html; charset=utf-8'
	);
	
	public $metaEquivNames = array('content-type', 'content-language', 'pragma', 'imagetoolbar');
	
	public function startup() 
	{
		$this->controller->set('MetaTags', $this);
		$this->data['keywords'] = new Collection($this->data['keywords']);
		return parent::startup();
	}
	
	public function __get($index) 
	{
		if (isset($this->data[$index])) {
			return $this->data[$index];
		}
		return false;
	}
	
	public function renderMetaTag($key, $value) 
	{
		if (empty($value)) {
			return '';
		}
		$tagAttributes = array();
		if (in_array($key, $this->metaEquivNames)) {
			$tagAttributes['http-equiv'] = $key;
		} else {
			$tagAttributes['name'] = $key;
		}
		if ($value instanceof IndexedArray) {
			$tagAttributes['content'] = $value->implode(',');
		} else {
			switch(gettype($value)) {
				case 'boolean':
					$tagAttributes['content'] = ($value ? 'true' : 'false');
					break;
				case 'array':
					if (count($value) > 0) {
						if (isset($value['content'])) {
							$tagAttributes = array_merge($tagAttributes, $value);
						} else {
							$tagAttributes['content'] = implode(', ', $value);
						}
					}
					break;
				default:
					$tagAttributes['content'] = $value;
					break;
			}
		}
		if (empty($tagAttributes['content'])) {
			return '';
		}
		$metaTag = new HTMLTag('meta', $tagAttributes);
		return $metaTag->render();
	}
	
	public function render() 
	{
		if (!$this->beforeRender()) return '';
		$rendered = '';
		foreach($this->data as $key => $value) {
			$rendered .= $this->renderMetaTag($key, $value).LF;
		}
		return $this->afterRender($rendered);
	}	
}