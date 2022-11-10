<?php
namespace WSR\Myleaflet\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***
 *
 * This file is part of the "Myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *
 ***/


/**
 * The repository for Categories
 */
class CategoryRepository {

protected $defaultOrderings = array('sorting' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING);
      
  /**
    * Finds all categories
    * @param int $storagePid
	* @return array of categories
	*/
    public function findAllOverride($storagePid, $sys_language_uid) {
		$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
			->getQueryBuilderForTable('sys_category');

		$queryBuilder
		->getRestrictions()
		->removeAll();

		$queryBuilder->select('*')
		->from('sys_category')
		->where(
			$queryBuilder->expr()->eq(
				'pid',
				$queryBuilder->createNamedParameter($storagePid, \PDO::PARAM_INT)
			)
		)			
		->andWhere($queryBuilder->expr()->andX(
				$queryBuilder->expr()->andX(
					$queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($sys_language_uid, \PDO::PARAM_INT))
				),
				$queryBuilder->expr()->andX(
					$queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
				),
				$queryBuilder->expr()->andX(
					$queryBuilder->expr()->gte('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
				)
			)
		)
        ->orderBy('sorting');
		$result = $queryBuilder->execute()->fetchAll();
        return $result;		
    }


  /**
    * Finds all categories
    * @param int $storagePid
	* @return array of categories
	*/
    public function findAll($storagePid) {
		$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
			->getQueryBuilderForTable('sys_category');

		$queryBuilder
		->getRestrictions()
		->removeAll();

		$queryBuilder->select('*')
		->from('sys_category')
		->where(
			$queryBuilder->expr()->eq(
				'pid',
				$queryBuilder->createNamedParameter($storagePid, \PDO::PARAM_INT)
			)
		)			
		->andWhere($queryBuilder->expr()->andX(
				$queryBuilder->expr()->andX(
					$queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
				),
				$queryBuilder->expr()->andX(
					$queryBuilder->expr()->gte('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
				)
			)
		);
		$result = $queryBuilder->execute()->fetchAll();
		return $result;		
    }

    public function getCategoryList($categoryList, $storagePid) {
        $categories = explode(',', $categoryList);
        $list = '';

        for ($i = 0; $i < count($categories); $i++) {
            $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
                ->getQueryBuilderForTable('sys_category');
    
            $queryBuilder
            ->getRestrictions()
            ->removeAll();
    
            $queryBuilder->select('*')
            ->from('sys_category')
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($storagePid, \PDO::PARAM_INT)
                )
            )			
            ->andWhere($queryBuilder->expr()->andX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($categories[$i], \PDO::PARAM_INT))
                    )
/*
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('hidden', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->gte('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT))
                    )
*/  
                )
            );
            $result = $queryBuilder->execute()->fetchAll();
            if ($result) {
                if ($result[0]['l10n_parent'] > 0) {
                    $list .= $result[0]['l10n_parent'] .',';
                } else {
                    $list .= $categories[$i] . ',';
                }
            }
        }
        return $list;

    }




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
