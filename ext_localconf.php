<?php
defined('TYPO3_MODE') || die('Access denied.');

call_user_func(
    function()
    {

        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'WSR.Myleaflet',
            'Ajaxmap',
            [
                'Address' => 'ajaxSearch'
            ],
            // non-cacheable actions
            [
                'Address' => 'ajaxSearch'
            ]
        );

		
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
			'WSR.Myleaflet',
            'SingleView',
            [
                'Address' => 'show'
            ],
            // non-cacheable actions
            [
                'Address' => 'show'
            ]
        );
		

		
		
    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    ajaxmap {
                        iconIdentifier = myleaflet-plugin-ajaxmap
                        title = LLL:EXT:myleaflet/Resources/Private/Language/locallang.xlf:tx_myleaflet_ajaxmap.name
                        description = LLL:EXT:myleaflet/Resources/Private/Language/locallang.xlf:tx_myleaflet_ajaxmap.description
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

		$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
		
		$iconRegistry->registerIcon(
			'myleaflet-plugin-ajaxmap',
			\TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
			['source' => 'EXT:myleaflet/Resources/Public/Icons/pointerGreen.png']
		);


		
    }
);

// for use with extension extender 
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_address']['extender'][\FriendsOfTYPO3\TtAddress\Domain\Model\Address::class]['ext_ttaddress']
//    = 'EXT:myleaflet/Classes/Domain/Model/Address.php';

	