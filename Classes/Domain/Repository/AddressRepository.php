<?php
namespace WSR\Myleaflet\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) Joachim Ruhs 2018
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
		$pi = M_PI;
		$lat = (float) $latLon->lat;
		$lon =  (float) $latLon->lon;
		$query = $this->createQuery();

		$limit = intval($page * $limit) . ',' . intval($limit);

        if ((!empty($categories))) {
			$query = $this->createQuery();

			// sanitizing
            $categories = \TYPO3\CMS\Core\Utility\GeneralUtility::intExplode(',', $categories, TRUE);

			// categoryAndSelect
			if ($categoryMode == 'AND')
				$categorySelectMode = ' AND ';
			else $categorySelectMode = ' OR ';

			for ($i = 0; $i < count($categories); $i++) {
				if ($categories[$i]) {
					if ($i == 0) {
						$categoryAndSelect = " AND (($categories[$i] in (select uid_local from sys_category_record_mm c
							where c.uid_foreign = a.uid AND tablenames='tt_address' AND fieldname='categories'))";

					} else {
						$categoryAndSelect .= " $categorySelectMode ($categories[$i] in (select uid_local from sys_category_record_mm c
							where c.uid_foreign = a.uid AND tablenames='tt_address' AND fieldname='categories'))";
						
					}
				}
			}
			if ($categoryAndSelect) $categoryAndSelect .= ')';

			$result = $query->statement("SELECT distinct a.*, (((acos(sin(($lat*$pi/180)) * sin((latitude*$pi/180)) + cos(($lat*$pi/180)) *
					cos((latitude*$pi/180)) * cos((($lon - longitude)*$pi/180)))))*6370) as distance,

					(SELECT GROUP_CONCAT(e.title ORDER BY e.title SEPARATOR ', ') from tt_address d, sys_category 
						e, sys_category_record_mm f
						where f.uid_local = e.uid AND 
						f.uid_foreign= d.uid
						AND f.uid_foreign = d.uid 
						and d.uid = a.uid
						and e.pid = " . intval($storagePid) ."
					) as categories
									
			FROM tt_address a
				WHERE a.latitude != 0 AND a.longitude != 0 AND a.hidden = 0 AND a.deleted = 0 AND a.pid in (" . $storagePid . ")
				
				$categoryAndSelect

				having distance <=" . intval($radius) . " order by " . $orderBy . " limit " . $limit )->execute(TRUE);

		} else {
			$result = $query->statement("SELECT distinct a.*, (((acos(sin(($lat*$pi/180)) * sin((latitude*$pi/180)) + cos(($lat*$pi/180)) *
					cos((latitude*$pi/180)) * cos((($lon - longitude)*$pi/180)))))*6370) as distance,
					(SELECT GROUP_CONCAT(e.title ORDER BY e.title SEPARATOR ', ') from tt_address d, sys_category 
						e, sys_category_record_mm f
						where f.uid_local = e.uid AND 
						f.uid_foreign= d.uid
						AND f.uid_foreign = d.uid 
						and d.uid = a.uid
						and e.pid = " . intval($storagePid) ."
					) as categories
	
			FROM tt_address a  
				WHERE a.latitude != 0 AND a.longitude != 0 AND a.hidden = 0 AND a.deleted = 0 AND a.pid in (" . $storagePid . ")
                having distance <=" . intval($radius) . " order by " . $orderBy . " limit " . $limit )->execute(TRUE);
		
		}		
		return $result;
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
