<?php
namespace WSR\Myttaddressmap\ViewHelpers;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/***
 *
 * This file is part of the "Myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 - 2021 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *
 ***/

/**
 *
 *
 * @package TYPO3
 * @subpackage myleaflet
 *
 */


class AddSlashesViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		$this->registerArgument('text', 'string', 'text for addslashes', true, 0);
	}

	/**
	 * Return string with added slashes
	 *
	 * @return string 
	 */
	public function render() {
		return addslashes($this->arguments['text']);
	}	 




}
?>