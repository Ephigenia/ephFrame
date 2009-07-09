<?php

/**
 * ephFrame: <http://code.moresleep.net/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Kopernikusstr. 8
 *                      10245 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('CURL') or require(dirname(__FILE__).'/../CURL.php');

/**
 * Simple Twitter API Integration
 * 
 * See the Twitter API Documentation: http://apiwiki.twitter.com/REST+API+Documentation#TheEasiestWaytoPlayAroundwiththeTwitterAPI<br />
 * <br />
 * This should be a simple Twitter API Integration. It does not support the 
 * hole set of methods that Twitter offers to it's users, but it implements
 * the most common ones like reading status messages and posting them.<br />
 * <br />
 * See the method descriptions for some examples.<br />
 * <br />
 * 
 * Example with error catching:
 * <code>
 * $twitter = new Twitter('love@ephigenia.de', 'xxxxxxx');
 * try {
 * 	$twitter->updateStatus('message');
 * } catch (TwitterAuthentificationException $e) {
 * 	echo 'ERROR, please check authenficiation data.';
 * }
 * </code>
 * 
 * @package ephFrame
 * @subpackage ephFrame.lib.api
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 11.11.2008
 */
class Twitter extends CURL {
	
	public $baseUrl = 'http://www.twitter.com/';
	
	public $username; 
	
	public $password;
	
	/**
	 * Construct {@link Twitter}
	 * 
	 * This method needs authentification data if you wish to use {@link status}
	 * or {@link location}. See the Twitter API-Documentation to see which
	 * methods will need user authentification.
	 * 
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username = null, $password = null) {
		$this->username = $username;
		$this->password = $password;
		$this->auth = array(&$this->username, &$this->password);
		parent::__construct($this->baseUrl);
	}
	
	/**
	 * Returns statuses
	 * 
	 * This will return status messages from a specific user (indicated by $id)
	 * or the authentificated user.
	 * 
	 * <code>
	 * // read public status messages from a user:
	 * $twitter = new Twitter();
	 * foreach($twitter->timeline(17006937) as $statusMessage) {
	 * 	echo date('d.m.Y H:i', strtotime($statusMessage->created_at))."\n";
	 * 	echo $statusMessage->text."\n\n";
	 * }
	 * </code>
	 * 
	 * @param integer $id Id of user who’s timeline you want to have
	 * @param integer $count number of statuses to receive
	 * @param string $since
	 * @param intever $since_id
	 * @param integer $page
	 * @return array(StdObj)
	 */
	public function timeline($id = null, $count = null, $since = null, $since_id = null, $page = null) {
		$this->url	= $this->baseUrl.'statuses/user_timeline.json';
		$this->data = array(
			'id' 	=> (int) $id,
			'count' => $count,
			'since' => $since,
			'since_id' => $since_id,
			'page' 	=> (int) $page
		);
		return $this->sendAndReceive();
	}
	
	/**
	 * Send a new Status message
	 * 
	 * This will update your status message on twitter and return the id of the
	 * status message.
	 * 
	 * <code>
	 * $twitter = new Twitter('love@ephigenia.de', 'xxxxxxxxx');
	 * $twitter->updateStatus('Hi Friends!');
	 * </code>
	 * 
	 * Empty Status messages will be ignored!
	 * @return integer $id
	 * @param string message
	 */
	public function updateStatus($message) {
		if (trim($message) == '') return false;
		$this->url 	= $this->baseUrl.'statuses/update.json';
		$this->data = array('status' => $message);
		$response = $this->sendAndReceive();
		return $response->id;
	}
	
	/**
	 * deletes a specific status message on twitter
	 * @param integer $id
	 * @return array(StdObj)
	 */
	public function deleteStatus($id) {
		$this->url	= $this->baseUrl.'statuses/destroy.json';
		$this->data = array('id' => (int) $id);
		return $this->sendAndReceive();
	}
	
	/**
	 * Update the Twitter user's location
	 * This will change the location information of a twitter user in his
	 * profile:
	 * 
	 * <code>
	 * $twitter = new Twitter('love@ephigenia.de', 'xxxxxxxxx');
	 * $twitter->statusUpdate('Hi Friends!');
	 * </code>
	 * 
	 * @param string
	 * @return StdObj
	 */
	public function location($location) {
		$this->url	= $this->baseUrl.'statuses/update_location.json';
		$thsi->data = array('location' => $location);
		return $this->sendAndReceive();
	}
	
	/**
	 * Return a list of friends
	 * @param integer $id
	 * @param integer $page
	 * @param integer $since
	 * @return array(StdObj)
	 */
	public function friends($id = null, $page = null, $since = null) {
		$this->url 	= $this->baseUrl.'statuses/friends.json';
		$this->data = array('id' => (int) $id, 'page' => (int) $page, 'since' => $since);
		return $this->sendAndReceive();
	}
	
	/**
	 * Return a list of followers
	 * @param integer $id
	 * @param integer $page
	 * @return array(StdObj)
	 */
	public function followers($id = null, $page = null) {
		$this->url	= $this->baseUrl.'statuses/followers.json';
		$this->data = array('id' => (int) $id, 'page' => (int) $page);
		return $this->sendAndReceive();
	}
	
	/**
	 * Send Twitter Request and receive Answer including check for errors
	 * @return object
	 */
	private function sendAndReceive() {
		if (!$response = $this->exec()) throw new TwitterException();
		$response = json_decode($response);
		$this->checkResponseError($response);
		return $response;
	}
	
	private function checkResponseError($response) {
		if (!isset($response->error)) return false;
		if (preg_match('/could not authenticate you/i', $response->error)) {
			throw new TwitterAuthentificationException($response);
		} else {
			throw new TwitterErrorException($response);
		}
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TwitterException extends BasicException {
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TwitterErrorException extends TwitterException {
	public function __construct($response) {
		parent::__construct('\''.$response->error.'\'');
	}
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class TwitterAuthentificationException extends TwitterErrorException {
}