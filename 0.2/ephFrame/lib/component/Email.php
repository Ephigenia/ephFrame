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

// load classes that are used
ephFrame::loadClass('ephFrame.lib.helper.Validator');
ephFrame::loadClass('ephFrame.lib.helper.Charset');
ephFrame::loadClass('ephFrame.lib.helper.String');

/**
 * 	Email Class for sending email messages
 * 	
 * 	Just send a mail by:
 * 	<code>
 * 	$mail = new Email();
 * 	$mail->to('love@ephigenia.de', 'Marcel Eichner');
 * 	$mail->from('info@ephigenia.de');
 * 	$mail->body('This may be body!');
 * 	$mail->send();
 * 	</code>
 * 
 * 	Attaching files!
 * 	<code>
 * 	$mail->attach('attach.pdf');
 * 	</code>
 * 
 * 	This Class can also be easily extended with default values,
 * 	this can be helpfull if your mail server only accepts email send with
 * 	additional parameters.
 * 	<code>
 * 	class MyMail extends Email {
 * 		public function __construct() {
 * 			$this->additionalParams = '-f love@ephigenia.de';
 * 			$this->from('love@ephigenia.de', 'Marcel Eichner');
 * 		}
 *  }
 * 	</code>
 * 
 * 	Or you overwrite some vars by using the {@link beforeSend} callback.
 * 	Important is that you send the true as return, otherwise the mail is
 * 	not send.
 * 	<code>
 * 	class MyMail extends Email {
 * 		public function beforeSend() {
 * 			$this->additionalParams = '-f love@ephigenia.de';
 * 			return true;
 * 		}
 * 	}
 * 	</code>
 * 
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 02.05.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 * 	@version 0.2
 * 	@uses String
 * 	@uses Validator
 * 	@uses Charset
 */
class Email extends Component implements Renderable {
	
	/**
	 *	Mail Text-Body
	 * 	@var string
	 */
	public $body;
	
	/**
	 *	Mail HTML-Body
	 * 	@var string
	 */
	public $bodyHTML;
	
	private $from = array();
	
	private $to = array();
	private $bcc = array();
	private $cc = array();
	private $header = array(
		'Subject' => null
	);
	
	/**
	 * 	Additional parameters that should be passed to the
	 * 	mail function when email is sended
	 * 	@var string
	 */
	public $additionalParams = '';

	private $charset;
	private $contentType = 'text/plain';

	private $attachments = array();
	
	/**
	 *	Boundary for mulitpart/mime messages
	 * 	@var string
	 */
	protected $boundary;

	/**
	 *	Email Constructor
	 * 	@param string $to
	 * 	@param string $subject
	 * 	@param string $textBody
	 * 	@param string $HTMLBody
	 */
	public function __construct($to = null, $subject = null, $textBody = null, $HTMLBody = null) {
		$this->charset = Charset::UTF_8;
		if ($to !== null) {
			$this->to($to);
		}
		$this->subject($subject);
		$this->body($textBody);
		$this->bodyHTML($HTMLBody);
		//$this->sender("PHP ".phpversion()); don't use a sender, security reasons
		$this->boundary = md5(time());
	}

	/**
	 *	Overwrite this function with your own logic,
	 * 	if you return false the mail will not be send
	 * 	@return boolean
	 */
	public function beforeSend() {
		return true;
	}

	/**
	 *	After Sending callback
	 * 	@return boolean
	 */
	public function afterSend() {
		return true;
	}

	/**
	 *	Sends email to recipients and returns true on success and false on no success
	 *	@return boolean success
	 */
	public function send() {
		$result = false;
		if ($this->beforeSend() !== false) {
			$subject = $this->escape($this->header['Subject']);
			foreach($this->to as $email => $name) {
				$recipients[] = $this->makeRFC2822MailAdress($email, $name);
			}
			$recipients = implode(', ', $recipients);
			$result = @mail($recipients, $subject, $this->body(), $this->header(), $this->additionalParams);
		}
		$this->afterSend();
		return ($result);
	}

	/**
	 * 	Sets or returns a header string or returns the rendered Header
	 *  With this you can set additional headers like the X-Sender header.
	 * 	<code>
	 * 	$mail->header('X-Sender', 'ephFrame');
	 * 	</code>
	 * 	If no parameters passed the function will return a valid email header.
	 *	@param string	$name	name of header part
	 *	@param string	$value	value
	 * 	@throws HeaderInjectionException
	 * 	@return string
	 */
	public function header($name = null, $value = null) {
		if (func_num_args() == 0) return $this->renderHeader();
		// check for XSS
		if (String::hasBrakes($value) || String::hasControlChars($value) ||
			String::hasBrakes($name) || String::hasControlChars($name)) {
				throw new HeaderInjectionException('Possible Header injection detected.');
		}
		$this->header[$name] = $value;
		return $this;
	}
	
