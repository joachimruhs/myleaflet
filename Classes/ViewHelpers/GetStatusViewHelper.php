<?php
namespace WSR\Myleaflet\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
//use TYPO3\CMS\Core\Database\ConnectionPool;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
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
 * for tests, not used actually
 *
 */


class GetStatusViewHelper extends AbstractViewHelper {

	public function initializeArguments()
	{
		$this->registerArgument('addressUid', 'integer', 'The uid of the address', true);
	}

	public static function renderStatic(
		array $arguments,
       \Closure $renderChildrenClosure,
       RenderingContextInterface $renderingContext
	) {
//		$ttaddressRepository = GeneralUtility::makeInstance(\FriendsOfTYPO3\TtAddress\Domain\Repository\AddressRepository::class);
		$ttaddressRepository = GeneralUtility::makeInstance(\WSR\Myleaflet\Domain\Repository\AddressRepository::class);

		$name = $ttaddressRepository->findByUid((int)$arguments['addressUid'])->getLeafletmapicon();	 
		return 'World' . ' ' . $name;
	}


}