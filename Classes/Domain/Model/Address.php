<?php
namespace WSR\Myleaflet\Domain\Model;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2018-2019 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
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
 * Address
 */
class Address extends \FriendsOfTYPO3\TtAddress\Domain\Model\Address 
{


	/**
	 * leafletmapicon
	 * 
	 * @var string
	 */
	protected $leafletmapicon = '';

	/**
	 * mapgeocode
	 * 
	 * @var string
	 */
	protected $mapgeocode = '';

	/**
	 * Returns the leafletmapicon
	 * 
	 * @return string $this->leafletmapicon
	 */
	public function getLeafletmapicon() {
		return $this->leafletmapicon;
	}

	/**
	 * Sets the leafletmapicon
	 * 
	 * @param string $leafletmapicon
	 * @return void
	 */
	public function setLeafletmapicon($leafletmapicon) {
		$this->leafletmapicon = $leafletmapicon;
	}


	/**
	 * Returns mapgeocode
	 * 
	 * @return string $this->mapgeocode
	 */
	public function getmapgeocode() {
		return $this->mapgeocode;
	}

	/**
	 * Sets mapgeocode
	 * 
	 * @param string $mapgeocode
	 * @return void
	 */
	public function setMapgeocode($mapgeocode) {
		$this->mapgeocode = $mapgeocode;
	}


    /**
     * Get first category
     *
     * @return Category
     */
    public function getFirstCategory()
    {
        $categories = $this->getCategories();
        if (!is_null($categories)) {
            $categories->rewind();
            return $categories->current();
        } else {
            return null;
        }
    }


}
