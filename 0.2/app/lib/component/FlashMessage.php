<?php

require_once dirname(__FILE__).'/AppComponent.php';

/**
 * Component to manage little flashing message for views
 * 
 * Example Controller code:
 * <code>
 * if (!$this->User->save()) {
 * 	$this->FlashMessage->set('Error while saving', FlashMessage::TYPE_ERROR);
 * }
 * </code>
 * 
 * @package app
 * @subpackage app.lib.component
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 09.03.2009
 */
class FlashMessage extends AppComponent
{
	public $components = array(
		'Session',
	);
	
	/**
	 * Different Flash Message types that can be used
	 * @var string
	 */
	const TYPE_ERROR 	= 'error';
	const TYPE_HINT 	= 'hint';
	const TYPE_DEFAULT 	= self::TYPE_HINT;
	
	/**
	 * Name of session variable that stores the flash message
	 * @var string
	 */
	public $sessionVarname = 'flashMessage';
	
	public function beforeRender() {
		if (!$this->Session->isEmpty($this->sessionVarname)) {
			$this->controller->data->set('flashMessage', $this->Session->get($this->sessionVarname));
			$this->reset();
		}
		return parent::beforeRender();
	}
	
	public function hasMessage() {
		return $this->Session->read($this->sessionVarname);
	}
	
	public function set($message, $type = self::TYPE_DEFAULT) {
		$this->Session->set($this->sessionVarname, array(
			'message'	=> $message,
			'type'		=> $type
		));
		return $this;	
	}
	
	public function delete() {
		$this->reset();
	}
	
	public function reset() {
		$this->Session->set($this->sessionVarname, false);
		return $this;
	}
	
} // END FlashMessage class