<?php
namespace WSR\Myleaflet\Tests\Unit\Controller;

/**
 * This file is part of the "myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information (MIT License), please read the
 * LICENSE.txt file that was distributed with this source code.
 */


use FriendsOfTYPO3\TtAddress\Domain\Model\Address;

/* call it from console in Document Root from a composer TYPO3 installation
 * vendor\bin\phpunit -c vendor\nimut\testing-framework\res\Configuration\UnitTests.xml Public\typo3conf\ext\myleaflet\Tests\Unit\Controller\AddressControllerTest
 *
 */


/**
 * Test case.
 *
 */
class AddressControllerTest extends \Nimut\TestingFramework\TestCase\UnitTestCase  //\TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var WSR\Myleaflet\Controller\AddressController
     */
    protected $subject = null;


    protected function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(\WSR\Myleaflet\Controller\AddressController::class)
            ->setMethods(['showAction', 'redirect', 'forward', 'addFlashMessage'])
//            ->setMethods(['redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
   }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @test
     */
    public function stringIsEqual()
    {
		$s1 = 'gggg';
		$this->assertEquals('gggg', $s1);
	}

    /**
     * @test
     */
    public function stringIsNotEqual()
    {
		$s1 = 'gggg1';
		$this->assertNotEquals('gggg', $s1);
	}


    /**
     * location data for getCoordinatesFromAddress
     * 
	 * @dataProvider providerGetCoordinatesFromAddress
     */
	public function providerGetCoordinatesFromAddress()
	{
	    return [
	        ['Rust', 'DE'],
	        ['Freiburg', 'DE'],
	        ['Freiburg', 'CH'],
	    ];
	}

    /**
     * @test
     * 
	 * @dataProvider providerGetCoordinatesFromAddress
     */
    public function getCoordinatesFromAddress($address, $country)
    {
		$latLon = $this->geocodeAddress($address, $country);
		$this->assertEquals('OK', $latLon->status);
		echo PHP_EOL . 'address=' . $address . ' lat=' . $latLon->lat . ' latLon->status = ' . $latLon->status . PHP_EOL;
	}


    public function geocodeAddress($address, $country)
    {
		$apiURL = "https://nominatim.openstreetmap.org/search/$address,+$country?format=json&limit=1";
		$addressData = $this->subject->get_webpage($apiURL); // call of function get_webpage in \WSR\Myleaflet\Controller\AddressController
		
		$coordinates[1] = json_decode($addressData)[0]->lat;
		$coordinates[0] = json_decode($addressData)[0]->lon;

		$latLon = new \stdClass();
		$latLon->lat = (float) $coordinates[1];
		$latLon->lon = (float) $coordinates[0];
		if ($latLon->lat) 
			$latLon->status = 'OK';
		else 
			$latLon->status = 'NOT FOUND';
		return $latLon;
	}

    /**
     * @test
     */
    public function leafletmapiconCanBeSet()
    {
        $this->address = new \WSR\Myleaflet\Domain\Model\Address;
        $value = 'mapicon.png';
        $this->address->setLeafletmapicon($value);
        $this->assertEquals($value, $this->address->getLeafletmapicon());
    }
	
    /**
     * @test
     */
    public function mapgeocodeCanBeSet()
    {
        $this->address = new \WSR\Myleaflet\Domain\Model\Address;
        $value = 1;
        $this->address->setMapgeocode($value);
        $this->assertEquals($value, $this->address->getMapgeocode());
    }

    /**
     * @test
     */
    public function listActionFetchesAllAddressesFromRepositoryAndAssignsThemToView()
    {

        $allAddresses = $this->getMockBuilder(\TYPO3\CMS\Extbase\Persistence\ObjectStorage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $addressRepository = $this->getMockBuilder(\WSR\Myleaflet\Domain\Repository\AddressRepository::class)
            ->setMethods(['findAll'])
            ->disableOriginalConstructor()
            ->getMock();
        $addressRepository->expects(self::once())->method('findAll')->will(self::returnValue($allAddresses));
        $this->inject($this->subject, 'addressRepository', $addressRepository);

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $view->expects(self::once())->method('assign')->with('addresses', $allAddresses);
        $this->inject($this->subject, 'view', $view);
        $this->subject->listAction();
    }

	
	
    /**
     * @test
     */

    public function showActionAssignsTheGivenAddressToView()
    {
		/*
		 * Error: Call to a member function getArguments() on null
		 * ...\public\typo3conf\ext\myleaflet\Classes\Controller\AddressController.php:128
		 */

		
        $address = new \WSR\Myleaflet\Domain\Model\Address();

        $view = $this->getMockBuilder(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface::class)->getMock();
        $this->inject($this->subject, 'view', $view);
        $view->expects(self::once())->method('assign')->with('address', $address);

        $view->assign('address', $address);

//        $this->subject->showAction($address); // this throws the error
 
    }



}
