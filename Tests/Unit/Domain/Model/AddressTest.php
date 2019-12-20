<?php
namespace WSR\Myleaflet\Tests\Unit\Domain\Model;

/**
 * Test case.
 *
 * @author Joachim Ruhs <postmaster@joachim-ruhs.de>
 */
class AddressTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \WSR\Myleaflet\Domain\Model\Address
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \WSR\Myleaflet\Domain\Model\Address();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsName()
    {
        $this->subject->setName('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getLeafletmapiconReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getLeafletmapicon()
        );
    }

    /**
     * @test
     */
    public function setLeafletmapiconForStringSetsLeafletmapicon()
    {
        $this->subject->setLeafletmapicon('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'leafletmapicon',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMapgeocodeReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getMapgeocode()
        );
    }

    /**
     * @test
     */
    public function setMapgeocodeForStringSetsMapgeocode()
    {
        $this->subject->setMapgeocode('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'mapgeocode',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getCategoriesReturnsInitialValueForString()
    {
        self::assertSame(
            '',
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesForStringSetsCategories()
    {
        $this->subject->setCategories('Conceived at T3CON10');

        self::assertAttributeEquals(
            'Conceived at T3CON10',
            'categories',
            $this->subject
        );
    }
}
