<?php
namespace WSR\Myttaddressmap\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


class MapShowJSViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	* Arguments Initialization
	*/
	public function initializeArguments() {
		$this->registerArgument('location', 'mixed', 'The locations for the map', TRUE);
		$this->registerArgument('city', 'string', 'The city for the map', TRUE);
		$this->registerArgument('settings', 'mixed', 'The settings', TRUE);
	}


     /**
	 * Returns the map javascript
	 *
	 * @return string
	 */
	 public function render() {
		 $location = $this->arguments['location'];
		 $city = $this->arguments['city'];
		 $this->settings = $this->arguments['settings'];
	 
		$out = $this->getMapJavascript($location, $this->arguments['settings']);
		$out .= '<script type="text/javascript">function getMarkers() {';
			$lat = $location->getLatitude();
			$lon = $location->getLongitude();
			
			$out .= 'var myLatLng = new google.maps.LatLng(' . $lat. ',' . $lon .');';

			if ($location->getMapicon()) {
 			$out .= 'marker[0] = new google.maps.Marker({
					                position: myLatLng,
					                map: map,
					                title: "' . str_replace('"', '\"', $location->getName()) .'",
					                icon: "/uploads/tx_myleaflet/icons/' . $location->getMapicon() .'"
					                });
									//mapBounds.extend(myLatLng);

									';
			
			
			} else {

 			$out .= 'marker[0] = new google.maps.Marker({
					                position: myLatLng,
					                map: map,
					                title: "' . str_replace('"', '\"', $location->getName()) .'",
									icon: "' . $this->settings['defaultIcon'] . '"
					                });
									//mapBounds.extend(myLatLng);
									';
			}

		$out .= '}</script>';
		return $out;
	 }
	 
	 function getMapJavascript($location, $settings) {
		$out = '<script type="text/javascript">
		var myOptions;
		var marker = [];
		var infoWindow = [];
		var map;
        var mapBounds = new google.maps.LatLngBounds();

		function load(){
			var circle = null;
		    var circleRadius = 1.5; // Miles

			var lon;
			var lat;

			var zoom1 = 16;

		    var latlng = new google.maps.LatLng(' . $location->getLatitude() . ',' . $location->getLongitude() . ');
		     myOptions = {
		      zoom: zoom1,
		      center: latlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP,
		      scaleControl: 1,
			  zoomControl: 1,

		//	  panControl: false,
		      disableDoubleClickZoom: 1,
			  scrollwheel: true,
			';

            if ($settings['mapTheme']) {
			    $themeFile = GeneralUtility::getFileAbsFileName($settings['mapTheme']);
				if (is_file($themeFile)) {
					$mapTheme = file_get_contents($themeFile);
					if (json_decode($mapTheme) == NULL) {
						// all is ok
					} else {
		                $out .= ' styles:' . $mapTheme .',';
					}
				}
			}

			$out .= '			
		 	  streetViewControl: 1
		    };
		    map = new google.maps.Map(document.getElementById("map"), myOptions);
//			map.fitBounds(mapBounds);


		function addMarker(location) {
		  marker = new google.maps.Marker({
		    position: location,
		    map: map
		  });
		  markersArray.push(marker);
		}

		function removeMarker(marker) {
			if(marker.setMap != null) marker.setMap(null);
		}

		function showMarker(marker) {
		     marker.setMap(map);
		}

			getMarkers();

		// panning for mobile devices
		google.maps.event.addListener(map, "click",function(event) {
		   //map.setZoom(9);
//		   map.setCenter(event.latLng);
	   });
			
		} // load
		</script>';
		return $out;
	 }
	 
	 
}

?>