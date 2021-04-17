<?php
namespace WSR\Myleaflet\Domain\Repository;

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
 * The repository for Categories
 */
class CategoryRepository extends \TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository {

  protected $defaultOrderings = array('sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);
  
  public function findChildrenByParent($category = 0, $excludeCategories = array()) {
    $constraints = array();
    $query = $this->createQuery();
    $query->getQuerySettings()->setRespectStoragePage(false);
    
    $constraints[] = $query->equals('parent', $category);
    
    if (is_array($excludeCategories)) {
      if (count($excludeCategories) > 0) {
        $constraints[] = $query->logicalNot($query->in('uid', $excludeCategories));
      }
    }
    $query->matching($query->logicalAnd($constraints));
    
    return $query->execute();
  }
}
?>
