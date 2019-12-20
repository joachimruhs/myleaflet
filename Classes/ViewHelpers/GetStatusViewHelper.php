<?php
namespace WSR\Myleaflet\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
//use TYPO3\CMS\Core\Database\ConnectionPool;

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;


/***************************************************************
 *  Copyright notice
 *
 *  (c) 2019 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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