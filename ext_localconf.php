<?php
defined('TYPO3_MODE') || die('Access denied.');

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
		

		
		
    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    ajaxmap {
                        iconIdentifier = extension-myleaflet-content-element
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

	
	// wizards
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
		'mod {
			wizards.newContentElement.wizardItems.plugins {
				elements {
					ajaxsearch {
						iconIdentifier = extension-myttaddressmap-content-element
						title = LLL:EXT:myttaddressmap/Resources/Private/Language/locallang_db.xlf:tx_myttaddressmap_domain_model_ajaxsearch
						description = LLL:EXT:myttaddressmap/Resources/Private/Language/locallang_db.xlf:tx_myttaddressmap_domain_model_ajaxsearch.description
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
	
	
	
		
    }
);

// for use with extension extender 
//$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_address']['extender'][\FriendsOfTYPO3\TtAddress\Domain\Model\Address::class]['ext_ttaddress']
//    = 'EXT:myleaflet/Classes/Domain/Model/Address.php';

	