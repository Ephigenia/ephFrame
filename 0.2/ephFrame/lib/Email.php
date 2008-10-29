<?php
ephFrame::loadClass('ephFrame.lib.helper.Validator');
ephFrame::loadClass('ephFrame.lib.helper.Charset');
ephFrame::loadClass('ephFrame.lib.helper.String');
ephFrame::loadClass('ephFrame.lib.Hash');
ephFrame::loadClass('ephFrame.lib.HTTPHeader');

class Email extends Object {

	protected $attachments = array();
	protected $from = null;
	protected $headers = null;
	protected $subject = null;
	protected $to = null;
	
	public function __construct($to = null, $subject = null, $body = null) {
		$this->headers = new HTTPHeader();
		$this->to = new Hash();

		$this->to($to);
		$this->subject($subject);
		$this->body($body);
	}
	
	public function addAttachment() {
		
	}
	
	public function addHeader($key, $value = null) {
		return $this->headers->add($key, $value);
	}
	
	public function addRecipient($email, $name = null) {
		if(!$this->isEmailAddress($email)) throw new InvalidEmailAddressException($email);
		return $this->to->add($this->RFC2822MailAddress($email, $name));
	}
	
	public function afterSend() {
		return true;
	}
	
	public function beforeSend() {
		return true;
	}
	
	public function body() {
		
	}
	
	public function from($email = null, $name = null) {
		if(!empty($email)) {
			if(!$this->isEmailAddress($email)) throw new InvalidEmailAddressException($email);
			$this->from = $this->RFC2822MailAddress($email, $name);
		}
		return $this->from;
	}
	/* TODO: Extend ViewClass to enter Filename directly! */
	public function fromTemplate($template) {
		$view = new View($template);
		return $this->body($view->render());
	}
	
	public function hasAttachments() {
		
	}
	
	public function isEmailAddress($email) {
		return Validator::email($email);
	}
		
	public function send() {
		if($this->beforeSend()) {
			foreach($this->to->values() as $recipient) {
				//@mail($recipient, $this->subject, $this->body(), $this->headers->render());
			}
			$this->afterSend();
			return true;
		}
		return false;
	}
	
	public function subject($subject = null) {
		if(!empty($subject)) {
			$this->subject= $subject;
		}
		return $this->subject;
	}
	
	public function to($recipients = null) {		
		if(!empty($recipients)) {
			if(!is_array($recipients)) $recipients = array($recipients);
			foreach($recipients as $recipient) {
				if(!$this->isEmailAddress($recipient)) throw new InvalidEmailAddressException($recipient);
				$this->to->add($this->toRFC2822MailAddress($recipient));
			}
		}
		return $this->to;
	}
	
	/* TODO: implement RFC2822 */
	protected function toRFC2822MailAddress($email, $name = null) {
		return $email;
	}
	
}

class EmailException extends ObjectException {}

class InvalidEmailAddressException extends EmailException {
	
	public function __construct($email, $emailObj = null) {
		$this->message = 'This Email Address: \''.$email.'\' is invalid';
		parent::__construct();	
	}
	
}

?>