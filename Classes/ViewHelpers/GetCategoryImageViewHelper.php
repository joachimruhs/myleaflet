<?php
namespace WSR\Myleaflet\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;

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