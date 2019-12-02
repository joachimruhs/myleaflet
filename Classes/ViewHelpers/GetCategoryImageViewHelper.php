<?php
namespace WSR\Myleaflet\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

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
 */


class GetCategoryImageViewHelper extends AbstractViewHelper {
	protected $categoryRepository;
	
	public function initializeArguments() {
		$this->registerArgument('categoryUid', 'integer', 'The category', true, 0);
		$this->registerArgument('settings', 'array', 'The settings', true, null);
	}

	/**
	 * Return category images
	 *
	 * @return mixed 
	 * @api
	 */
	public function render() {
		$categoryUid = $this->arguments['categoryUid'];
		
		$fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
		$fileObjects = $fileRepository->findByRelation('sys_category', 'images', intval($categoryUid));		
		
		return $fileObjects;
 		
	}	 




}
?>