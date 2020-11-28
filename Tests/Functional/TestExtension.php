<?php

/**
 * This file is part of the "myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information (MIT License), please read the
 * LICENSE.txt file that was distributed with this source code.
 */



/* J. Ruhs
 * open a cmd console as Administrator
 * call with vendor\bin\phpunit -c vendor\nimut\testing-framework\res\Configuration\FunctionalTests.xml Public\typo3conf\ext\myleaflet\Tests\Functional\TestExtension
 *
 */
class TestExtension extends \Nimut\TestingFramework\TestCase\FunctionalTestCase {

	/**
	 * @var array 
	 */
	protected $testExtensionToLoad = array(
		'typo3conf/ext/myleaflet'
	);


	/**
	 * @var array
	 */
	protected $coreExtensionToLoad = array(
		'workspaces'
	);


	/**
	 * @var array
	 */
	protected $configurationToUseInTestInstance = array (
		'BE' => array (
				'debug' => TRUE,
				),
		'FE' => array(
				'debug' => TRUE,
				),
	);

		
	protected function setUp() {
		parent::setUp();
		// if BE User is needed and logged in
		$this->setUpBackenduserFromFixture(1);
		
		// import your data
		$fixturePath = ORIGINAL_ROOT . 'typo3conf/ext/myleaflet/Tests/Functional/Fixtures';
//		$this->importDataSet($fixturePath . 'pages.xml');
		

$this->importDataSet('ntf://Database/pages.xml');		
$this->importDataSet('ntf://Database/tt_content.xml');
$this->setUpFrontendRootPage(1, array('ntf://TypoScript/JsonRenderer.ts'));

		
		// setUp the frontend
		$this->setUpFrontendRootPage(
			1, // root page
			array ( // array of typoscript files
				   'typo3/sysext/core/Tests/Functional/Fixtures/Frontend/JsonRenderer.ts'
				  )
			);
	}

	



	/**
	 * @test
	 */
	public function TestContentIsShown() {
$response = $this->getFrontendResponse(1);
/*
		$response = $thisGetFrontendResponse(
			1, // page
			0, // language id
			0, // backend user id
			0, // workspace id
			TRUE, // fail on failure
			0 // frontend user id
			);
*/

		$responseContent = json_decode($response->getContent(), TRUE);
		$this->assertEquals($expectedRecords, $responseContent['Default']['records']);
//		$this->assertInRecords($expectedRecords[0], $responseContent['Default']['records']);

/*
// First import some page records
$this->importDataSet('ntf://Database/pages.xml');

// Import tt_content record that should be shown on your home page
$this->importDataSet('ntf://Database/tt_content.xml');

// Setup the page with uid 1 and include the TypoScript as sys_template record
$this->setUpFrontendRootPage(1, array('ntf://TypoScript/JsonRenderer.ts'));

// Fetch the frontend response
$response = $this->getFrontendResponse(1);

// Assert no error has occured
$this->assertSame('success', $response->getStatus());

// Get the first section from the response
$sections = $response->getResponseSections();
$defaultSection = array_shift($sections);

// Get the section structure
$structure = $defaultSection->getStructure();

// Make assertions for the structure
$this->assertTrue(is_array($structure['pages:1']['__contents']['tt_content:1']));
*/


	}
	

		
}