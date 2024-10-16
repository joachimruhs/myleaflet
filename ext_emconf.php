<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "myleaflet".
 *
 * Auto generated 07-07-2024 18:51
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'MyLeaflet',
  'description' => 'Leaflet / OpenStreetMap for tt_address data with radial search and categories. No registration and no API-keys necessary.',
  'category' => 'plugin',
  'version' => '2.0.0',
  'state' => 'beta',
  'uploadfolder' => false,
  'clearcacheonload' => false,
  'author' => 'Joachim Ruhs',
  'author_email' => 'postmaster@joachim-ruhs.de',
  'author_company' => NULL,
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '12.4.0-13.4.99',
      'tt_address' => '9.0.0-9.1.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
);

