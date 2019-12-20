<?php
namespace WSR\Myleaflet\Domain\Model;


/***
 *
 * This file is part of the "MyLeaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Joachim Ruhs <postmaster@joachim-ruhs.de>
 *
 ***/
/**
 * Category
 */
class Category extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * name
     * 
     * @var
     */
    protected $name = null;

    /**
     * Returns the name
     * 
     * @return  $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     * 
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
