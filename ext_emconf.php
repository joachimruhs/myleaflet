<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "myleaflet".
 *
 * Auto generated 27-09-2025 12:48
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
  'title' => 'MyLeaflet',
  'description' => 'Leaflet / OpenStreetMap for tt_address data with radial search and categories. No registration and no API-keys necessary.',
  'category' => 'plugin',
  'version' => '2.3.6',
  'state' => 'beta',
  'uploadfolder' => false,
  'clearcacheonload' => false,
  'author' => 'Joachim Ruhs',
  'author_email' => 'postmaster@joachim-ruhs.de',
  'author_company' => '',
  'constraints' => 
  array (
    'depends' => 
    array (
      'typo3' => '13.4.0-14.3.99',
      'tt_address' => '9.0.0-10.1.99',
    ),
    'conflicts' => 
    array (
    ),
    'suggests' => 
    array (
    ),
  ),
);

