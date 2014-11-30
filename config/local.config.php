<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */

return [
	'doctrine' => [
		'connection' => [
			'orm_default' => [
				'driverClass' => 'Doctrine\DBAL\Driver\PDOMySql\Driver',
				'params' => [
					'host' => 'localhost',
					'port' => '3306',
					'user' => 'demouser',
					'password' => 'password',
					'dbname' => 'bike_share',
				],
			],
		],
	],
	'rdn_db_adapters' => [
		'adapters' => [
			'default' => [
				'driver' => 'pdo_mysql',
				'hostname' => 'localhost',
				'port' => '3306',
				'username' => 'demouser',
				'password' => 'password',
				'database' => 'bike_share',
			],
		],
	],
	'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => function() {
                $adapter = new BjyProfiler\Db\Adapter\ProfilingAdapter([
                    'driver'    => 'pdo',
                    'dsn'       => 'pgsql:dbname=media;host=localhost',
                    'database'  => 'bike_share',
                    'username'  => 'demouser',
                    'password'  => 'password',
                    'hostname'  => 'localhost',
                ]);
 
                $adapter->setProfiler(new BjyProfiler\Db\Profiler\Profiler);
                $adapter->injectProfilingStatementPrototype();
                return $adapter;
            },
        ],
    ],	
];
