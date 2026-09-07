<?php

namespace WSR\Myleaflet\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Psr\Http\Message\ResponseFactoryInterface;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Service\TypoScriptService;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;

/***
 *
 * This file is part of the "Myleaflet" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2018 - 2022 Joachim Ruhs <postmaster@joachim-ruhs.de>, Web Services Ruhs
 *
 ***/


class MapUtilities implements MiddlewareInterface {
  
    /** @var ResponseFactoryInterface */

	private $responseFactory;

    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }


    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
        ): ResponseInterface {
            $parsedBody = $request->getParsedBody();
            
            if (!is_array($parsedBody)) {
                return $handler->handle($request);
            }
            
            $requestArguments = $parsedBody['tx_myleaflet_ajax'] ?? [];
            
            if (
                !is_array($requestArguments)
                || ($requestArguments['action'] ?? '') !== 'ajaxPsr'
                ) {
                    return $handler->handle($request);
                }
                
                $ajaxController = GeneralUtility::makeInstance(
                    \WSR\Myleaflet\Controller\AjaxController::class
                    );
                
                return $ajaxController->handleAjaxRequest($request);
    }

}
