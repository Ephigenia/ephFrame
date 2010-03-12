<?php

/**
 * Email class with SMTP/Mail or Debug-Output Delivery
 *
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2009-08-30
 */
class Email extends AppComponent implements Renderable
{
	public $to;
	
	public $from = array();
	public $replyTo = array();
	
	public $cc = array();
	public $bcc = array();
	
	public $subject = '';
	public $message = '';
	public $htmlMessage = '';
	
	public $header = array();
	public $attachments = array();
	
	public $delivery = 'mail';
	
	public $mailOptions = array(
		'params' => null
	);
	
	public $smtpOptions = array(
		'host' => 'mail.ephigenia.de',
		'username' => '632377',
		'password' => 'alone',
		'port'	=> 25,
		'timeout' => 10,
	);
	
	public $charset = 'UTF-8';
	
	public function __construct() 
	{
		$this->boundary = md5(time());
    	$this->boundaryAlt = md5(time().'alternative');
		return parent::__construct();
	}
	
	public function reset() 
	{
		$this->header = $this->attachments = array();
		$this->cc = $this->bcc = array();
		$this->message = $this->htmlMessage = '';
		return $this;
	}
	
	public function attach($filename, $content = null) 
	{
		if (!in_array($filename, $this->attachments)) {
			if (func_num_args() == 2) {
				$this->attachments[$filename] = $content;
			} else {
				$this->attachments[] = $filename;
			}
		}
		return $this;
	}
	
	public function from($email, $name = null) 
	{
		$this->from = $this->formatEmail($email, $name);
		return $this;
	}
	
	public function to($email, $name = null) 
	{
		$this->from = $this->formatEmail($email, $name);
		return $this;
	}
	
	public function messageType() 
	{
		if (count($this->attachments) > 0) {
			return 'alt_attach';
		}
		if (!empty($this->htmlMessage) && !empty($this->message)) {
			return 'alt';
		}
		if (!empty($this->htmlMessage)) {
			return 'html';
		}
		return 'plain';
	}
	
	public function beforeSend() 
	{
		$this->subject = $this->stripInjections($this->subject);
		$this->to = $this->stripInjections($this->to);
		$this->from = $this->stripInjections($this->from);
		$this->message = $this->stripInjections($this->message);
		$this->htmlMessage = $this->stripInjections($this->htmlMessage);
		// utf8-special encodign for subject
		if ($this->charset == 'UTF-8') {
			// $this->subject = '=?UTF-8?B?'.preg_replace('/[^\x09\x20-\x3C\x3E-\x7E]/e', 'sprintf("=%02X", ord("$0"));', $this->subject);
			$this->subject = '=?UTF-8?B?'.base64_encode($this->subject).'?=';
		}
		return true;
	}
	
	public function send($to = null, $subject = null, $message = null, Array $header = array(), $additionalParameters = null) 
	{
		// set last-minute variables
		if (!empty($to)) $this->to = $to;
		if (!empty($subject)) $this->subject = $subject;
		if (!empty($message)) $this->message = $message;
		if (is_array($header)) $this->header = array_merge($this->header, $header);
		// call beforesend callback for addional
		if (!$this->beforeSend()) return false;
		// send depending on delivery method set
		if ($this->delivery == 'mail') {
		 	return mail($to, $this->subject, $this->body(), $this->header(), $this->mailOptions['params']);
		} elseif ($this->delivery == 'smtp') {
			return $this->smtp();
		} elseif ($this->delivery == 'debug') {
			file_put_contents(LOG_DIR.'email-'.date('Ymd-His').'.eml', $this->render());
			return true;
		}
		return false;
	}
	
	public function smtp() 
	{
		trigger_error('smtp delivery not finished yet', E_USER_WARNING);
		return false;
		class_exists('Socket') or require dirname(__FILE__).'/../Socket.php';
		$socket = new Socket($this->smtpOptions['host'], 25, $this->smtpOptions['timeout']);
		$socket->connect();
		echo $socket->read(255, true);
		die('OK');
		flush();
	}
	
	public function header() 
	{
    	// FROM and TO
		if ($this->delivery == 'smtp') {
			$header[] = 'To: '.$this->formatEmail($this->to);
		}
    	$header[] = 'From: '.$this->formatEmail($this->from);
		// REPLY-TO, CC and BCC
		if (!empty($this->replyTo)) $header[] = 'Reply-To: '.implode(', ', array_map(array($this, 'formatEmail'), $this->replyTo));
		if (!empty($this->cc)) $header[] = 'cc: '.implode(', ', array_map(array($this, 'formatEmail'), $this->cc));
		if (!empty($this->bcc)) $header[] = 'Bcc: '.implode(', ', array_map(array($this, 'formatEmail'), $this->bcc));
		// SUBJECT
		if ($this->delivery == 'smtp') {
			$header[] = 'Subject: '.preg_replace('@[\r\n]@i', '', $this->subject);
		}
		// header
		if (!is_array($this->header)) {
			foreach($this->header as $lft => $rgt) {
				if (!is_int($lft)) {
					$header[] = 'X-'.$lft.': '.$rgt;
				} else {
					$header[] = $rgt;
				}
			}
		}
    	// Content Type
    	switch ($this->messageType()) {
			default:
			case 'plain':
				$header[] = 'Content-Transfer-Encoding: 8bit';
				$header[] = 'Content-Type: text/plain; charset="'.$this->charset.'"';
    			break;
    		case 'html':
    			$header[] = 'Content-Transfer-Encoding: 8bit';
				$header[] = 'Content-Type: text/html; charset="'.$this->charset.'"';
    			break;
    		case 'alt_attach':
    			$header[] = 'Content-Type: multipart/mixed; boundary="'.$this->boundary.'"';
    			break;
    		case 'alt':
				$header[] = 'Content-Type: multipart/alternative; boundary="'.$this->boundary.'"';
    			break;
    	}
    	return implode(LF, $header);
    }
	
