<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "myleaflet".
 *
 * Auto generated 28-11-2019 15:00
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'MyLeaflet',
  'description' => 'Leaflet / OpenStreetMap for tt_address data with radial search and categories. No registration and no API-keys necessary.',
  'category' => 'plugin',
  'author' => 'Joachim Ruhs',
  'author_email' => 'postmaster@joachim-ruhs.de',
  'state' => 'beta',
  'uploadfolder' => true,
  'createDirs' => 'uploads/tx_myleaflet/icons',
  'clearCacheOnLoad' => 0,
  'version' => '0.5.0',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '9.5.0-9.5.99',
      'tt_address' => '4.0.0',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
  'clearcacheonload' => false,
  'author_company' => NULL,
);