	/**
	 *	Renders email adresses for the mail header. The names in the emails
	 * 	need special encodings
	 * 	@param array(string) $array
	 * 	@return string
	 */
	protected final function renderEmailHeader($array) {
		$rendered = array();
		foreach($array as $email => $name) {
			$rendered[] = $this->makeRFC2822MailAdress($email, $name);
		}
		$rendered = implode(', ', $rendered);
		return $rendered;
	}

	/**
	 *	Renders the header and returns it
	 *	@return string
	 */
	protected final function renderHeader() {
		// FROM
		if (count($this->from) > 0) $this->header('From', $this->renderEmailHeader($this->from));
		// TO
		if (count($this->to) > 0) $this->header('To', $this->renderEmailHeader($this->to));
		// CC
		if (count($this->cc) > 0) $this->header('cc', $this->renderEmailHeader($this->cc));
		// BCC
		if (count($this->bcc) > 0) $this->header('bcc', $this->renderEmailHeader($this->bcc));
		// Content Type
		switch ($this->messageType()) {
			default:
			case 'plain':
				$this->header('Content-Transfer-Encoding', '8bit');
				$this->header('Content-Type','text/plain; charset="'.Charset::encodingName($this->charset).'"');
				break;
			case 'html':
				$this->header('Content-Transfer-Encoding', '8bit');
				$this->header('Content-Type','text/html; charset="'.Charset::encodingName($this->charset).'"');
				break;
			case 'alt_attach':
				$this->header('Content-Type','multipart/mixed; boundary="'.$this->boundary.'"');
				break;
			case 'alt':
				$this->header('Content-Type','multipart/alternative; boundary="'.$this->boundary.'"');
				break;
		}
		$header = '';
		foreach ($this->header as $name => $value) {
			$header .= sprintf('%s: %s'.LF, $name, $value);
		}
		return $header;
	}

	/**
	 *	Renders Body Part Of Message depending on message type that is determined
	 *	by {@link messageType}
	 * 	@return string
	 */
	protected final function renderBody() {
		switch ($this->messageType()) {
			case 'plain':
			default:
				$body = $this->body;
				break;
			case 'alt':
				// TextPart
				$body = '--'.$this->boundary.LF;
				$body .= 'Content-Type: text/plain; charset="'.Charset::encodingName($this->charset).'"'.LF;
				$body .= 'Content-Transfer-Encoding: 8bit'.LF.LF;
				$body .= $this->body.$eol.$eol;
				// HTML Part
				$body .= '--'.$this->boundary.LF;
				$body .= 'Content-Type: text/html; charset="'.Charset::encodingName($this->charset).'"'.LF;
				$body .= 'Content-Transfer-Encoding: 8bit'.LF.LF;
				$body .= $this->bodyHTML.LF.LF;
				// Boundary End
				$body .= '--'.$this->boundary.'--'.LF;
				break;
			case 'alt_attach':
				$body = '--'.$this->boundary.LF;
				$body .= 'Content-Type: multipart/alternative;';
				$body .= TAB.'boundary="'.$this->boundary.'_"'.LF.LF;
				// Text part
				$body .= '--'.$this->boundary.'alt'.LF;
				$body .= 'Content-Type: text/plain; charset="'.Charset::encodingName($this->charset).'"'.LF;
				$body .= 'Content-Transfer-Encoding: 8bit'.LF.LF;
				$body .= $this->body.LF.LF;
				// Html Part
				$body .= '--'.$this->boundary.'alt'.LF;
				$body .= 'Content-Type: text/html; charset="'.Charset::encodingName($this->charset).'"'.LF;
				$body .= 'Content-Transfer-Encoding: 8bit'.LF.LF;
				$body .= $this->bodyHTML.LF.LF;
				$body .= '--'.$this->boundary.'alt--'.LF;
				// attachment
				$body .= $this->renderAttachments();
				break;
			case 'attach':
				$body = '--'.$this->boundary.LF;
				$body .= $this->body.LF;
				$body .= $this->renderAttachments();
				break;
		}
		return $body;
	}

	/**
	 *	Tests wheter this mail has Attachments or not
	 * 	@return boolean
	 */
	protected function hasAttachments() {
		return (count($this->attachments) > 0);
	}

	/**
	 * 	Set or returns the textual mail message, pure text
	 * 	use {@link bodyHTML} for setting a html body for the message.
	 * 	If setting the body of the message all HTML Tags will be stripped
	 *	@param string $body
	 * 	@throws InjectionException
	 * 	@return Email
	 */
	public function body($body = null) {
		if (func_num_args() == 0) return $this->body;
		if (!Charset::isUTF8($body) && $this->charset === Charset::UTF_8) $body = utf8_encode($body);
		$this->body = strip_tags($body);
		return $this;
	}
	
