<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return [
    'console' => [
        'router' => [
            'routes' => [
            ],
        ],
    ],
	'module_layouts' => [
		'App' => 'app/layout/layout.phtml',
		'ZfcUser' => 'app/layout/layout.phtml',
		'ArkhamHorror' => 'arkham-horror/layout/layout.phtml',
	],
	'rdn_entity_managers' => [
		'managers' => [
			'App' => [],
			'ArkhamHorror' => [],
		],
	],
	'rdn_event_listeners' => [
		'factories' => [
			'Libs:InjectTemplate' => 'Libs\Factory\Listener\InjectTemplate',
		],
	],
	'rdn_event' => [
		'listeners' => [
			'Libs:InjectTemplate',
		],
	],
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
		'factories' => [
			'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
		],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
	'view_helpers' => [
		'invokables' => [
			'Form' => 'Libs\View\Helper\Form',
			'FormCollection' => 'Libs\View\Helper\FormCollection',
			'FormElement' => 'Libs\View\Helper\FormElement',
			'FormRow' => 'Libs\View\Helper\FormRow',
			'PaginatorTable' => 'Libs\View\Helper\PaginatorTable',
		],
	],
];
