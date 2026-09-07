<?php

declare(strict_types=1);

namespace WSR\Myleaflet\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use WSR\Myleaflet\Domain\Repository\AddressRepository;
use WSR\Myleaflet\Domain\Repository\CategoryRepository;

/**
 * AJAX handler for myleaflet.
 *
 * This is intentionally NOT an Extbase ActionController.
 * It is called directly by the PSR-15 middleware.
 */
final class AjaxController
{
    private array $configuration = [];

    private array $settings = [];

    private int $storagePid = 0;


    public function __construct(
        private readonly AddressRepository $addressRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ViewFactoryInterface $viewFactory,
        private readonly RequestFactory $requestFactory,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {
    }


    /**
     * Main entry point used by the middleware.
     */
    public function handleAjaxRequest(
        ServerRequestInterface $request
    ): ResponseInterface {
        if ($request->getMethod() !== 'POST') {
            return $this->createHtmlResponse(
                '',
                405,
                [
                    'Allow' => 'POST',
                ]
            );
        }

        $requestArguments = $this->getAjaxArguments($request);

        if ($requestArguments === []) {
            return $this->createHtmlResponse(
                '<div class="ajaxMessage">Invalid AJAX request.</div>',
                400
            );
        }

        $this->initializeConfiguration($request);

        $output = $this->ajaxEid(
            $request,
            $requestArguments
        );

        return $this->createHtmlResponse($output);
    }


    /**
     * Read AJAX arguments safely.
     */
    private function getAjaxArguments(
        ServerRequestInterface $request
    ): array {
        $parsedBody = $request->getParsedBody();

        if (!is_array($parsedBody)) {
            return [];
        }

        $arguments = $parsedBody['tx_myleaflet_ajax'] ?? [];

        return is_array($arguments)
            ? $arguments
            : [];
    }


    /**
     * Load TypoScript configuration from the PSR-7 request.
     *
     * No Extbase ConfigurationManager is required.
     */
    private function initializeConfiguration(
        ServerRequestInterface $request
    ): void {
        $frontendTypoScript =
            $request->getAttribute('frontend.typoscript');

        if ($frontendTypoScript === null) {
            $this->configuration = [];
            $this->settings = [];
            $this->storagePid = 0;

            return;
        }

        $setup = $frontendTypoScript->getSetupArray();

        $this->configuration =
            $setup['plugin.']['tx_myleaflet.'] ?? [];

        $this->settings =
            $this->configuration['settings.'] ?? [];

        $this->storagePid = (int)(
            $this->configuration['persistence.']['storagePid']
            ?? 0
        );
    }


    /**
     * Main AJAX processing.
     */
    private function ajaxEid(
        ServerRequestInterface $request,
        array $requestArguments
    ): string {
        /*
         * Requested content language.
         */
        $requestedLanguageUid =
            (int)($requestArguments['language'] ?? 0);

        $defaultLanguageUid =
            $this->settings['defaultLanguageUid'] ?? '';

        if ($defaultLanguageUid !== '') {
            $languageUid = (int)$defaultLanguageUid;
        } else {
            $languageUid = $requestedLanguageUid;
        }


        /*
         * Categories.
         */
        $categoryList = '';

        $categories =
            $requestArguments['categories'] ?? [];

        if (is_array($categories) && $categories !== []) {
            $categoryIds = [];

            foreach ($categories as $categoryUid) {
                $categoryUid = (int)$categoryUid;

                if ($categoryUid > 0) {
                    $categoryIds[] = $categoryUid;
                }
            }

            if ($categoryIds !== []) {
                $categoryList = implode(
                    ',',
                    $categoryIds
                );
            }
        }

        if ($categoryList !== '') {
            $categoryList =
                $this->categoryRepository->getCategoryList(
                    $categoryList,
                    $this->storagePid
                );
        }


        /*
         * Geocoding.
         */
        $latLon = $this->geocode(
            $request,
            $requestArguments
        );

        if ($latLon->status !== 'OK') {
            return
                '<div class="ajaxMessage">'
                . 'Geocoding Error: '
                . htmlspecialchars(
                    (string)$latLon->status,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                )
                . '</div>'
                . '<script type="text/javascript">'
                . 'const ajaxMessage = document.querySelector(".ajaxMessage");'
                . 'if (ajaxMessage) { ajaxMessage.style.display = "block"; }'
                . '</script>';
        }


        /*
         * Radius.
         */
        $radius =
            (float)($requestArguments['radius'] ?? 0);


        /*
         * Result limit.
         */
        $limit =
            (int)($this->settings['resultLimit'] ?? 100);

        if ($limit <= 0) {
            $limit = 100;
        }


        /*
         * Page.
         */
        $page =
            (int)($requestArguments['page'] ?? 0);

        if ($page === -1) {
            $limit = 1000;
            $page = 0;
        }


        /*
         * Sorting.
         */
        $address =
            trim((string)($requestArguments['address'] ?? ''));

        $orderBy =
            $address === ''
                ? 'city'
                : 'distance';


        /*
         * Category selection mode.
         */
        $categoryMode =
            $this->settings['categorySelectMode'] ?? '';


        /*
         * Find locations.
         */
        $locations =
            $this->addressRepository->findLocationsInRadius(
                $latLon,
                $radius,
                $categoryList,
                $this->storagePid,
                $languageUid,
                $limit,
                $page,
                $orderBy,
                $categoryMode
            );

        $allLocations =
            $this->addressRepository->findLocationsInRadius(
                $latLon,
                $radius,
                $categoryList,
                $this->storagePid,
                $languageUid,
                1000,
                0,
                $orderBy,
                $categoryMode
            );


        /*
         * Prepare result data.
         */
        if (is_array($locations)) {
            foreach ($locations as &$location) {
                $description =
                    (string)($location['description'] ?? '');

                $address =
                    (string)($location['address'] ?? '');

                /*
                 * Preserve former behaviour:
                 * infoWindowDescription may contain HTML.
                 */
                $location['infoWindowDescription'] =
                    str_replace(
                        ["\r\n", "\r", "\n"],
                        '<br />',
                        $description
                    );

                $location['description'] =
                    str_replace(
                        ["\r\n", "\r", "\n"],
                        '<br />',
                        htmlspecialchars(
                            $description,
                            ENT_QUOTES | ENT_SUBSTITUTE,
                            'UTF-8'
                        )
                    );

                $location['address'] =
                    str_replace(
                        ["\r\n", "\r", "\n"],
                        '<br />',
                        $address
                    );

                $location['infoWindowAddress'] =
                    str_replace(
                        ["\r\n", "\r", "\n"],
                        '<br />',
                        htmlspecialchars(
                            $address,
                            ENT_QUOTES | ENT_SUBSTITUTE,
                            'UTF-8'
                        )
                    );


                /*
                 * FAL images.
                 */
                if ((int)($location['image'] ?? 0) > 0) {
                    $addressObject =
                        $this->addressRepository->findByUid(
                            (int)($location['uid'] ?? 0)
                        );

                    if ($addressObject !== null) {
                        $location['images'] =
                            $addressObject->getImage();
                    }
                }
            }

            unset($location);
        }


        /*
         * No locations found.
         */
        if (
            !is_array($locations)
            || $locations === []
        ) {
            $message =
                $this->translate(
                    $request,
                    'noLocationsFound'
                );

            return
                '<div class="ajaxMessage">'
                . htmlspecialchars(
                    $message,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                )
                . '</div>'
                . '<script type="text/javascript">

                    if (typeof marker !== "undefined") {
                        for (var i = 0; i < marker.length; i++) {
                            if (marker[i]) {
                                map.removeLayer(marker[i]);
                            }
                        }
                    }

                    marker = [];

                    if (
                        typeof markerClusterGroup !== "undefined"
                        && map.hasLayer(markerClusterGroup)
                    ) {
                        map.removeLayer(markerClusterGroup);
                    }

                    markerClusterGroup =
                        L.markerClusterGroup();

                    const ajaxMessage =
                        document.querySelector(".ajaxMessage");

                    if (ajaxMessage) {
                        ajaxMessage.style.display = "block";
                    }

                </script>';
        }


        /*
         * Marker JavaScript.
         */
        $output = $this->getMarkerJs(
            $request,
            $locations,
            '',
            $latLon,
            $radius
        );


        /*
         * Location list.
         *
         * page == -1 means: markers only.
         */
        if (
            (int)($requestArguments['page'] ?? 0)
            !== -1
        ) {
            $labels = [
                'distance' =>
                    $this->translate($request, 'distance'),

                'address' =>
                    $this->translate($request, 'address'),

                'zip' =>
                    $this->translate($request, 'zip'),

                'city' =>
                    $this->translate($request, 'city'),

                'country' =>
                    $this->translate($request, 'country'),

                'phone' =>
                    $this->translate($request, 'phone'),

                'email' =>
                    $this->translate($request, 'email'),

                'fax' =>
                    $this->translate($request, 'fax'),

                'route' =>
                    $this->translate($request, 'route'),
            ];

            $output .= $this->getLocationsList(
                $request,
                $locations,
                '',
                is_array($allLocations)
                    ? $allLocations
                    : [],
                $labels
            );
        }

        return $output;
    }


    /**
     * Geocode an address using OpenStreetMap / Nominatim.
     */
    private function geocode(
        ServerRequestInterface $request,
        array $requestArguments
    ): object {
        $latLon = new \stdClass();

        $latLon->lat = 0.0;
        $latLon->lon = 0.0;
        $latLon->status = 'NOT FOUND';

        $address =
            trim((string)($requestArguments['address'] ?? ''));

        $country =
            trim((string)($requestArguments['country'] ?? ''));

        if ($address === '') {
            return $latLon;
        }

        $query = $address;

        if ($country !== '') {
            $query .= ', ' . $country;
        }

        $apiUrl =
            'https://nominatim.openstreetmap.org/search'
            . '?q='
            . rawurlencode($query)
            . '&format=json'
            . '&limit=1';

        try {
            $referer =
                (string)$request
                    ->getUri()
                    ->withQuery('')
                    ->withFragment('');

            $response =
                $this->requestFactory->request(
                    $apiUrl,
                    'GET',
                    [
                        'headers' => [
                            'Accept' =>
                                'application/json',

                            'User-Agent' =>
                                'TYPO3-myleaflet/1.0',

                            'Referer' =>
                                $referer,
                        ],

                        'timeout' => 10,
                    ]
                );

            if ($response->getStatusCode() !== 200) {
                return $latLon;
            }

            $data = json_decode(
                (string)$response->getBody(),
                true
            );

            if (
                !is_array($data)
                || !isset($data[0])
                || !is_array($data[0])
                || !isset(
                    $data[0]['lat'],
                    $data[0]['lon']
                )
            ) {
                return $latLon;
            }

            $latLon->lat =
                (float)$data[0]['lat'];

            $latLon->lon =
                (float)$data[0]['lon'];

            $latLon->status = 'OK';

        } catch (\Throwable) {
            return $latLon;
        }

        return $latLon;
    }


    /**
     * Build Leaflet marker JavaScript.
     */
    private function getMarkerJs(
        ServerRequestInterface $request,
        array $locations,
        mixed $categories,
        object $latLon,
        float $radius
    ): string {
        $output = '<script type="text/javascript">';

        $output .= '

            var markerGroup = L.featureGroup();

            if (typeof marker !== "undefined") {
                for (var i = 0; i < marker.length; i++) {

                    if (!marker[i]) {
                        continue;
                    }

                    if (map.hasLayer(marker[i])) {
                        map.removeLayer(marker[i]);
                    }

                    if (
                        typeof markerClusterGroup !== "undefined"
                    ) {
                        markerClusterGroup.removeLayer(
                            marker[i]
                        );
                    }
                }
            }

            marker = [];

            markerClusterGroup =
                L.markerClusterGroup();

        ';


        foreach ($locations as $index => $location) {
            $lat =
                (float)($location['latitude'] ?? 0);

            $lon =
                (float)($location['longitude'] ?? 0);

            if ($lat === 0.0) {
                continue;
            }


            /*
             * Custom map icon.
             */
            $leafletMapIcon =
                (string)(
                    $location['leafletmapicon']
                    ?? ''
                );

            if ($leafletMapIcon == '0') $leafletMapIcon = $this->settings['defaultIcon'];                
            if ($leafletMapIcon !== '') {
                $iconUrl =
                    '/fileadmin/ext/myleaflet/'
                    . 'Resources/Public/MapIcons/'
                    . rawurlencode($leafletMapIcon);

                $iconWidth =
                    (int)(
                        $this->settings['markerIconWidth']
                        ?? 25
                    );

                $iconHeight =
                    (int)(
                        $this->settings['markerIconHeight']
                        ?? 41
                    );

                $iconAnchor =
                    (int)($iconWidth / 2);

                $output .= '

                    var mapIcon' . $index . ' =
                        L.icon({
                            iconUrl: '
                            . json_encode(
                                $iconUrl,
                                JSON_UNESCAPED_SLASHES
                            )
                            . ',
                            iconSize: [
                                ' . $iconWidth . ',
                                ' . $iconHeight . '
                            ],
                            iconAnchor: [
                                ' . $iconAnchor . ',
                                ' . $iconHeight . '
                            ]
                        });

                    marker[' . $index . '] =
                        L.marker(
                            [
                                ' . $lat . ',
                                ' . $lon . '
                            ],
                            {
                                icon: mapIcon'
                                . $index
                                . '
                            }
                        ).addTo(markerGroup);

                ';

            } else {
                $output .= '

                    marker[' . $index . '] =
                        L.marker(
                            [
                                ' . $lat . ',
                                ' . $lon . '
                            ]
                        ).addTo(markerGroup);

                ';
            }


            /*
             * Info window.
             *
             * IMPORTANT:
             * The current PSR-7 request is forwarded
             * to renderFluidTemplate().
             */
            $output .= $this->renderFluidTemplate(
                $request,
                'AjaxLocationListInfoWindow',
                [
                    'location' =>
                        $location,

                    'categories' =>
                        $categories,

                    'i' =>
                        $index,

                    'startingPoint' =>
                        $latLon,

                    'settings' =>
                        $this->settings,
                ]
            );
        }


        /*
         * Marker clustering.
         */
        if (
            (int)(
                $this->settings['enableMarkerClusterer']
                ?? 0
            ) === 1
        ) {
            $output .= '

                if (
                    typeof markerClusterGroup !== "undefined"
                    && map.hasLayer(markerClusterGroup)
                ) {
                    map.removeLayer(markerClusterGroup);
                }

                markerClusterGroup =
                    L.markerClusterGroup();

                for (
                    var i = 0;
                    i < marker.length;
                    i++
                ) {
                    if (marker[i]) {
                        markerClusterGroup.addLayer(
                            marker[i]
                        );
                    }
                }

                map.addLayer(markerClusterGroup);

                if (
                    markerClusterGroup
                        .getLayers()
                        .length > 0
                ) {
                    map.fitBounds(
                        markerClusterGroup.getBounds()
                    );
                }

            ';

        } else {
            $output .= '

                markerGroup =
                    L.featureGroup(
                        marker.filter(
                            function(item) {
                                return !!item;
                            }
                        )
                    ).addTo(map);

                if (
                    markerGroup
                        .getLayers()
                        .length > 0
                ) {
                    map.fitBounds(
                        markerGroup.getBounds()
                    );
                }

            ';
        }

        return $output . '</script>';
    }


    /**
     * Render the location list.
     */
    private function getLocationsList(
        ServerRequestInterface $request,
        array $locations,
        mixed $categories,
        array $allLocations,
        array $labels
    ): string {
        return $this->renderFluidTemplate(
            $request,
            'AjaxLocationList',
            [
                'locations' =>
                    $locations,

                'categories' =>
                    $categories,

                'labels' =>
                    $labels,

                'settings' =>
                    $this->settings,

                'locationsCount' =>
                    count($allLocations),
            ]
        );
    }


    /**
     * Render an independent Fluid template.
     *
     * TYPO3 14:
     * - no StandaloneView
     * - ViewFactoryInterface
     * - PSR-7 request passed to ViewFactoryData
     * - render() without .html
     */
    private function renderFluidTemplate(
        ServerRequestInterface $request,
        string $template,
        array $assign = []
    ): string {
        $templateRootPaths =
            $this->configuration['view.']['templateRootPaths.']
            ?? [
                10 =>
                    'EXT:myleaflet/'
                    . 'Resources/Private/Templates/',
            ];

        $partialRootPaths =
            $this->configuration['view.']['partialRootPaths.']
            ?? [
                10 =>
                    'EXT:myleaflet/'
                    . 'Resources/Private/Partials/',
            ];

        $layoutRootPaths =
            $this->configuration['view.']['layoutRootPaths.']
            ?? [
                10 =>
                    'EXT:myleaflet/'
                    . 'Resources/Private/Layouts/',
            ];


        $viewFactoryData =
            new ViewFactoryData(
                templateRootPaths:
                    $templateRootPaths,

                partialRootPaths:
                    $partialRootPaths,

                layoutRootPaths:
                    $layoutRootPaths,

                /*
                 * CRITICAL for TYPO3 14.3.
                 */
                request:
                    $request,
            );


        $view =
            $this->viewFactory->create(
                $viewFactoryData
            );


        $view->assignMultiple($assign);


        /*
         * Be tolerant if a caller still passes .html.
         */
        $template =
            preg_replace(
                '/\.html$/i',
                '',
                $template
            ) ?? $template;


        return $view->render(
            'Address/' . $template
        );
    }


    /**
     * Translate a locallang.xlf label.
     *
     * Intentionally does NOT use
     * Extbase LocalizationUtility.
     */
    private function translate(
        ServerRequestInterface $request,
        string $key
    ): string {
        $siteLanguage =
            $request->getAttribute('language');

        if (!$siteLanguage instanceof SiteLanguage) {
            /*
             * Fallback:
             * obtain default language from site.
             */
            $site =
                $request->getAttribute('site');

            if ($site !== null) {
                try {
                    $siteLanguage =
                        $site->getDefaultLanguage();
                } catch (\Throwable) {
                    return $key;
                }
            }
        }

        if (!$siteLanguage instanceof SiteLanguage) {
            return $key;
        }

        try {
            $languageService =
                $this->languageServiceFactory
                    ->createFromSiteLanguage(
                        $siteLanguage
                    );

            $label =
                $languageService->sL(
                    'LLL:EXT:myleaflet/'
                    . 'Resources/Private/Language/'
                    . 'locallang.xlf:'
                    . $key
                );

            return $label !== ''
                ? $label
                : $key;

        } catch (\Throwable) {
            return $key;
        }
    }


    /**
     * Create an HTML PSR-7 response.
     */
    private function createHtmlResponse(
        string $content,
        int $statusCode = 200,
        array $headers = []
    ): ResponseInterface {
        $response =
            $this->responseFactory
                ->createResponse($statusCode)
                ->withHeader(
                    'Content-Type',
                    'text/html; charset=utf-8'
                );

        foreach ($headers as $name => $value) {
            $response =
                $response->withHeader(
                    $name,
                    $value
                );
        }

        $response
            ->getBody()
            ->write($content);

        return $response;
    }


    /**
     * Kept for compatibility if it is called elsewhere.
     */
    public function getChildren(
        array $items,
        int $id,
        string $children = ''
    ): string {
        foreach ($items as $item) {
            if (
                (int)($item['parent'] ?? 0)
                === $id
            ) {
                $children =
                    $this->getChildren(
                        $items,
                        (int)($item['uid'] ?? 0),
                        $children
                    );
            }
        }

        return $id . ',' . $children;
    }
}
