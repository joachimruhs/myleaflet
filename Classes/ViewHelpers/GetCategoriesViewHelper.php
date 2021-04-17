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


class GetCategoriesViewHelper extends AbstractViewHelper {
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