	private function body() {
		switch ($this->messageType()) {
			case 'plain':
			default:
				$body = $this->message;
				break;
			case 'alt':
				// TextPart
				$body = '--'.$this->boundary.LF
					.'Content-Type: text/plain; charset="'.$this->charset.'"'.LF
					.'Content-Transfer-Encoding: 8bit'.LF.LF
					.$this->message.LF.LF
					// HTML Part
					.'--'.$this->boundary.$eol
					.'Content-Type: text/html; charset="'.$this->charset.'"'.LF
					.'Content-Transfer-Encoding: 8bit'.LF.LF
					.$this->htmlMessage.LF.LF
					// Boundary End
					.'--'.$this->boundary.'--'.LF;
				break;
			case 'alt_attach':
				$body = '--'.$this->boundary.LF
					.'Content-Type: multipart/alternative; boundary="'.$this->boundaryAlt.'"'.LF.LF;
				// Text part	
				if (!empty($this->message)) {
					$body .=
					 '--'.$this->boundaryAlt.LF
					.'Content-Type: text/plain; charset="'.$this->charset.'"'.LF
					.'Content-Transfer-Encoding: 8bit'.LF.LF
					.$this->message.LF.LF;
				}
				// Html Part
				if (!empty($this->htmlMessage)) {
					$body .=
					 '--'.$this->boundaryAlt.LF
					.'Content-Type: text/html; charset="'.$this->charset.'"'.LF
					.'Content-Transfer-Encoding: 8bit'.LF.LF
					.$this->htmlMessage.LF.LF;
				}
				$body .= '--'.$this->boundaryAlt.'--'.LF;
				// attachments
				$body .= $this->attachments();
				break;
			case 'attach':
				$body = '--'.$this->boundary.LF
					.$this->message.LF
					.$this->attachments();
				break;
		}
    	return $body;
	}
	
	private function attachments() {
		class_exists('MimeTypes') or require dirname(__FILE__).'/../helper/MimeTypes.php';
		class_exists('File') or require dirname(__FILE__).'/../File.php';
		$body = "";
		// Attached Files
		foreach ($this->attachments as $index => $filename) {
			if ($filename instanceof File) {
				$mime = $file->mimeType();
				$content = $file->slurp();
			} elseif (is_int($index)) {
				$file = new File($filename);
				$mime = $file->mimeType();
				$content = $file->slurp();
			} else {
				$content = $filename;
				$mime = MimeTypes::mimeType($index);
				$filename = $index;
			}
			$body .=  '--'.$this->boundary.LF
					.'Content-Type: '.$mime.'; name="'.basename($filename).'";'.LF
					.'Content-Transfer-Encoding: base64'.LF
					.'Content-Disposition: attachment; filename="'.basename($filename).'"'.LF.LF
					.chunk_split(base64_encode($content)).LF.LF;
		}
		$body .= '--'.$this->boundary.'--';
		return $body;
	}
	
	/**
	 * http://www.erich-kachel.de/?p=26
	*/
	public function stripInjections($string, $brakes = false) 
	{
		$regexp = '%0a|%0d|Content-(?:Type|Transfer-Encoding)\:|charset\=|mime-version\:|multipart/mixed|(?:[^a-z]to|b?cc)\:.*';
		if ($brakes) {
			$regexp .= '|\r|\n';
		}
		$regexp = '@(?:'.$regexp.')@i';
		while(preg_match($regexp, $string)) {
			$string = preg_replace($regexp, '', $string);
		}
		return $string;
	}
	
	public function formatEmail($email, $name = null) 
	{
		$email = mb_encode_mimeheader($this->stripInjections($email, true), $this->charset, 'B', RTLF);
		if (empty($name)) {
			return $email;
		}
		$name = mb_encode_mimeheader($this->stripInjections($name, true), $this->charset, 'B', RTLF);
		return sprintf('%s <%s>', $name, $email);
	}
	
	public function render() 
	{
		if (!$this->beforeRender()) return false;
		return $this->afterRender($this->header().RTLF.$this->body());
	}
	
	public function beforeRender() 
	{
		return true;
	}
	
	public function afterRender($content) 
	{
		return $content;
	}	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class EmailException extends ComponentException 
{}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class EmailEmptyToException extends EmailException 
{
	public function __construct() 
	{
		$this->message = 'Unable to deliver mail because recipient was empty.';
		return parent::__construct();
	}
}
