<?php

require_once dirname(__FILE__).'/AppComponent.php';

/**
 * Flash Message Types
 * 
 * @package nms.update
 * @subpackage nms.update.lib.component
 * @since 2009-10-10
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 */
class FlashMessageType
{	
	const ERROR = 'error';
	const HINT = 'hint';
}

/**
 * Component to manage little flashing message for views
 * 
 * Example Controller code:
 * <code>
 * if (!$this->User->save()) {
 * 	$this->FlashMessage->set('Error while saving', FlashMessageType::ERROR);
 * }
 * </code>
 * 
 * @package nms.update
 * @subpackage nms.update.lib.component
 * @author Ephigenia // Marcel Eichner <love@ephigenia.de>
 * @since 09.03.2009
 */
class FlashMessage extends AppComponent
{
	public $components = array(
		'Session',
	);

	public $sessionVarname = 'flashMessage';
	
	public function beforeRender()
	{
		if (!$this->Session->isEmpty($this->sessionVarname)) {
			$this->controller->data->set('flashMessage', $this->Session->get($this->sessionVarname));
			$this->reset();
		}
		return parent::beforeRender();
	}
	
	public function set($message, $type = FlashMessageType::HINT)
	{
		$this->Session->set($this->sessionVarname, array(
			'message'	=> $message,
			'type'		=> $type
		));
		return $this;	
	}
	
	public function delete()
	{
		$this->reset();
	}
	
	public function reset()
	{
		$this->Session->set($this->sessionVarname, false);
		return $this;
	}
}