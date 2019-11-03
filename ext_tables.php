<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'WSR.Myleaflet',
            'Ajaxmap',
            'MyLeaflet (AjaxMap)'
        );

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'WSR.Myleaflet',
            'SingleView',
            'MyLeaflet (SingleView)'
        );

        
        
        

        
        

/**
 * Register icons
 */
$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
	'extension-myleaflet-content-element',
	\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
	['source' => 'EXT:myleaflet/Resources/Public/Icons/contentElementIcon.png']
);



        
        
    },
    $_EXTKEY
);
