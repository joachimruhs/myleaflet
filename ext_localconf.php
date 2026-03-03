<?php
defined('TYPO3') or die();

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'Myleaflet',
            'Ajaxmap',
            [
                \WSR\Myleaflet\Controller\AddressController::class => 'ajaxSearch'
            ],
            // non-cacheable actions
            [
                \WSR\Myleaflet\Controller\AddressController::class => 'ajaxSearch'
            ]
        );

		
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
			'Myleaflet',
            'SingleView',
            [
                \WSR\Myleaflet\Controller\AddressController::class => 'show'
            ],
            // non-cacheable actions
            [
                \WSR\Myleaflet\Controller\AddressController::class => 'show'
            ]
        );

    }
);

// for use with extension extender 
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_address']['extender'][\FriendsOfTYPO3\TtAddress\Domain\Model\Address::class]['ext_ttaddress']
//    = 'EXT:myleaflet/Classes/Domain/Model/Address.php';

	