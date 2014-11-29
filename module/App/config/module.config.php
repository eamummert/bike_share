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
    'controllers' => [
        'invokables' => [
			'App:Admin' => 'App\Controller\Admin',
            'App:Index' => 'App\Controller\Index',
			'App:Movie' => 'App\Controller\Movie',
			'App:Music' => 'App\Controller\Music',
			'App:Television' => 'App\Controller\Television',
			'App:User' => 'App\Controller\User',
        ],
    ],
    'doctrine' => [
		'driver' => [
			'app_entities' => [
				'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
				'cache' => 'array',
				'paths' => [__DIR__.'/../src/App/Entity']
			],
			'orm_default' => [
				'drivers' => [
					'App\Entity' => 'app_entities',
				],
			],
		],
	],
	'form_elements' => [
		'invokables' => [
			'App:Movie' => 'App\Form\Movie',
			'App:TvSeason' => 'App\Form\TvSeason',
			'App:TvShow' => 'App\Form\TvShow',
			'App:User' => 'App\Form\User',

			'App:Fieldset:TvSeason' => 'App\Form\Fieldset\TvSeason',
		],
	],
	'libs' => [
		'require_js' => [
			'deps' => [
				'App/js/Global',
				'App/js/autosize.min',
				'App/js/video',
			],
		],
	],
	'mpd_config' => [
		'port' => 5858
	],
	'router' => Yaml::parse(__DIR__ .'/router.yaml'),
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../views/app/layout/layout.phtml',
            'layout/music'           => __DIR__ . '/../views/app/layout/music.phtml',
            'app/index/index' => __DIR__ . '/../views/app/mvc/index/index.phtml',
            'error/404'               => __DIR__ . '/../views/error/404.phtml',
            'error/index'             => __DIR__ . '/../views/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../views',
        ],
		'layouts' => [
			'App:Music' => 'layout/music',
		],
    ],
	'view_helpers' => [
		'factories' => [
			'CanWatch' => 'App\Factory\View\Helper\CanWatch',
		],
	],
];
