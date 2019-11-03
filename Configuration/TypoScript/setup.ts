
plugin.tx_myleaflet {
  view {
    templateRootPaths.0 = EXT:myleaflet/Resources/Private/Templates/
    templateRootPaths.1 = {$plugin.tx_myleaflet.view.templateRootPath}
    partialRootPaths.0 = EXT:myleaflet/Resources/Private/Partials/
    partialRootPaths.1 = {$plugin.tx_myleaflet.view.partialRootPath}
    layoutRootPaths.0 = EXT:myleaflet/Resources/Private/Layouts/
    layoutRootPaths.1 = {$plugin.tx_myleaflet.view.layoutRootPath}
  }
  persistence {
    storagePid = {$plugin.tx_myleaflet.persistence.storagePid}
    #recursive = 1
    
     classes{

            WSR\Myleaflet\Domain\Model\Address {
                mapping {
                    tableName = tt_address
					recordType >
                }
            }

			WSR\Myleaflet\Domain\Model\Category {
			  mapping {
				tableName = sys_category
				columns {
				}
			  }
			}

    	}
    
    
  }
  features {
    #skipDefaultArguments = 1
  }
  mvc {
    #callDefaultActionIfActionCantBeResolved = 1
  }


	settings {
		defaultIcon = {$plugin.tx_myleaflet.settings.defaultIcon}

		resultPageId = {$plugin.tx_myleaflet.settings.resultPageId}
		detailsPageId = {$plugin.tx_myleaflet.settings.detailsPageId}
		singleViewUid = {$plugin.tx_myleaflet.settings.singleViewUid}

		resultLimit = {$plugin.tx_myleaflet.settings.resultLimit}
		initialMapCoordinates = {$plugin.tx_myleaflet.settings.initialMapCoordinates}

		categorySelectMode = {$plugin.tx_myleaflet.settings.categorySelectMode}
	}





}

plugin.tx_myleaflet._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    .tx-myleaflet table {
        border-collapse:separate;
        border-spacing:10px;
    }

    .tx-myleaflet table th {
        font-weight:bold;
    }

    .tx-myleaflet table td {
        vertical-align:top;
    }

    .typo3-messages .message-error {
        color:red;
    }

    .typo3-messages .message-ok {
        color:green;
    }
)

page.includeCSS.tx_myleaflet = {$plugin.tx_myleaflet.view.cssFile}


page.includeJS {
  myleaflet10.forceOnTop = 1
  myleaflet10.if.isTrue = {$plugin.tx_myleaflet.view.includejQueryCore}
  myleaflet10 = {$plugin.tx_myleaflet.view.jQueryFile}
  myleaflet10.insertData = 1
}

page.includeJSFooterlibs.myleaflet_js1 = {$plugin.tx_myleaflet.view.javascriptFile}

plugin.tx_myleaflet.features.requireCHashArgumentForActionArguments = 0

