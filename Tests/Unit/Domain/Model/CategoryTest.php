<?php
namespace WSR\Myleaflet\Tests\Unit\Domain\Model;

/**
 * This file is part of the "myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information (MIT License), please read the
 * LICENSE.txt file that was distributed with this source code.
 */


/**
 * Test case.
 *
 * @author Joachim Ruhs <postmaster@joachim-ruhs.de>
 */
class CategoryTest extends \Nimut\TestingFramework\TestCase\UnitTestCase// extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var \WSR\Myleaflet\Domain\Model\Category
     */
    protected $subject = null;

    protected function setUp()
    {
        parent::setUp();
        $this->subject = new \WSR\Myleaflet\Domain\Model\Category();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueFor()
    {
		$s1 = 'gggg';
		$this->assertEquals('gggg', $s1);

    }

    /**
     * @test
     */
    public function setNameForSetsName()
    {
		$s1 = 'gggg';
		$this->assertEquals('gggg', $s1);
    }
}
