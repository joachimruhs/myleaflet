<?php
namespace WSR\Myttaddressmap\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
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


class GetCategoriesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {
	protected $categoryRepository;
	
	public function initializeArguments() {
		$this->registerArgument('parentCategory', 'integer', 'The parent category', true, 0);
		$this->registerArgument('excludeCategories', 'string', 'Exclude categories', false);
		$this->registerArgument('as', 'string', 'Name of the template variable that will contain the categories', true);
	}

	/**
	 * Return child categories
	 *
	 * @return mixed 
	 * @api
	 */
	public function render() {
		$parent = $this->categoryRepository->findByUid($this->arguments['parentCategory']);
		$excludeCategories = ($this->arguments['excludeCategories'] ? explode(',', $this->arguments['excludeCategories']) : array());
		$children = $this->categoryRepository->findChildrenByParent($this->arguments['parentCategory'], $excludeCategories);
		$as = (string)$this->arguments['as'];
		$options = array(); // for dropdown select
		
		$options[0] = $parent->getTitle();
		
		foreach ($children as $child) {
			$options[$child->getUid()] = $child->getTitle();
		}
			
		$this->templateVariableContainer->add($as, array(
			'parent' => $parent,
			'children' => $children,
			'options' => $options
		));
		
		$output = $this->renderChildren();
		$this->templateVariableContainer->remove($as);
		
		return $output;
	}	 




}
?>