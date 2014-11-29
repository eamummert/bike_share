<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
use Symfony\Component\Yaml\Yaml;

return [
    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [
            ],
        ],
    ],
	'console_commands' => [
		'factories' => [
			'Libs:AssestsPublish' => 'Libs\Factory\Console\Command\AssestsPublish',
		],
	],
    'controllers' => [
        'invokables' => [],
    ],
    'controller_plugins' => [
		'aliases' => [
			'Flash' => 'flashmessenger',
			'Auth' => 'ZfcUserAuthentication',
		],
		'factories' => [
			'Controller' => 'Libs\Factory\Controller\Plugin\Controller',
			'Entity' => 'Libs\Factory\Controller\Plugin\Entity',
			'Form' => 'Libs\Factory\Controller\Plugin\Form',
			'IsAllowed' => 'Libs\Factory\Controller\Plugin\IsAllowed',
			'Mpd' => 'Libs\Factory\Controller\Plugin\Mpd',
			'Transmission' => 'Libs\Factory\Controller\Plugin\Transmission',
		],
	],
	'form_elements' => [
		'invokables' => [
			'Collection' => 'Libs\Form\Element\Collection',
		],
	],
    'doctrine' => [
		'driver' => [
			'libs_entities' => [
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => [__DIR__.'/../src/Libs/Entity']
			],
			'orm_default' => [
				'drivers' => [
					'Libs\Entity' => 'libs_entities',
				],
			],
		],
	],
	'libs' => [
		'listeners' => [
			'AdminToolbar' => 999,
			'AuthGuard',
			'Layout',
			'RequireJS',
		],
		'require_js' => [
			'baseUrl' => '/modules',
			'library' => 'libs/js/require-2.1.10.js',
			'paths' => [
				'jquery' => [
					'//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min',
					'libs/js/jquery-1.11.0',
				],
			],
			'shim' => [],
			'deps' => [],
		],
	],
	'rdn_entity_managers' => [
		'managers' => [
			'Libs' => [],
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
	//'router' => Yaml::parse(__DIR__ .'/router.yaml'),
    'service_manager' => [
        'abstract_factories' => [
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
		'factories' => [
			//'ListenerManager' => 'Libs\Factory\Listener\ListenerManager',
		],
		'invokables' => [
			'AdminToolbar' => 'Libs\Listener\AdminToolbar',
			'AuthGuard' => 'Libs\Listener\AuthGuard',
			'Layout' => 'Libs\Listener\Layout',
			'RequireJS' => 'Libs\Listener\RequireJS',
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
	'transmission' => [
		'username' => 'eamummert',
		'password' => 'Krzyzmumski',
	],
	'view_helpers' => [
		'aliases' => [
			'Auth' => 'ZfcUserIdentity',
			'Flash' => 'FlashMessenger',
		],
		'factories' => [
			'IsAllowed' => 'Libs\Factory\View\Helper\IsAllowed',
			'RequireJS' => 'Libs\Factory\View\Helper\RequireJS',
		],
		'invokables' => [
			'Form' => 'Libs\View\Helper\Form',
			'FormCollection' => 'Libs\View\Helper\FormCollection',
			'FormElement' => 'Libs\View\Helper\FormElement',
			'FormRow' => 'Libs\View\Helper\FormRow',
			'PaginatorTable' => 'Libs\View\Helper\PaginatorTable',
		],
	],
	'view_helper_config' => [
		'flashmessenger' => [
			'message_open_format' => '<div%s>',
			'message_separator_string' => "<br/>",
			'message_close_string' => '</div>',
		],
	],
];