	/**
	 * 	Alias for {@link body}
	 * 	@param string $message
	 */
	public function message($message = null) {
		return $this->body($message);
	}

	/**
	 *	Sets or returns the html body of this mail. You can use {@link body}
	 * 	for setting a pure-text body.
	 * 	@param string $bodyHTML
	 * 	@throws InjectionException
	 * 	@return Email
	 */
	public function bodyHTML($bodyHTML = null) {
		if (func_num_args() == 0) return $this->bodyHTML;
		if (String::hasControlChars($bodyHTML)) throw new InjectionException();
		if (!Charset::isUTF8($bodyHTML) && $this->charset === Charset::UTF_8) $bodyHTML = utf8_encode($bodyHTML);
		$this->bodyHTML = $bodyHTML;
		return $this;
	}

	/**
	 *	Determines which type of email should be used
	 * 	Mutlipart emails (html/txt/attachments) differ
	 * 	from other simpler mails such as plain text mails
	 * 	@return string
	 */
	protected final function messageType() {
		$type = '';
		if (!empty($this->bodyHTML) && !empty($this->body) && $this->hasAttachments()) {
			$type = 'alt_attach';
		} elseif (!empty($this->bodyHTML) && !empty($this->body) && !$this->hasAttachments()) {
			$type = 'alt';
		} elseif (empty($this->bodyHTML) && !empty($this->body) && !$this->hasAttachments()) {
			$type = 'plain';
		} elseif (!empty($this->bodyHTML) && empty($this->body) && !$this->hasAttachments()) {
			$type = 'html';
		}
		return $type;
	}

	/**
	 * 	Renders whole email
	 * 	@param boolean $output if set to true render emediently echoes the rendered email
	 * 	@return string
	 */
	public function render($output = false) {
		if (!$this->beforeRender()) return null;
		$body = $this->header().RT.LF.$this->renderBody();
		$body = $this->afterRender($body);
		if ($output) {
			echo $body;
		} else {
			return $body;
		}
	}
	
	public function beforeRender() {
		return true;
	}
	
	public function afterRender($rendered) {
		return $rendered;
	}

	/**
	 *	Returns rendered email
	 * 	@return string
	 */
	public function __toString() {
		return $this->render();
	}
	
	/**
	 *	Checks email adress for validity and attacks
	 * 	@param string $email
	 * 	@throws HeaderInjectionException
	 * 	@throws EmailInvalidEmailException
	 * 	@return boolean
	 */
	protected final function validateEmail($email) {
		if (String::hasBrakes($email) || String::hasControlChars($email)) throw new HeaderInjectionException();
		if (!Validator::email($email)) throw new EmailInvalidEmailException($email, $this);
		return true;
	}

	/**
	 * 	Static Method for creating RFC2822 conform email adresses that are
	 * 	used in emails. Check the doc for more info <a href="http://rfc.net/rfc2822.html">RFC2822</a>
	 *	@static
	 * 	@param string $email
	 * 	@param string $name
	 * 	@return string
	 */
	private static function makeRFC2822MailAdress($email, $name = null) {
		$name = trim($name);
		if (!empty($name)) {
			if (String::hasBrakes($name) || String::hasControlChars($name)) throw new HeaderInjectionException();
			$string = sprintf('"%s" <%s>', self::escape($name), $email);
		} else {
			$string = $email;
		}
		$string = String::stripBrakes($string);
		return $string;
	}
	
	/**
	 *	Escapes a string for use in a email header or subject
	 * 	This function is from O'Reilly - Building Scalable Websites
	 * 	@param string $input
	 * 	@param static
	 * 	@return string
	 */
	private static function escape($input) {
		if (preg_match('/[^a-z ]/i', $input)) {
			$input = preg_replace('/([^a-z ])/ie', 'sprintf("=%02x", ord(StripSlashes("\\1")))', $input);
			$input = str_replace(' ', '_', $input);
			return '=?utf-8?Q?'.$input.'?=';
		}
		return $input;
	}
	
	/**
	 *	Sets from part of the email, should be the name and
	 * 	the email adress of the sender
	 * 	@param string $email
	 * 	@param string $name
	 * 	@throws EmailInvalidEmailException
	 * 	@return Email
	 */
	public function from($email, $name = null) {
		$this->validateEmail($email);
		$this->from[$email] = $name;
		return $this;
	}

