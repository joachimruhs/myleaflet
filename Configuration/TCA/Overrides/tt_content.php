<?php
defined('TYPO3_MODE') or die();

/*********
 * Plugins
 */

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
