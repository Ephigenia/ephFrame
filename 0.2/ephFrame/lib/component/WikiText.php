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
 * 	Class that converts plain text inputs into html just like wikipedia does.
 * 
 * 	You can use this class to create your own syntax. See the following
 * 	example how to enhance this class to convert bb code
 * 	<code>
 * 	class BBCode extends WikiText {
 * 		public function __construct() {
 * 			$this->syntax['bold'] = array(
 * 				'/\[b\](.*)\[\/b\]/' => '<strong>\\1</strong>'
 * 			);
 * 			return parent::__construct();
 * 		}
 * 	}
 * 	</code>
 * 	<br />
 * 
 * 	You can also create complex callback methods that do more than just
 * 	the stuff a regular expression can make.<br />
 * 	<code>
 * 	class ProjectText extends WikiText {
 * 		public function __construct() {
 * 			$this->syntax['testcallback'] = array('method' => 'callbackmethodName');
 * 			return parent::__construct();
 * 		}
 * 	}
 * 	</code>
 * 	
 * 	This is partially tested in {@link TestWikiText}
 *
 * 	@author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * 	@since 04.11.2007
 * 	@package ephFrame
 * 	@subpackage ephFrame.lib.component
 */
class WikiText extends Component {

	/**
	 *	Wiki syntax array
	 * 	@var array(string)
	 */
	public $syntax = array(
		'links' => array(
			'/\[(.+)\s(.+)\]/' => '<a href="\\1">\\2</a>',
			'/\[(.*)\]/' => '<a href="\\1">\\1</a>'
			),
		'headlines' => array(
			'/^= (.+) =/m' => '<h1>\\1</h1>',
			'/^={2} (.+) ={2}/m' => '<h2>\\1</h2>',
			'/^={3} (.+) ={3}/m' => '<h3>\\1</h3>',
			'/^={4} (.+) ={4}/m' => '<h4>\\1</h4>',
			'/^={5} (.+) ={5}/m' => '<h5>\\1</h5>',
			'/^={6,} (.+) ={6,}/m' => '<h6>\\1</h6>',
			),
		'hr' => array(
			'/^-{4}/m' => '<hr />'
			),
		'lists' => array(
			'/^\#{1,} (.*)/m' => '<ul><li>\\1</li></ul>',
		),
		'numbered list' => array(
			'/^\*{1,} (.*)/m' => '<ol><li>\\1</li></ol>',
			),
		// intext syntax
		'bold' => array(
			'/\'{3}(.*)\'{3}/' => '<strong>\\1</strong>'
			),
		'italic' => array(
			'/\'{2}(.*)\'{2}/' => '<i>\\1</i>'
			),
		'urlReplace' => array(
			'/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/' => '<a href="http://$2$3" rel="nofollow">$2$4</a>'			
		)
	);
	
	/**
	 *	WikiText contstructer, enhance the syntax from here
	 */
	public function __construct() {
		return parent::__construct();
	}
	
	/**
	 *	Translates a string with wiki syntax into valid xhtml string
	 * 	translates wiki syntax into xhtml.
	 * 	Change the syntax (and translated styles by changing the {@link syntax}
	 * 	array.
	 * 	
	 * 	@param string $string
	 * 	@param string
	 */
	public function format($string) {
		$translated = $string;
		foreach ($this->syntax as $syntaxName => $syntaxData) {
			// use the callback function if it's set
			if (isset($syntaxData['method'])) {
				$translated = $this->callMethod($syntaxData['method'], $translated);
			} else {
				// use all regular expressions stored in the array
				foreach ($syntaxData as $regexp => $replace) {
					$translated = preg_replace($regexp, $replace, $translated);
				}
			}
		}
		$translated = preg_replace('/[\n\r]{2,}/s', '<br />'.LF, $translated);
		return $translated;
	}
	
}

?>