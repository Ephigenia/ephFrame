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
 * @link        http://code.marceleichner.de/projects/ephFrame/
 * @version		$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @filesource		$HeadURL$
 */

/**
 * Component for handling with geo coordinates such as longitudes and latitudes
 * 
 * This component takes the earth as perfect sphere to make the calculations
 * more ease. So the distances on the Equator are calculated the most correct
 * way, but the coordinates more in north or south are calculated on a "rough"
 * basis. 
 * 
 * @author Marcel Eichner // Ephigenia <love@ephigenia.de
 * @since 12.02.2008
 * @package ephFrame
 * @subpackage ephFrame.lib.component
 */
class GEOCoords extends AppComponent {
	
	/**
	 * Stores the constant radius at the equator
	 * @var float
	 */
	const EQUATORIAL_RADIUS_KM = 6378.137;
	
	/**
	 * Translates a degree, minute, second, hemisphere set of arguments to
	 * the latitude and returns it as float value.
	 *
	 * @param float $degree
	 * @param float $minutes
	 * @param float $seconds
	 * @param string $hemisphere
	 * @return float
	 */
	public static function toLatitude ($degree, $minutes, $seconds, $hemisphere) {  
		if (strcasecmp($hemisphere,'s')) {
			return -($degree + ($minutes / 60) + ($seconds / 3600));  
		} else {
			return $degree + ($minutes / 60) + ($seconds / 3600);
		}
	}
	
	/**
	 * Translates a degree, minute, second, hemisphere set of arguments to
	 * the longitude and returns it as float value.
	 *
	 * @param float $degree
	 * @param float $minutes
	 * @param float $seconds
	 * @param string $hemisphere
	 * @return float
	 */
    public static function toLongitude ($degree, $minutes, $seconds, $hemisphere) {
    	if (strcasecmp($hemisphere, 'w')) {
			return -($degree + ($minutes / 60) + ($seconds / 3600));
    	} else {
    		return $degree + ($minutes / 60) + ($seconds / 3600);	
    	}
    }
    
    /**
     * Calculate the distance between to geo coordinates in kilometers and
     * return it.
     * @return float km distance between the geo coordinates
     */
    public static function getDistance ($latitudeA, $longitudeA, $latitudeB, $longitudeB) {  
        $latA = $latitudeA  / 180 * pi();
        $lonA = $longitudeA / 180 * pi();
        $latB = $latitudeB  / 180 * pi();
        $lonB = $longitudeB / 180 * pi();
        return acos (sin($latA) * sin($latB) + cos($latA) * cos($latB) * cos($lonB-$lonA)) * self::EQUATORIAL_RADIUS_KM;  
    } 
	
}