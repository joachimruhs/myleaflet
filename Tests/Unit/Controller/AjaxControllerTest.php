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
 * vendor\bin\phpunit -c vendor\nimut\testing-framework\res\Configuration\UnitTests.xml .\Public\typo3conf\ext\myleaflet\Tests\Unit\Controller\AddressControllerTest
 *
 */


/**
 * Test case.
 *
 */
class AjaxControllerTest extends \Nimut\TestingFramework\TestCase\UnitTestCase  //\TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @var WSR\Myleaflet\Controller\AjaxController
     */
    protected $subject = null;


    protected function setUp()
    {
        parent::setUp();

        $this->subject = $this->getMockBuilder(\WSR\Myleaflet\Controller\AddressController::class)
            ->setMethods(['ajaxEidGeocodeAction', 'redirect', 'forward', 'addFlashMessage'])
            ->disableOriginalConstructor()
            ->getMock();
   }

    protected function tearDown()
    {
        parent::tearDown();
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
		$latLon = $this->ajaxEidGeocodeAction($address, $country);
		$this->assertEquals('OK', $latLon->status);
		echo PHP_EOL . 'address=' . $address . ' lat=' . $latLon->lat . ' latLon->status = ' . $latLon->status . PHP_EOL;
	}


    /**
     * function to fetch coordinates and status from nomatim
     * 
     * @var string $address
     * @var string $country
     * @return \stdClass
     */
    public function ajaxEidGeocodeAction($address, $country)
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
     * 
	 * example stub method overriding
     */
    public function overriding()
    {
		$this->subject->expects($this->once())
		    ->method('ajaxEidGeocodeAction')
		    ->will($this->returnValue('RETURN VALUE HERE!'));	
		$result = $this->subject->ajaxEidGeocodeAction();
		$this->assertEquals($result, 'RETURN VALUE HERE!');
	}

}
