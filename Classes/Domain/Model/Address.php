<?php
namespace WSR\Myleaflet\Domain\Model;

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
