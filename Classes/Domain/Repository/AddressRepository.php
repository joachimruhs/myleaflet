<?php
namespace WSR\Myleaflet\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Joachim Ruhs 2018-2019
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

 
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
 
 
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
	public function findLocationsInRadius($latLon, $radius, $categories, $storagePid, $limit, $page, $orderBy = 'distance', $categoryMode = true) {
		$radius = intval($radius);
		$lat = $latLon->lat;
		$lon =  $latLon->lon;
		$query = $this->createQuery();

		// categoryAndSelect
		if ($categoryMode == 'AND')
			$categorySelectMode = ' AND ';
		else $categorySelectMode = ' OR ';

		$queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
			->getQueryBuilderForTable('tx_myttaddressmap_domain_model_address');

		$queryBuilder->from('tt_address', 'a');

		$arrayOfPids = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $storagePid, TRUE);
		$storagePidList = implode(',', $arrayOfPids);
		
		$queryBuilder->selectLiteral(
			'distinct a.*', '(acos(sin(' . floatval($lat * M_PI / 180) . ') * sin(latitude * ' . floatval(M_PI / 180) . ') + cos(' . floatval($lat * M_PI / 180) . ') *
			cos(latitude * ' . floatval(M_PI / 180) . ') * cos((' . floatval($lon) . ' - longitude) * ' . floatval(M_PI / 180) . '))) * 6370 as `distance`,

			(SELECT GROUP_CONCAT(e.title ORDER BY e.title SEPARATOR \', \') from tt_address d, sys_category 
						e, sys_category_record_mm f
						where f.uid_local = e.uid
						AND f.uid_foreign= d.uid
						and d.uid = a.uid
						and e.pid in (' . $storagePidList  . ')
					) as categories			
			'
		)

		->where(
			$queryBuilder->expr()->in(
				'a.pid',
				$queryBuilder->createNamedParameter(
				$query->getQuerySettings()->getStoragePageIds(),
				\Doctrine\DBAL\Connection::PARAM_INT_ARRAY
				)
			)
		)		
		
		->orderBy('distance');

        $queryBuilder->having('`distance` <= ' . $queryBuilder->createNamedParameter($radius, \PDO::PARAM_INT));
		$queryBuilder = $this->addCategoryQueryPart($categories, $categoryMode, $queryBuilder);

		if ($categorySelectMode == ' OR ')
			$queryBuilder->setMaxResults(intval($limit))->setFirstResult(intval($page * $limit));

		$result =  $queryBuilder->execute()->fetchAll();
		
		$arrayOfCategories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $categories, TRUE);



		if ($categorySelectMode == ' AND ' /*&& count($arrayOfCategories) */) {
			// we have to check the location for $arrayOfCategories
			$j = 0;
			for ($i = 0; $i < count($result); $i++) {
				$checkOk = $this->testLocationCategories($result[$i]['uid'], $arrayOfCategories);
				if ($checkOk) $j++;
				//echo $result[$i]['name'] . ' ' . $checkOk . '$j= ' .$j . '<' . $page * $limit . '&&' . $j . '<=' . intval(($page + 1) * $limit) . '<br />';
				if (!($arrayOfCategories)) $checkOk = 1;

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
			$queryBuilder->expr()->andX(
				$queryBuilder->expr()->eq('a.uid_foreign', $queryBuilder->createNamedParameter($locationUid, \PDO::PARAM_INT)),
				$queryBuilder->expr()->eq('a.tablenames', $queryBuilder->createNamedParameter('tt_address')),
				$queryBuilder->expr()->eq('a.fieldname', $queryBuilder->createNamedParameter('categories')),
				$queryBuilder->expr()->in('a.uid_local', $queryBuilder->createNamedParameter($arrayOfCategories, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY))
			)
		);
		
		
		$result = $queryBuilder->execute()->fetchAll();
		
		for ($i = 0; $i < count($result); $i++) {
			for ($j = 0; $j < count($arrayOfCategories); $j++) {
				if ($arrayOfCategories[$j] == $result[$i]['uid_local']) {
					$f++;
					$i++;
					continue;
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
                $expression->andX(
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
						$queryBuilder->createNamedParameter($arrayOfCategories, \Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
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
                $expression->andX(
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
					$queryBuilder->createNamedParameter($locationUid, \PDO::PARAM_INT)
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
