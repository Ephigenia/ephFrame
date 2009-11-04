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
 * @link        http://code.ephigenia.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

class_exists('HTTPRequest') or require dirname(__FILE__).'/../HTTPRequest.php';

/**
 * A Class for retreiving information from the google maps api
 * http://code.google.com/apis/maps/documentation/
 * 
 * A simple example:
 * <code>
 * $search = new GoogleMapsCoordinatesSearch();
 * $result = $search->getLngLat('Kopernikusstr. 8', 'Berlin', '10245);
 * var_dump($result)
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 14.02.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses HTTPRequest
 */
class GoogleMapsCoordinatesSearch extends AppComponent {

	/**
	 * default api code for localhost
	 * this can be overwritten with the apps component extended
	 * @var string
	 */ 
	public $apiCode = 'ABQIAAAAaNrWTb-P8LseAxAETioNsxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQ-SGBYJDWJx7xTqkKjF-xgveMI9A';
	
	public $apiURL = 'http://maps.google.com/maps/geo';
	
	/**
	 * Set or return the google maps api code
	 *
	 * @param unknown_type $apiCode
	 * @return unknown
	 */
	public function apiCode($apiCode = null) {
		return $this->__getOrSet('apiCode', $apiCode);
	}
	
	public function generateRequest() {
		$request = new HTTPRequest();
		$request->data['key'] = $this->apiCode;
		return $request;
	}
	
	/**
	 * Uses the google api to retreive the longitude & latitude of an address
	 * and returns them as array.
	 * 
	 * @param string $street
	 * @param string $city
	 * @param string $zip
	 * @return array(string) set of n arrays holding lat and lng values
	 */
	public function getLngLat($street, $city, $zip = null) {
		// build the search string
		$q = $street.', '.$zip.' '.$city;
		// prepare request
		$request = $this->generateRequest();
		$request->data['q'] = $q;
		$request->data['output'] = 'csv';
		// send request
		$answer = $request->send($this->apiURL);
		// parse the answer csv code, the list comes from google
		// statuscode, accuracy, latitude, longitude
		$return = array();
		if (preg_match_all('/(\d+),([+-]?\d+),([+-]?\d+(?:.\d+)?),([+-]?\d+(?:.\d+)?)/i', $answer->content, $found, PREG_SET_ORDER)) {
			foreach($found as $index => $data) {
				$return[] = array('status' => $data[1], 'accuracy' => $data[2], 'lat' => $data[3], 'lng' => $data[4]);
			}
		}
		return $return;
	}
	
}

/**
 * @package ephFrame
 * @subpackage ephFrame.lib.exception
 */
class GoogleMapsCoordinatesException extends ComponentException {}