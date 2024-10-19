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

 
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Core\Database\Connection;
 
//use TYPO3\CMS\Extbase\Persistence\Repository;


/**
 * The repository for Addresses
 */
class AddressRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{


	/**
	 * Find locations within radius and categories
	 *
	 * @param stdClass  $latLon
	 * @param int  $radius
	 * @param array $categories
	 * @param int  $limit
	 * @param int  $page
	 * @param string  $orderBy
	 * @param bool $categoryMode 
	 * 
	 * @return QueryResultInterface|array of the locations
	 */
	public function findLocationsInRadius($latLon, $radius, $categories, $storagePid, $language, $limit, $page, $orderBy = 'distance', $categoryMode = true) {
		$radius = intval($radius);
		$lat = $latLon->lat;
		$lon =  $latLon->lon;
//		$query = $this->createQuery();
//		$query->getQuerySettings()->setRespectStoragePage(true);
		
		// categoryAndSelect
		if ($categoryMode == 'AND')
			$categorySelectMode = ' AND ';
		else $categorySelectMode = ' OR ';

		$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
			->getQueryBuilderForTable('tx_myleaflet_domain_model_address');

		$queryBuilder->from('tt_address', 'a');

		$arrayOfPids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $storagePid, TRUE);
		$storagePidList = implode(',', $arrayOfPids);
		
		if ($language) {
		$queryBuilder->selectLiteral(
			'distinct a.*', '(acos(sin(' . floatval($lat * M_PI / 180) . ') * sin(latitude * ' . floatval(M_PI / 180) . ') + cos(' . floatval($lat * M_PI / 180) . ') *
			cos(latitude * ' . floatval(M_PI / 180) . ') * cos((' . floatval($lon) . ' - longitude) * ' . floatval(M_PI / 180) . '))) * 6370 as `distance`,

			(SELECT GROUP_CONCAT(e.title ORDER BY e.title SEPARATOR \', \') from tt_address d, sys_category 
						e , sys_category_record_mm m
						where  m.uid_foreign = d.uid
						and e.sys_language_uid = ' . intval($language) . '
						and e.l10n_parent = m.uid_local
						and d.uid = a.uid
						and e.pid in (' . $storagePidList  . ')
					) as categories			
			');
		} else {
		$queryBuilder->selectLiteral(
			'distinct a.*', '(acos(sin(' . floatval($lat * M_PI / 180) . ') * sin(latitude * ' . floatval(M_PI / 180) . ') + cos(' . floatval($lat * M_PI / 180) . ') *
			cos(latitude * ' . floatval(M_PI / 180) . ') * cos((' . floatval($lon) . ' - longitude) * ' . floatval(M_PI / 180) . '))) * 6370 as `distance`,

			(SELECT GROUP_CONCAT(e.title ORDER BY e.title SEPARATOR \', \') from tt_address d, sys_category 
						e , sys_category_record_mm m
						where m.uid_local = e.uid
						and m.uid_foreign = d.uid
						and e.sys_language_uid = 0
						and d.uid = a.uid
						and e.pid in (' . $storagePidList  . ')
					) as categories			
			');
			
		}			

		$queryBuilder->where(
			$queryBuilder->expr()->in(
				'a.pid',
				$queryBuilder->createNamedParameter(
				$arrayOfPids,
//				$query->getQuerySettings()->getStoragePageIds(),
				Connection::PARAM_INT_ARRAY
				)
			)
		)		
		->andWhere(
			$queryBuilder->expr()->eq('a.sys_language_uid',	$queryBuilder->createNamedParameter($language, Connection::PARAM_INT))
		)
		
		->orderBy('distance');
		
        $queryBuilder->having('`distance` <= ' . $queryBuilder->createNamedParameter($radius, Connection::PARAM_INT));
		$queryBuilder = $this->addCategoryQueryPart($categories, $categoryMode, $queryBuilder);

		if ($categorySelectMode == ' OR ')
			$queryBuilder->setMaxResults(intval($limit))->setFirstResult(intval($page * $limit));

		$result =  $queryBuilder->executeQuery()->fetchAllAssociative();
		
		$arrayOfCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $categories, TRUE);

