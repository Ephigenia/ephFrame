<?php

/**
 * ephFrame: <http://code.marceleichner.de/project/ephFrame/>
 * Copyright (c) 2007+, Ephigenia M. Eichner
 *                      Brunnenstr. 10
 *                      10119 Berlin
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 * 
 * @license     http://www.opensource.org/licenses/mit-license.php The MIT License
 * @copyright   copyright 2007+, Ephigenia M. Eichner
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @filesource
 */

class_exists('CURL') or require dirname(__FILE__).'/../socket/CURL.php';

/**
 * Simple Integration of Google Maps GeoCoding API
 * 
 * This API Wrapper can help you to find right formatted addresses by geo
 * coordinates or search strings and will return an array with real formatted
 * street, zip, country data.
 * 
 * This little example shows you how to use this API Wrapper:
 * <code>
 * Library::loadClass('ephFrame.lib.api.GoogleGeoCodingAPI');
 * $request = new GoogleGeoCodingAPI();
 * $result = $request->search('Kopernikusstr. 8 10245 Berlin');
 * </code>
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de>
 * @since 2010-05-01
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 * @uses CURL
 */
class GoogleGeoCodingAPI extends CURL
{
	/**
	 * URL where request are sent to
	 * @var string
	 */
	public $url = 'http://maps.google.com/maps/api/geocode/json';
	
	/**
	 * @var HTTPRequest
	 */
	public $request;

	public $method = self::METHOD_GET;
	
	public $data = array(
		'sensor' => 'true',
	);
	
	/**
	 * Either Search for a lat,lng pair or a address search string
	 * and return found addresses as array.
	 * 
	 * @param string|float $address
	 * @param float $lng
	 * @return array(string)
	 */
	public function search($address, $lng = false)
	{
		if (func_num_args() == 2) {
			$this->data['latlng'] = $address.','.$lng;
		} else {
			$this->data['address'] = $address;
		}
		// nothing found, return false
		if (!$results = $this->exec()) {
			return false;
		}
		return $this->resultJSONtoArray($results->body);
	}
	
	/**
	 * Formats the Google JSON Result to a nice array with less information
	 * @param string $json
	 * @return array(string)
	 */
	private function resultJSONtoArray($json)
	{
		// additonal field mappings, googlename => alias name
		$mapping = array(
			'administrative_area_level_2' => 'city',
			'postal_code' => 'zip',
			'route' => 'street',
			'street_address' => 'street',
		);
		// iterate over found addresses and format to array
		foreach($json as $index => $found) {
			$address = array(
				'formatted_address' => $found->formatted_address,
				'lat' => $found->geometry->location->lat,
				'lng' => $found->geometry->location->lng,
				'location_type' => $found->geometry->location_type,
			);
			// address data
			foreach($found->address_components as $addressComponent) {
				$type = $addressComponent->types[0];
				$value = $addressComponent->long_name;
				$address[$type] = $value;
				// alias mapping
				if (isset($mapping[$type])) {
					$address[$mapping[$type]] = $value;
				}
			}
			$results[$index] = $address;
		}
		return $results;
	}
	
	public function exec($buffered = true)
	{
		if ($result = parent::exec($buffered)) {
			$obj = json_decode($result->body);
			if ($obj->status == 'OK') {
				return $obj->results;
			}
		}
		return false;
	}
}