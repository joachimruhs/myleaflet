<?php
namespace WSR\Myleaflet\Controller;

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;


/***
 *
 * This file is part of the "Myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 - 2021 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *
 ***/

/**
 * AddressController
 */
class AddressController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
	public function initializeObject() {

		//		$this->_GP = $this->request->getArguments();
		$configuration = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$this->conf['storagePid'] = $configuration['persistence']['storagePid'];
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
	 * categoryRepository
	 * not used anymore
	 *
	 * @var \WSR\Myleaflet\Domain\Repository\CategoryRepository
	 */
	protected $categoryRepository;

	
    /**
     * Inject a categoryRepository to enable DI
	 * not used anymore
     *
     * @param \WSR\Myleaflet\Domain\Repository\CategoryRepository $categoryRepository
     * @return void
     */
    public function injectCategoryRepository(\WSR\Myleaflet\Domain\Repository\CategoryRepository $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }


	/**
	 * typo3CategoryRepository
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository
	 */
	protected $typo3CategoryRepository;
	
    /**
     * Inject a categoryRepository to enable DI
     *
     * @param \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository $typo3CategoryRepository
     * @return void
     */
    public function injectTypo3CategoryRepository(\TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository $typo3CategoryRepository) {
        $this->typo3CategoryRepository = $typo3CategoryRepository;
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
     * action show
     * 
     * @return void
     */
    public function showAction()
    {
		$this->_GP = $this->request->getArguments();
		if ($this->_GP['locationUid']) {// called from list link
			$address = $this->ttaddressRepository->findByUid(intval($this->_GP['locationUid']));
//			$address = $this->ttaddressRepository->findAll();
		}
		else {
			$address = $this->ttaddressRepository->findByUid(intval($this->settings['singleViewUid']));
		}
        $this->view->assign('address', $address);
    }

    /**
     * action list
     * 
     * @return void
     */
    public function listAction()
    {
        $addresses = $this->addressRepository->findAll();
        $this->view->assign('addresses', $addresses);
    }

	/*
	 * build the category tree
	 *
	 * @var array $elements
	 * @var int $parentId
	 *
	 * @return array
	 */
	function buildTree(array &$elements, $parentId = 0) {
		$branch = array();
		foreach ($elements as &$element) {
			if ($element['parent'] == $parentId) {
				$children = $this->buildTree($elements, $element['uid']);
				if ($children) {
					$element['children'] = $children;
				}
				$branch[$element['uid']] = $element;
				unset($element);
			}
		}
		return $branch;
	}




    /**
     * action ajaxSearch
     * 
     * @return void
     */
    public function ajaxSearchAction()
    {

/*********************************************************/
// alternative geocoder
//https://nominatim.openstreetmap.org/search/elzstr.%2010%20rheinhausen?format=json&addressdetails=1&limit=1&polygon_svg=1//
//			$addressData = $this->get_webpage($apiURL);
//        $addresses = $this->addressRepository->findAll();
    
/*
$radius = 1000;
$storagePid = $this->conf['storagePid'];
$limit = 300;
$page = 0;

$addresses = $this->addressRepository->findLocationsInRadius($latLon, $radius, $categories, $storagePid, $limit, $page, $orderBy = 'distance');

		$categories = $this->categoryRepository->findAll();
*/
		$iconPath = 'fileadmin/ext/myleaflet/Resources/Public/Icons/';
		if (!is_dir(Environment::getPublicPath() . '/' . $iconPath)) {
			$this->addFlashMessage('Directory ' . $iconPath . ' created for use with own mapIcons!', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::INFO);
			GeneralUtility::mkdir_deep(Environment::getPublicPath() . '/' . $iconPath);
			$sourceDir = 'typo3conf/ext/myleaflet/Resources/Public/MapIcons/';
			$files = GeneralUtility::getFilesInDir($sourceDir, 'png,gif,jpg');			
			foreach ($files as $file) {
				copy($sourceDir . $file, $iconPath . $file);
			}
		}
		
		
		// Get the default Settings
		$customStoragePid = $this->conf['storagePid'];
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\Typo3QuerySettings');

		$querySettings->setRespectStoragePage(true);
		$querySettings->setStoragePageIds(array($customStoragePid));

		$addresses = $this->ttaddressRepository->findAll();

		if ($this->settings['defaultLanguageUid'] > '') {
			$querySettings->setLanguageUid($this->settings['defaultLanguageUid']);
		}

		$this->typo3CategoryRepository->setDefaultQuerySettings($querySettings);
		$this->typo3CategoryRepository->setDefaultOrderings(array('sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING));
		$categories = $this->typo3CategoryRepository->findAll();

		for($i = 0; $i < count($categories); $i++) {

			// process only sys_categories of storagePid
			if (! GeneralUtility::inList($customStoragePid, $categories[$i]->getPid())) continue;

			$arr[$i]['uid']= $categories[$i]->getUid();
			if ($categories[$i]->getParent()) {
				$arr[$i]['parent'] = $categories[$i]->getParent()->getUid();
			} else $arr[$i]['parent'] = 0;
				
			$arr[$i]['title'] = $categories[$i]->getTitle();
		}
		if (!$arr) {
			$this->addFlashMessage('Please insert some sys_categories first!', 'Myleaflet', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
			return;
		}
		$categories = $this->buildTree($arr);

		$languageAspect = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Context\Context::class)->getAspect('language');
		$sys_language_uid = $languageAspect->getId();
		$this->view->assign('L', $sys_language_uid);

        $this->view->assign('id' , $GLOBALS['TSFE']->page['uid']);
        $this->view->assign('categories' , $categories);
        $this->view->assign('addresses' , $addresses);
		$this->view->assign('locationsCount', count($addresses));
    
    }


	function get_webpage($url) {
		if (ini_get('allow_url_fopen'))
			$this->conf['useCurl'] = 0;
		else
			$this->conf['useCurl'] = 1;

		if ($this->conf['useCurl']) {
			$sessions = curl_init();
			curl_setopt($sessions, CURLOPT_URL, $url);
			curl_setopt($sessions, CURLOPT_HEADER, 0);
			curl_setopt($sessions, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($sessions);
			curl_close($sessions);
		} else {
			$data = \TYPO3\CMS\Core\Utility\GeneralUtility::getURL($url); 
		}
		return $data;
	}




}
