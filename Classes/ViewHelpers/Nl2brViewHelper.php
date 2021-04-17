<?php
namespace WSR\Myleaflet\ViewHelpers;

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


class Nl2brViewHelper extends AbstractViewHelper {
	
	public function initializeArguments() {
		$this->registerArgument('text', 'string', 'text for nl2br', true, 0);
		$this->registerArgument('htmlSpecialChars', 'integer', 'flag for htmlspecialchars', true, 0);
	}

	/**
	 * Return string with nl2br
	 *
	 * @return string 
	 */
	public function render() {
		if ($this->arguments['htmlSpecialChars']) {
			return str_replace(array("\r\n", "\r", "\n"), '<br />', htmlspecialchars($this->arguments['text'], ENT_QUOTES));
		} else {
			return str_replace(array("\r\n", "\r", "\n"), '<br />', $this->arguments['text']);
		}
	}	 




}
?>