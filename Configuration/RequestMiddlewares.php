<?php


return [
    'frontend' => [
        'typo3/cms-frontend/eid' => [
            'disabled' => false,
        ],		
        'wsr/myleaflet/map-utilities' => [
			'disabled' => false,
            'target' => \WSR\Myleaflet\Middleware\MapUtilities::class,
            'before' => [
//				'typo3/cms-frontend/prepare-tsfe-rendering'
//				'typo3/cms-frontend/shortcut-and-mountpoint-redirect'
//			'typo3/cms-frontend/content-length-headers'
			],
            'after' => [
//				'typo3/cms-frontend/prepare-tsfe-rendering'
				'typo3/cms-frontend/tsfe'
            ],
        ],
    ]
];

