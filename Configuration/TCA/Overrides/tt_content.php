<?php
defined('TYPO3') or die();

/*********
 * Plugins
 */

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Myleaflet',
    'Ajaxmap',
    'MyLeaflet (AjaxMap)'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Myleaflet',
    'SingleView',
    'MyLeaflet (SingleView)'
);
