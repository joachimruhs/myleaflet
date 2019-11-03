<?php

namespace WSR\myleaflet\Eid;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Service\TypoScriptService;


/***************************************************************
 *
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

/**
 * This class could called via eID
 */
class AjaxBootstrap {
	
	/**
   * @var \array
   */
	protected $configuration;
	
	/**
   * @var \array
   */
	protected $bootstrap;
	
	/**
   * The main Method
   *
   * @return \string
   */
	public function run() {
		return $this->bootstrap->run( '', $this->configuration );
	}
	
	/**
   * Initialize Extbase
   *
   * @param \array $TYPO3_CONF_VARS
   */
	public function __construct($TYPO3_CONF_VARS) {

	
		if (! $_POST['tx_myaddressmap_ajax']['action']) { // set default action, if not set
			$_POST['tx_myaddressmap_ajax']['action'] = 'ajaxEid';
		}

		$_POST['tx_myaddressmap_ajax']['controller'] = 'Ajax'; // set controller
		
		// create bootstrap
		$this->bootstrap = new \TYPO3\CMS\Extbase\Core\Bootstrap();
		

		// get User
		$feUserObj = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();

		// set PID
		$pid = (GeneralUtility::_GET( 'id' )) ? GeneralUtility::_GET( 'id' ) : 1;
		
// J. Ruhs
// Fehler bei initTemplate()
//echo '********' . GeneralUtility::_GET( 'id' );

		// Create and init Frontend
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance( 'TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $TYPO3_CONF_VARS, $pid, 0, TRUE );
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->fe_user = $feUserObj;
		$GLOBALS['TSFE']->id = $pid;
		$GLOBALS['TSFE']->determineId();
//		$GLOBALS['TSFE']->getCompressedTCarray();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();
//		$GLOBALS['TSFE']->includeTCA();
		\TYPO3\CMS\Frontend\Utility\EidUtility::initTCA();
		
		// Get Plugins TypoScript
//		$TypoScriptService = new \TYPO3\CMS\Extbase\Service\TypoScriptService();

// for TYPO3 8.7
		$pluginConfiguration = $this->convertTypoScriptArrayToPlainArray($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_myleaflet.']);

		// Set configuration to call the plugin
		$this->configuration = array (
				'pluginName' => 'Ajax',
				'vendorName' => 'WSR',
				'extensionName' => 'Myleaflet',
				'controller' => 'Ajax',
				'action' => $_POST['tx_myleaflet_ajax']['action'],
				'mvc' => array (
						'requestHandlers' => array (
								'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler' => 'TYPO3\CMS\Extbase\Mvc\Web\FrontendRequestHandler'
						)
				),
				'settings' => $pluginConfiguration['settings'],
				'persistence' => array (
						'storagePid' => $pluginConfiguration['persistence']['storagePid']
				)
		);

	}


    /**
     * Removes all trailing dots recursively from TS settings array
     *
     * Extbase converts the "classical" TypoScript (with trailing dot) to a format without trailing dot,
     * to be more future-proof and not to have any conflicts with Fluid object accessor syntax.
     *
     * @param array $typoScriptArray The TypoScript array (e.g. array('foo' => 'TEXT', 'foo.' => array('bar' => 'baz')))
     * @return array e.g. array('foo' => array('_typoScriptNodeValue' => 'TEXT', 'bar' => 'baz'))
     * @api
     */
    public function convertTypoScriptArrayToPlainArray(array $typoScriptArray)
    {
        foreach ($typoScriptArray as $key => $value) {
            if (substr($key, -1) === '.') {
                $keyWithoutDot = substr($key, 0, -1);
                $typoScriptNodeValue = isset($typoScriptArray[$keyWithoutDot]) ? $typoScriptArray[$keyWithoutDot] : null;
                if (is_array($value)) {
                    $typoScriptArray[$keyWithoutDot] = $this->convertTypoScriptArrayToPlainArray($value);
                    if (!is_null($typoScriptNodeValue)) {
                        $typoScriptArray[$keyWithoutDot]['_typoScriptNodeValue'] = $typoScriptNodeValue;
                    }
                    unset($typoScriptArray[$key]);
                } else {
                    $typoScriptArray[$keyWithoutDot] = null;
                }
            }
        }
        return $typoScriptArray;
    }





}

global $TYPO3_CONF_VARS;

// make instance of bootstrap and run
$eid = GeneralUtility::makeInstance( 'WSR\myleaflet\Eid\AjaxBootstrap', $TYPO3_CONF_VARS );
echo $eid->run();
?>