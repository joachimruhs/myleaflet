<?php
defined('TYPO3') or die();

$tmp_myleaflet_columns = array(

	'leafletmapicon' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:myleaflet/Resources/Private/Language/locallang_db.xlf:tx_myleaflet_domain_model_address.leafletmapicon',
		'config' => [
		    'type' => 'select',
			'renderType' => 'selectSingle',
		    'items' => [
		        [ '', 0 ],
			],
			'fileFolder' => 'fileadmin/ext/myleaflet/Resources/Public/Icons/',
			'fileFolder_extList' => 'png,jpg,jpeg,gif',
			'fileFolder_recursions' => 0,
			'fieldWizard' => [
	            'selectIcons' => [
	                'disabled' => false,
	            ],
	        ],
		    'size' => 1,
		    'minitems' => 0,
		    'maxitems' => 1,
		],

	),
	'mapgeocode' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:myleaflet/Resources/Private/Language/locallang_db.xlf:tx_myleaflet_domain_model_address.geocode',
		'config' => array(
			'type' => 'check',
			'default' => '1',
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tt_address',$tmp_myleaflet_columns);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tt_address', 'leafletmapicon,mapgeocode;;,', '', 'after:title');

        

