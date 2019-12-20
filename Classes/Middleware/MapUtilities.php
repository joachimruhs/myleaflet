<?php

namespace WSR\Myleaflet\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;


class MapUtilities implements MiddlewareInterface {
		
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

		/** @var NormalizedParams $normalizedParams */
		$normalizedParams = $request->getAttribute('normalizedParams');
		$typo3SiteUrl = $normalizedParams->getSiteUrl(); // Same as GeneralUtility::getIndpEnv('TYPO3_SITE_URL')

		$requestArguments = $request->getParsedBody()['tx_myleaflet_ajax'];

		// Remove any output produced until now
		ob_clean();

		// continue only if action is ajaxPsr of extension myleaflet
		if ($requestArguments['action'] != 'ajaxPsr') return $handler->handle($request);

		//print_r ($normalizedParams);
//		print_r($requestArguments);
//		print_r($GLOBALS['TSFE']);

//echo 'xxx';
//print_r($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_myleaflet.']);

//echo 'Test:' .($GLOBALS['TSFE'] instanceof TypoScriptFrontendController);
//exit;

		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');		
		$ajaxController = $objectManager->get('WSR\Myleaflet\Controller\AjaxController');


//print_r($frontend->tmpl->setup['plugin.']['tx_myrental.']);

//exit;
		$ajaxController->indexAction($request);
		// when this exit is missing an infinite loop will result

		exit;
//$out .= '5555';
	
//		$response = GeneralUtility::makeInstance(Response::class);
//		$response->getBody()->write($out);
	
		// the following code never reached!
        //$response = $handler->handle($request);
 
		// Set caching header for cache servers (like NGINX) in seconds
//        $response = $response->withHeader('X-Accel-Expires', '60');
//		$response = $response->withHeader('Content-Type', 'application/json; charset=utf-8');
 
//		$response->getBody()->write('I\'m content fetched via AJAX.' . json_encode($requestArguments));
//        return $response;

    }


}