	/**
	 *	Sets email recipient email and adress
	 * 	use {@link alsoTo} for adding recipients
	 * 	@param string $email
	 * 	@param string $name
	 * 	@param boolean $add set to true if you want to add an other recipient
	 *	@return Email Instance of itsself
	 */
	public function to($email, $name = null, $add = false) {
		if (!$add) $this->to = array();
		$this->validateEmail($email);
		if (!array_key_exists($email, $this->to)) {
			$this->to[$email] = $name;
		}
		return $this;
	}

	/**
	 *	Adds one or more recipients to the list of recipients.
	 * 	Usefull for mass-mails. Double entries are ignored
	 * 	@param string	$email
	 * 	@param string	$name
	 * 	@return Email Instance of itsself
	 */
	public function alsoTo($email, $name = null) {
		$this->to($email, $name, true);
	}

	/**
	 * 	Sets the cc recipient or adds an other recipient to the cc list
	 * 	if you add true as third parameter
	 * 	@param string $email
	 * 	@param string $name
	 * 	@param boolean $add
	 * 	@return Email Instance of this Email
	 */
	public function cc($email, $name = null, $add = false) {
		if (!$add) $this->cc = array();
		$this->validateEmail($email);
		if (!array_key_exists($email, $this->cc)) {
			$this->cc[$email] = $name;
		}
		return $this;
	}

	/**
	 * 	Adds one recipient to bcc list
	 * 	@param string	$email
	 * 	@param string	$name
	 * 	@return Email Instance of this Email
	 */
	public function bcc($email, $name = null, $add = false) {
		if (!$add) $this->bcc = array();
		$this->validateEmail($email);
		if (!array_key_exists($email, $this->bcc)) {
			$this->bcc[$email] = $name;
		}
		return $this;
	}

	/**
	 *	Sets or Returns email subject
	 *	@param string $subject
	 *	@return Email|string
	 * 	@throws XSSIntrusionException
	 */
	public function subject($subject = null) {
		if (func_num_args() == 0) return $this->subject;
		if (String::hasBrakes($subject)) throw new XSSIntrusionException('Brakes found in Email subject.');
		$this->header('Subject', $subject);
		return $this;
	}

	/**
	 * 	Attach an files to email
	 *
	 * 	Existing files:
	 * 	<code>
	 * 		$mail->attach("path/to/file/file.ext");
	 * 	</code>
	 *
	 * 	Not existing files, but generated on the fly
	 * 	(such as PDFs or Images)
	 * 	<code>
	 * 		$mail->attach("filename.txt", "This should be a text file", "txt");
	 * 	</code>
	 *
	 * 	@param string $filename
	 * 	@param string $fileContent Content of file
	 * 	@param string $fileType	Alternate Mime Type of File or File Extension
	 * 	@throws XSSIntrusionException
	 * 	@return Email
	 */
	public function attach($filename, $filecontent = null, $filetype = null) {
		if (String::hasBrakes($filename)) throw new XSSIntrusionException("Brakes found in filename.");
		if (!in_array($filename, $this->attachments)) {
			if ($filetype === null) {
				$filetype = strtolower(substr(strrchr(basename($filename), '.'), 1));
			}
			$this->attach[] = array($filename, $filetype, $filecontent);
		}
		return $this;
	}

	/**
	 *	Renders the Attachment Parts if there are any
	 * 	@uses CptMimeTypesComponent
	 * 	@return string
	 */
	protected final function renderAttachments() {
		ephFrame::loadClass('ephFrame.lib.helper.MimeTypes');
		$body = '';
		// Attached Files
		foreach ($this->attachments as $filearr) {
			$fileName = $filearr[0];
			if (!empty($filearr[1])) {
				$fileType = (strlen($filearr[1]) < 6) ? MimeTypes::mimeType($filearr[1]) : $filearr[1];
				$fileContent = $filearr[2];
			} else {
				$fileType = MimeTypes::mimeType($fileName);
				$fp = fopen($fileName,"rb");
				$fileContent = fread($fp,filesize($fileName));
				fclose($fp);
			}
			$body.= '--'.$this->boundary.LF.
				'Content-Type: $fileType; name=\''.basename($fileName).'\';'.LF.
				'Content-Transfer-Encoding: base64'.LF.
				'Content-Disposition: attachment; filename=\''.basename($fileName).'\''.LF.LF.
				chunk_split(base64_encode($fileContent)).LF.LF;
		}
		$body .= '--'.$this->boundary.'--';
		return $body;
	}

}

/**
 * 	Basic Email Exception
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class EmailException extends ComponentException {
}

/**
 * 	Thrown if an email is invalid
 *	@package ephFrame
 * 	@subpackage ephFrame.lib.exception
 */
class EmailInvalidEmailException extends EmailException {
	public function __construct($email, $emailObj = null) {
		$this->message = 'The email \''.$email.'\' is invalid';
		parent::__construct();	
	}
}

?>