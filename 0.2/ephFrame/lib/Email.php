<?php
ephFrame::loadClass('ephFrame.lib.helper.Validator');
ephFrame::loadClass('ephFrame.lib.helper.Charset');
ephFrame::loadClass('ephFrame.lib.helper.String');
ephFrame::loadClass('ephFrame.lib.Hash');
ephFrame::loadClass('ephFrame.lib.HTTPHeader');

class Email extends Object {

	protected $attachments = array();
	protected $body = null;
	protected $contentType = 'text/plain';
	protected $from = null;
	protected $headers = null;
	protected $subject = null;
	protected $recipients = array();
	protected $reply = null;
	
	public function __construct($to = null, $subject = null) {
		$this->headers = new HTTPHeader();
		$this->recipients = array('bcc' => new Hash(), 'cc' => new Hash(), 'to' => new Hash());
		$this->subject($subject);
	}
	
	public function addAttachment($filename) {
		$this->attachments[] = $filename;
	}
	
	public function addBCC($email, $name = null) {
		return $this->recipients['bcc']->add($this->toRFC2822MailAddress($email, $name));
	}
	
	public function addCC($email, $name = null) {
		return $this->recipients['cc']->add($this->toRFC2822MailAddress($email, $name));
		
	}
	
	public function addHeader($key, $value = null) {
		return $this->headers->add($key, $value);
	}
	
	public function addTo($email, $name = null) {
		return $this->recipients['to']->add($this->toRFC2822MailAddress($email, $name));
	}
	
	public function afterSend() {
		return true;
	}
	
	public function beforeSend() {
		return true;
	}
	
	public function bcc($bcc = null) {
		if(!empty($bcc)){
			$this->recipients['bcc'] = $bcc;
		}
		return $this->recipients['bcc'];
	}

	public function cc($cc = null) {
		if(!empty($cc)){
			$this->recipients['cc'] = $cc;
		}
		return $this->recipients['cc'];
	}
	
	public function from($email = null, $name = null) {
		if(!empty($email)) {
			$this->from = $this->toRFC2822MailAddress($email, $name);
		}
		return $this->from;
	}
	/* TODO: Extend ViewClass to enter Filename directly! */
	public function fromTemplate($template) {
		$view = new View($template);
		return $this->html($view->render());
	}
	
	public function hasAttachments() {
		
	}
	
	public function html($html = null) {
		if(!empty($html)){
			$this->contentType = 'text/html';
			$this->body = $html;
		}
		return $this->body;
	}
	
	public function isEmailAddress($email) {
		return Validator::email($email);
	}
	
	protected function renderBody() {
		return $this->body;
	}
	
	protected function renderHeaders() {
		return $this->headers->render();
	}
	
	public function reply($email, $name = null) {
		
	}
	
	public function replyTo($email, $name = null) {
		return $this->reply($email, $name);
	}
	
	public function send() {
		if(($this->beforeSend()) && (!$this->to()->isEmpty()) && ($this->from())) {
			// turn HTTP Headers off & add BCC/CC Headers
			if(!$this->bcc()->isEmpty()) $this->addHeader('Bcc', $this->bcc()->implode(', '));
			if(!$this->cc()->isEmpty()) $this->addHeader('Cc', $this->cc()->implode(', '));
			$this->headers->statusCode = 0;
			
			foreach($this->to()->values() as $to) {
				//@mail($to, $this->subject(), $this->renderBody(), $this->renderHeaders());
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
	
	public function text($text = null) {
		if(!empty($text)){
			$this->contentType = 'text/plain';
			$this->body = $text;
		}
		return $this->body;
	}
	
	public function to($to = null) {		
		if(!empty($to)){
			$this->recipients['to'] = $to;
		}
		return $this->recipients['to'];
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