<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function($extKey)
	{

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WSR.Myleaflet',
            'Ajaxmap',
            [
                'Address' => 'ajaxSearch'
            ],
            // non-cacheable actions
            [
                'address' => 'ajaxSearch'
            ]
        );

		
		// Plugin for AJAX-calls
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
				'WSR.' . $extKey,
				'Ajax',
				array(
						'Ajax' => 'ajaxEid'
				),
				// non-cacheable actions
				array(
						'Ajax' => 'ajaxEid'
				)
		);
		
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WSR.Myleaflet',
            'SingleView',
            [
                'Address' => 'show'
            ],
            // non-cacheable actions
            [
                'address' => 'show'
            ]
        );
		




		
	// wizards
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
		'mod {
			wizards.newContentElement.wizardItems.plugins {
				elements {
					ajaxmap {
						iconIdentifier = extension-myleaflet-content-element
						title = LLL:EXT:myleaflet/Resources/Private/Language/locallang_db.xlf:tx_myleaflet_domain_model_ajaxmap
						description = LLL:EXT:myleaflet/Resources/Private/Language/locallang_db.xlf:tx_myleaflet_domain_model_ajaxmap.description
						tt_content_defValues {
							CType = list
							list_type = myleaflet_ajaxmap
						}
					}
				}
				show = *
			}
	   }'
	);
    },
    $_EXTKEY
);

/**
 * Register eID for ajax action-call
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['myleaflet'] = 'EXT:myleaflet/Classes/Eid/AjaxBootstrap.php';