//print_r($queryBuilder->getSql());
//print_r($queryBuilder->getParameters());
//print_r($result);


		if ($categorySelectMode == ' AND ' /*&& count($arrayOfCategories) */) {
			// we have to check the location for $arrayOfCategories
			$j = 0;
			for ($i = 0; $i < count($result); $i++) {
				$checkOk = $this->testLocationCategories($result[$i]['uid'], $arrayOfCategories);
				if ($checkOk) $j++;
//				echo $result[$i]['name'] . ' CheckOk = ' . $checkOk . ' $j= ' .$j . '<' . $page * $limit . '&&' . $j . '<=' . intval(($page + 1) * $limit) . '<br />';
				if (!($arrayOfCategories)) $checkOk = 1;

                $newResult = [];
				if ($checkOk && ($j >  $page * $limit && $j <= intval(($page + 1) * $limit))) {
					$newResult[] = $result[$i];
				}
			}

			return $newResult;
		}
		return $result;
	}

	/*
	 * 	check if location belongs to all categories of $arrayOfCategories
	 * 	
	 * @param int $locationUid
	 * @param array $arrayOfCategories
	 *
	 * @return bool $allCategoriesFound
	 */
    protected function testLocationCategories($locationUid, $arrayOfCategories)
    {
		$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
			->getQueryBuilderForTable('sys_category_record_mm');

		$queryBuilder->from('sys_category_record_mm', 'a');
		$queryBuilder->select('*');
		$queryBuilder->where(
			$queryBuilder->expr()->and(
				$queryBuilder->expr()->eq('a.uid_foreign', $queryBuilder->createNamedParameter($locationUid, Connection::PARAM_INT)),
				$queryBuilder->expr()->eq('a.tablenames', $queryBuilder->createNamedParameter('tt_address')),
				$queryBuilder->expr()->eq('a.fieldname', $queryBuilder->createNamedParameter('categories')),
				$queryBuilder->expr()->in('a.uid_local', $queryBuilder->createNamedParameter($arrayOfCategories, Connection::PARAM_INT_ARRAY))
			)
		);
		$queryBuilder->orderBy('a.uid_foreign', 'asc');
		$result = $queryBuilder->executeQuery()->fetchAllAssociative();

        $f = 0;
		for ($i = 0; $i < count($result); $i++) {
			for ($j = 0; $j < count($arrayOfCategories); $j++) {
				if ($arrayOfCategories[$j] == $result[$i]['uid_local']) {
					$f++;
//					$i++; have to be commented in old versions 
//					continue;
				}
			}		
		}
		$allCategoriesFound = (count($arrayOfCategories) == $f)? 1 : 0;
		return $allCategoriesFound; // if $f == count($arrayOfCategories) all categories are found for this location  
	}
	

	/*
	 * 	adopted from EXT storefinder
	 * 	
	 * @param string $categories
	 * @param QueryBuilder $queryBuilder
	 *
	 * @return QueryBuilder
	 */
    protected function addCategoryQueryPart($categoryList, $categoryMode, QueryBuilder $queryBuilder): QueryBuilder
    {
		$arrayOfCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $categoryList, TRUE);

        if (!empty($arrayOfCategories)) {
			$expression = $queryBuilder->expr();
			$queryBuilder->innerJoin(
				'a',
				'sys_category_record_mm',
				'c',
                $expression->and(
                    $expression->eq('a.uid', $queryBuilder->quoteIdentifier('c.uid_foreign')),
                    $expression->eq(
						'c.tablenames',
						$queryBuilder->createNamedParameter('tt_address')
                    ),
					$expression->eq(
						'c.fieldname',
						$queryBuilder->createNamedParameter('categories')
					)
					
                )
            );
				$queryBuilder->andWhere(
					$expression->in(
						'c.uid_local',
						$queryBuilder->createNamedParameter($arrayOfCategories, Connection::PARAM_INT_ARRAY)
					)
				);
		}
		return $queryBuilder;
	}


	/**
	 * Get the first categoryImage of a location
	 *
	 * @param int  $locationUid
	 * @param int $storagePid
	 * 
	 * @return mixed $image
	 */
	public function getFirstCategoryImage($locationUid, $storagePid) {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_category');
        $query = $queryBuilder
            ->select('uid')
            ->from('sys_category', 'c');

//		$arrayOfUids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $locationUid, TRUE);

        if (is_int($locationUid)) {
			$expression = $queryBuilder->expr();
			$queryBuilder->innerJoin(
				'c',
				'sys_category_record_mm',
				'm',
                $expression->and(
                    $expression->eq('c.uid', 'm.uid_local'),
  
                    $expression->eq(
						'm.tablenames',
						$queryBuilder->createNamedParameter('tt_address')
                    ),
					$expression->eq(
						'm.fieldname',
						$queryBuilder->createNamedParameter('categories')
					)
/*
					$expression->eq(
						'm.uid_foreign',
						$queryBuilder->createNamedParameter($locationUid, \PDO::PARAM_INT)
					)
*/					
                )
            );

			$queryBuilder->andWhere(
				$expression->eq(
					'm.uid_foreign',
					$queryBuilder->createNamedParameter($locationUid, Connection::PARAM_INT)
				)
			);

/*
			$queryBuilder->andWhere(
				$expression->in(
					'm.uid_foreign',
					$queryBuilder->createNamedParameter($arrayOfUids, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
				)
			);
*/			
	
		}
		
		$result =  $queryBuilder->execute()->fetchAll();

		$fileRepository = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
		for ($i = 0; $i < count($result); $i++) {
			$categoryUid = $result[$i]['uid'];
			if (is_array($fileObjects))
				$fileObjects = array_merge($fileObjects, $fileRepository->findByRelation('sys_category', 'images', intval($categoryUid)));
			else $fileObjects = $fileRepository->findByRelation('sys_category', 'images', intval($categoryUid));
		}

		if ($fileObjects[0])
			return $fileObjects[0]->getOriginalFile()->getPublicUrl();
	}		

}
