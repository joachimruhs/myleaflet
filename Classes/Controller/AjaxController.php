<?php

namespace WSR\Myleaflet\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Core\Environment;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
//use Psr\Http\Server\MiddlewareInterface;
//use Psr\Http\Server\RequestHandlerInterface;

use FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository;

//use TYPO3\CMS\Core\Http\NullResponse;
//use TYPO3\CMS\Core\Http\Response;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Joachim Ruhs 2018-2019
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

/**
 *
 *
 * @package myleaflet
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * 
 */
class AjaxController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var LanguageService
	 */
	public $languageService;
	
	/**
	 * AjaxController constructor.
	 */
	public function __construct()
	{
		/** @var LanguageService $this->languageService */
		$this->languageService = GeneralUtility::makeInstance('TYPO3\CMS\Core\Localization\LanguageService');
		$this->languageService->init(trim($_POST['tx_myleaflet_ajax']['language']));
	}


	/**
	 * AddressRepository
	 *
	 * @var \WSR\Myleaflet\Domain\Repository\AddressRepository
	 */
	protected $addressRepository;
	
    /**
     * Inject a addressRepository to enable DI
     *
     * @param \WSR\Myleaflet\Domain\Repository\AddressRepository $addressRepository
     * @return void
     */
    public function injectAddressRepository(\WSR\Myleaflet\Domain\Repository\AddressRepository $addressRepository) {
        $this->addressRepository = $addressRepository;
    }



	/**
	 * TTAddressRepository
	 *
	 * @var \FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository
	 */
	protected $ttaddressRepository;

	
    /**
     * Inject a ttaddressRepository to enable DI
     *
     * @param \FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository $ttaddressRepository
     * @return void
     */
    public function injectTtAddressRepository(\FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository $ttaddressRepository) {
        $this->ttaddressRepository = $ttaddressRepository;
    }

	/**
	 * categoryRepository
	 *
	 * @var \WSR\Myleaflet\Domain\Repository\CategoryRepository
	 */
	protected $categoryRepository;
	
    /**
     * Inject a categoryRepository to enable DI
     *
     * @param \WSR\Myleaflet\Domain\Repository\CategoryRepository $categoryRepository
     * @return void
     */
    public function injectCategoryRepository(\WSR\Myleaflet\Domain\Repository\CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }
	

	/**
	 * action ajaxPage
	 * @return \string JSON
	 */
	public function ajaxPageAction() {
		// not used yet 
		$requestArguments = $this->request->getArguments();
		return json_encode($requestArguments);
	}
	
	/**
	 * action ajaxEidGeocode
	 * @return \stdclass $latLon
	 */
	public function ajaxEidGeocodeAction() {
		$requestArguments = $this->request->getParsedBody()['tx_myleaflet_ajax'];

		$address = urlencode($requestArguments['address']);
		$country = urlencode($requestArguments['country']);

/*
https://nominatim.openstreetmap.org/search/elzstr.%2010%20rheinhausen?format=json&addressdetails=1&limit=1&polygon_svg=1
max 1 call/sec
*/

		$apiURL = "https://nominatim.openstreetmap.org/search?q=$address,$country&format=json&limit=1";
		
		$addressData = $this->get_webpage($apiURL);
		
		$coordinates[1] = json_decode($addressData)[0]->lat;
		$coordinates[0] = json_decode($addressData)[0]->lon;

		$latLon = new \stdClass();
		$latLon->lat = (float) $coordinates[1];
		$latLon->lon = (float) $coordinates[0];
		if ($latLon->lat) 
			$latLon->status = 'OK';
		else 
			$latLon->status = 'NOT FOUND';

		return $latLon;
	}



	function get_webpage($url) {
		$sessions = curl_init();
		curl_setopt($sessions, CURLOPT_URL, $url);
		curl_setopt($sessions, CURLOPT_HEADER, 0);
		curl_setopt($sessions, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($sessions, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
		$data = curl_exec($sessions);
		curl_close($sessions);
		return $data;
	}


	/**
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @param \Psr\Http\Message\ResponseInterface      $response
	 */
	public function indexAction(ServerRequestInterface $request)
	{
		switch ($request->getMethod()) {
			case 'GET':
				$this->processGetRequest($request, $response);
				break;
			case 'POST':
				$this->processPostRequest($request, $response);
				break;
			default:
				$response->withStatus(405, 'Method not allowed');
		}
	
		return $response;
	}

	/**
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @param \Psr\Http\Message\ResponseInterface      $response
	 */
	protected function processGetRequest(ServerRequestInterface $request, ResponseInterface $response) {
//		$view = $this->getView();
	
		$response->withHeader('Content-type', ['text/html; charset=UTF-8']);
		$response->getBody()->write($view->render());
	}

	/**
	 * @param \Psr\Http\Message\ServerRequestInterface $request
	 * @param \Psr\Http\Message\ResponseInterface      $response
	 */
	protected function processPostRequest(ServerRequestInterface $request, $response)
	{
		$queryParams = $request->getQueryParams();
	
//		$queryParameters = $request->getParsedBody();
//		$pid = (int)$queryParameters['pid'];
//		$queryParams = $queryParameters;
	
		$frontend = $GLOBALS['TSFE'];
//print_r($frontend->tmpl->setup['plugin.']['tx_myleaflet.']);

		/** @var TypoScriptService $typoScriptService */
		$typoScriptService = GeneralUtility::makeInstance('TYPO3\CMS\Core\TypoScript\TypoScriptService');
		$this->configuration = $typoScriptService->convertTypoScriptArrayToPlainArray($frontend->tmpl->setup['plugin.']['tx_myleaflet.']);
		$this->settings = $this->configuration['settings'];
		$this->conf['storagePid'] = $this->configuration['persistence']['storagePid'];
	
		$this->request = $request;
		$out = $this->ajaxEidAction();
	
		echo $out;
		return $response;

		//    $response->getBody()->write(json_encode($queryParams));
		//    $response->getBody()->write($out);
		
		/** @var Response $response */
		//$response = GeneralUtility::makeInstance(Response::class);
		//$response->getBody()->write($out);
		
		//return $response;
/*		
		$view = $this->getView();
		$hasErrors = false;
		// ... some logic
	
		if ($hasErrors) {
			$response->withHeader('Content-type', ['text/html; charset=UTF-8']);
			$response->getBody()->write($view->render());
		} else {
			$response->withHeader('Content-type', ['application/json; charset=UTF-8']);
			$response->getBody()->write(json_encode(['success' => true]));
		}
*/
	}


	/**
	 * @return \TYPO3\CMS\Fluid\View\StandaloneView
	 */
	protected function getView() {
	//    $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
		$templateService = GeneralUtility::makeInstance(TemplateService::class);
		// get the rootline
	//    $rootLine = $pageRepository->getRootLine($pageRepository->getDomainStartPage(GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY')));
		$rootlineUtility = GeneralUtility::makeInstance(RootlineUtility::class, 0);
	
		$rootLine = $rootlineUtility->get();
	
		// initialize template service and generate typoscript configuration
		$templateService->init();
		$templateService->runThroughTemplates($rootLine);
		$templateService->generateConfig();
	
		$fluidView = new StandaloneView();
		$fluidView->setLayoutRootPaths($templateService->setup['plugin.']['tx_yourext.']['view.']['layoutRootPaths.']);
		$fluidView->setTemplateRootPaths($templateService->setup['plugin.']['tx_yourext.']['view.']['templateRootPaths.']);
		$fluidView->setPartialRootPaths($templateService->setup['plugin.']['tx_yourext.']['view.']['partialRootPaths.']);
		$fluidView->getRequest()->setControllerExtensionName('YourExt');
		$fluidView->setTemplate('index');
	
		return $fluidView;
	}


	/**
	 * action ajaxEid
	 * @return \string html
	 */
	public function ajaxEidAction() {
		$requestArguments = $this->request->getParsedBody()['tx_myleaflet_ajax'];

		if ($requestArguments['categories'])
		$this->_GP['categories'] = @implode(',', $requestArguments['categories']);
		// sanitizing categories						 
		if ($this->_GP['categories'] && preg_match('/^[0-9,]*$/', @implode(',', $this->_GP['categories'])) != 1) {
			$this->_GP['categories'] = '';
		}		
		
		if ($this->settings['defaultLanguageUid'] > '') {
			$this->language = $this->settings['defaultLanguageUid'];
		} else {
			$this->language = $requestArguments['language'];		
		}		


		$latLon = $this->ajaxEidGeocodeAction();

		if ($latLon->status != 'OK') {
			if ($latLon->status ==  '') $latLon->status = 'There was no status from geocoding returned.';

			$out = '<div class="ajaxMessage">Geocoding Error: ' . $latLon->status . '</div>';
			$out .= '<script	type="text/javascript">
			$(".ajaxMessage").fadeIn(2000);
			</script>';
			return $out;
		} else {
/*
 			$out .= '<script	type="text/javascript">
				$("#tx_myleaflet_lat").val(' . $latLon->lat . ');
				$("#tx_myleaflet_lon").val(' . $latLon->lon . ');
			</script>';
*/			
		}

		$this->_GP['radius'] = (float) $requestArguments['radius'];

		$limit = $this->settings['resultLimit'];

		$page = intval($requestArguments['page']);
		if ($page == -1) {
			$limit = 1000;
			$page = 0;
		}

		if (!$requestArguments['address']) {
			$orderBy = 'city';
		} else {
			$orderBy = 'distance';
		}			

		$categoryMode = $this->settings['categorySelectMode'];
		
		$locations = $this->addressRepository->findLocationsInRadius($latLon, $this->_GP['radius'], $this->_GP['categories'], $this->conf['storagePid'], $this->language, $limit, $page, $orderBy, $categoryMode);
		$allLocations = $this->addressRepository->findLocationsInRadius($latLon, $this->_GP['radius'], $this->_GP['categories'], $this->conf['storagePid'], $this->language, 1000, 0, $orderBy, $categoryMode);


		// field images
		if (is_array($locations)) {
			for ($i = 0; $i < count($locations); $i++) {
				$locations[$i]['infoWindowDescription'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $locations[$i]['description']);  
				$locations[$i]['description'] = str_replace(array("\r\n", "\r", "\n"), '<br />', htmlspecialchars($locations[$i]['description'], ENT_QUOTES));
				$address = $locations[$i]['address'];
				$locations[$i]['address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', $locations[$i]['address']);  
	
				$locations[$i]['infoWindowAddress'] = str_replace(array("\r\n", "\r", "\n"), '<br />', htmlspecialchars($address, ENT_QUOTES));
	
				if ($locations[$i]['image'] > 0) {
					if ($this->ttaddressRepository->findByUid($locations[$i]['uid'])) {
						$images = $this->ttaddressRepository->findByUid($locations[$i]['uid'])->getImage();
					}
					$locations[$i]['images'] =	$images;				
				}
			}
		}
		if (!is_array($locations) || count($locations) == 0) {
			$out = '<div class="ajaxMessage">' . \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('noLocationsFound', 'myleaflet') .'</div>';
			$out .= '<script	type="text/javascript">';
			// remove marker from map
			$out .= '
				for (var i = 0; i < marker.length; i++) {
					map.removeLayer(marker[i]);
				}
				marker = [];
				map.removeLayer(markerClusterGroup);
				markerClusterGroup = L.markerClusterGroup();
				$(".ajaxMessage").fadeIn(2000);
				</script>';
			return $out;
		}
	
		$out .= $this->getMarkerJS($locations, $categories, $latLon, $this->_GP['radius']);
		
		// get  the loctions list
		
		if ($requestArguments['page'] != -1) { // do not show the list for page loading 
			$labels = [
				'distance' => $this->translate('distance'),
				'address' => $this->translate('address'),
				'zip' => $this->translate('zip'),
				'city' => $this->translate('city'),
				'country' => $this->translate('country'),
				'phone' => $this->translate('phone'),
				'email' => $this->translate('email'),
				'fax' => $this->translate('fax'),
				'route' => $this->translate('route'),

			];
			$out .= $this->getLocationsList($locations, $categories, $allLocations, $labels);
		}
		
		return $out;
	}


	function getChildren($arr, $id, $children) {
		for ($i = 0; $i < count($arr); $i++) {
			if ($arr[$i]['parent'] == $id) {
//				$children .= ',' . $arr[$i]['uid'];
				$children = $this->getChildren($arr, $arr[$i]['uid'], $children);
			}
		}
		
		return $id . ',' . $children;
//		return $children;
	}


	protected function getMarkerJS($locations, $categories, $latLon, $radius) {

		$out = '<script	type="text/javascript">';

		// remove marker from map
		$out .= 'var markerGroup = L.featureGroup(); //.addTo(map);
			for(i=0;i<marker.length;i++) {
				map.removeLayer(marker[i]);
				markerClusterGroup.removeLayer(marker[i]);
			}
			marker = [];
			markerClusterGroup = L.markerClusterGroup();
			';
			
		for ($i = 0; $i < count($locations); $i++) {
			$lat = $locations[$i]['latitude'];
			$lon = $locations[$i]['longitude'];
			
			if (!$lat) continue;

			if ($locations[$i]['leafletmapicon']) {
			$out .= '
		
				var mapIcon' . $i . ' = L.icon({
					iconUrl: "/fileadmin/ext/myleaflet/Resources/Public/Icons/' . $locations[$i]['leafletmapicon'] .'",
					iconSize:     [25, 41], // size of the icon
					iconAnchor:   [12, 41]
				});
				marker[' . $i . '] = L.marker([' . $lat . ',' . $lon . '], {icon: mapIcon' . $i . '}).addTo(markerGroup);
			';
			
			} else {
				$out .= "marker[$i] = L.marker([$lat, $lon]).addTo(markerGroup);
				";
			}

			// infoWindows
			$out .= $this->renderFluidTemplate('AjaxLocationListInfoWindow.html', array('location' => $locations[$i], 'categories' => $categories, 'i' => $i,
																						'startingPoint' => $latLon, 'settings' => $this->settings));
			
		} // for

		if ($this->settings['enableMarkerClusterer'] == 1) {
			$out .= '
			markerClusterGroup = L.markerClusterGroup();
			markerClusterGroup.clearLayers();
			map.removeLayer(markerClusterGroup);
			markerClusterGroup = L.markerClusterGroup();
			for (var i = 0; i < marker.length; i++) {
				markerClusterGroup.addLayer(marker[i]);
			}
			map.addLayer(markerClusterGroup);
			map.fitBounds(markerClusterGroup.getBounds());
			';				
		} else {
			$out .= 'markerGroup = L.featureGroup(marker).addTo(map);
					map.fitBounds(markerGroup.getBounds());';
		}
		return $out . '</script>';
	}
	

	function getLocationsList($locations, $categories, $allLocations, $labels) {
		$out .= $this->renderFluidTemplate('AjaxLocationList.html', array('locations' => $locations, 'categories' => $categories, 'labels' => $labels,
											  'settings' => $this->settings, 'locationsCount' => count($allLocations)));
		return $out;
	}
	
	
	/**
	 * Renders the fluid template
	 * @param string $template
	 * @param array $assign
	 * @return string
	 */
	public function renderFluidTemplate($template, Array $assign = array()) {
      	$configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);

		$templateRootPath = $this->configuration['view']['templateRootPaths'][1];

		if (!$templateRootPath) 	
		$templateRootPath = $this->configuration['view']['templateRootPath'][0];
		
		$templatePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($templateRootPath . 'Address/' . $template);
		$view = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setTemplatePathAndFilename($templatePath);
		$view->assignMultiple($assign);
		return $view->render();
	}


	/**
	 * Returns the translation of $key
	 *
	 * @param string $key
	 * @return string
	 */
	protected function translate($key)
	{
		return $this->languageService->sL('LLL:EXT:myleaflet/Resources/Private/Language/locallang.xlf:' . $key);
	}

	
}

?>