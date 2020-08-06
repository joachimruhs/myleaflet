
plugin.tx_myleaflet {
  view {
    # cat=plugin.tx_myleaflet/file; type=string; label=Path to template root (FE)
    templateRootPath = EXT:myleaflet/Resources/Private/Templates/
    # cat=plugin.tx_myleaflet/file; type=string; label=Path to template partials (FE)
    partialRootPath = EXT:myleaflet/Resources/Private/Partials/
    # cat=plugin.tx_myleaflet/file; type=string; label=Path to template layouts (FE)
    layoutRootPath = EXT:myleaflet/Resources/Private/Layouts/
    
		# customsubcategory=javascript=Javascript
		# cat=plugin.tx_myleaflet/javascript; type=boolean; label=Include jQuery core: Add jQuery core script. Turn it off (0), if jQuery is already added.
		includejQueryCore = 0

		# customsubcategory=css=CSS
		# cat=plugin.tx_myleaflet/css; type=string; label=CSS file
		cssFile = EXT:myleaflet/Resources/Public/CSS/myleaflet.css

		# cat=plugin.tx_myleaflet/javascript; type=string; label=jQuery library
		jQueryFile = EXT:myleaflet/Resources/Public/JavaScript/jquery-3.3.1.min.js

		# cat=plugin.tx_myleaflet/javascript; type=string; label=Javascript file
		javascriptFile = EXT:myleaflet/Resources/Public/JavaScript/myleaflet.js
    
  }
  persistence {
    # cat=plugin.tx_myleaflet//a; type=string; label=Default storage PID
    storagePid =
  }

	settings {
		# customsubcategory=leafletmaps=maps
		# cat=plugin.tx_myleaflet/leafletmaps; type=int; label=Result page ID: Result page ID
		resultPageId = 

		# cat=plugin.tx_myleaflet/leafletmaps; type=int; label=Details page ID: Details page ID
		detailsPageId = 

		# cat=plugin.tx_myleaflet/leafletmaps; type=int; label=Single view uid: Uid (not the pageId) for the singleView plugin
		singleViewUid = 1

		# cat=plugin.tx_myleaflet/leafletmaps; type=int; label=Result limit: Limit of results
		resultLimit = 300

		# cat=plugin.tx_myleaflet/leafletmaps; type=string; label=Initial map coordinates: Initial map coordinates
		initialMapCoordinates = 48,8

		# cat=plugin.tx_myleaflet/leafletmaps; type=string; label=Category select mode: [AND|OR] Default OR
		categorySelectMode = OR

		# cat=plugin.tx_myleaflet/leafletmaps; type=boolean; label=Marker clusterer: Enables the marker clusterer
		enableMarkerClusterer = 0

		# cat=plugin.tx_myleaflet/leafletmaps; type=string; label=Default languageUid: Use 0 in multi language sites to override selected language in Frontend and if tt_adress record are not localized. Leave it blank to use TYPO3 localization.
		defaultLanguageUid = 
	}




}
