<?php
defined('TYPO3') or die();

call_user_func(
    function()
    {
		/**
		 * Register icons
		 */

		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		$iconRegistry->registerIcon(
			'extension-myleaflet-content-element',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:myleaflet/Resources/Public/Icons/contentElementIcon.png']
		);

	    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_myleaflet_domain_model_address', 'EXT:myleaflet/Resources/Private/Language/locallang_csh_tx_myleaflet_domain_model_address.xlf');
	    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_myleaflet_domain_model_address');

	    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_myleaflet_domain_model_category', 'EXT:myleaflet/Resources/Private/Language/locallang_csh_tx_myleaflet_domain_model_category.xlf');
	    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_myleaflet_domain_model_category');

    }
);
