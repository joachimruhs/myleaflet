<?php
namespace WSR\Myleaflet\ViewHelpers;

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


class MapJSViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	/**
	* Arguments Initialization
	*/
	public function initializeArguments() {
		$this->registerArgument('locations', 'array', 'The locations for the map', TRUE);
		$this->registerArgument('city', 'string', 'The city for the map', TRUE);
		$this->registerArgument('settings', 'mixed', 'The settings', TRUE);
	}

    /**
    * Returns the map javascript
    *
    * @return string
    */
    public function render() {
		$locations = $this->arguments['locations'];
		$city = $this->arguments['city'];
		$this->settings = $this->arguments['settings'];

		$out = $this->getMapJavascript($locations, $this->arguments['settings']);
		
		$out .= '<script type="text/javascript">
            function getMarkers() {';
			if (is_array($locations)) {


				for ($i = 0; $i < count($locations); $i++) {

					if (is_array($locations[$i])) {
						$lat = $locations[$i]['latitude'];
						$lon = $locations[$i]['longitude'];
						$mapIcon = $locations[$i]['mapicon'];
						$locationName = $locations[$i]['name'];
					} else {
						$lat = $locations[$i]->getLatitude();
						$lon = $locations[$i]->getLongitude();
						$locationName = $locations[$i]->getName();
						$mapIcon = $locations[$i]->getMapicon();
					}
					$out .= 'var myLatLng = new google.maps.LatLng(' . $lat. ',' . $lon .');';
		
		
					if ($mapIcon) {
					$out .= 'marker[' . $i . '] = new google.maps.Marker({
											position: myLatLng,
											map: map,
											title: "' . str_replace('"', '\"', $locationName) .'",
											icon: "/uploads/tx_myleaflet/icons/' . $mapIcon .'",
											' . $animation . '
											map: map
											});
											mapBounds.extend(myLatLng);
		
											';
					
					
					} else {
		
					$out .= 'marker[' . $i . '] = new google.maps.Marker({
											position: myLatLng,
											title: "' . str_replace('"', '\"', $locationName) .'",
											icon: "' . $this->settings['defaultIcon'] . '",

										' . $animation . '
											map: map
											});
											mapBounds.extend(myLatLng);
		
											';
					}
		
		
				}
			}
//            $out .= 'map.fitBounds(mapBounds);';

            $out .= '}</script>';
		return $out;
	 }
	 
	 function getMapJavascript($locations, $settings) {
        $out = '<script type="text/javascript">
        var myOptions;
        var marker = [];
        var infoWindow = [];
        var map;
        var mapBounds = new google.maps.LatLngBounds();
        
        function load(){
        
            var lon;
            var lat;
        
            var zoom1 = 9;
        
            var latlng = new google.maps.LatLng(' . $settings['initialMapCoordinates'] . ');
        
             myOptions = {
              zoom: zoom1,
              center: latlng,
        //		      mapTypeId: google.maps.MapTypeId.ROADMAP,
              scaleControl: true,
              zoomControl: true,
              zoomControlOptions: {
                    position: google.maps.ControlPosition.LEFT_TOP
                },
        
              panControl: true,
			  draggable: 1,			  
              rotateControl: true,
//              rotateControlOptions: {
//                                position: google.maps.ControlPosition.LEFT_TOP
//                            },
              disableDoubleClickZoom: 1,
			  ';


            if ($settings['mapTheme']) {
			    $themeFile = GeneralUtility::getFileAbsFileName($settings['mapTheme']);

				if (is_file($themeFile)) {
					$mapTheme = file_get_contents($themeFile);
					if (json_decode($mapTheme) == NULL) {
	//					die('Incorrect mapTheme file: ' . $settings['mapTheme']);
					} else {
		                $out .= ' styles:' . $mapTheme .',';
					}
				}
			}


            if ($settings['enableStreetViewLayer']) {                
                $out .= '  streetViewControl: 1,
                            streetViewControlOptions: {
                                position: google.maps.ControlPosition.LEFT_TOP
                            },
                        ';
            }
        
            $out .= '
              scrollwheel: true
            };
        
            map = new google.maps.Map(document.getElementById("map"), myOptions);
            if (mapBounds.length > 0)
        			map.fitBounds(mapBounds);

			// 45 degree images of cities		
			map.setTilt(45);
					
            ';
            
            if ($settings['enableBicyclingLayer']) {                
                $out .= '
                var bikeLayer = new google.maps.BicyclingLayer();
                bikeLayer.setMap(map);
                ';
            }

            if ($settings['enableTrafficLayer']) {                
                $out .= '
                var trafficLayer = new google.maps.TrafficLayer();
                trafficLayer.setMap(map);
                ';
            }

            $out .= '

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
//				   map.setCenter(event.latLng);
			   });
		

			} // load
				
				
        </script>';
        return $out;
	 }
	 

	 
}

?>