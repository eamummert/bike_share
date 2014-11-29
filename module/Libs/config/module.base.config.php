<?php

$ref = new ReflectionClass($this);
$module = $ref->getNamespaceName();
$source = dirname($ref->getFileName());

$inflector = new Zend\Filter\Word\CamelCaseToDash;
$package = strtolower($inflector->filter($module));

$mvcDir = $package . '/mvc';
$viewDir = dirname(dirname($source)) . '/views';
$mvcPath = $viewDir . '/' . $mvcDir;

return [
	'entity_managers' => [
		'modules' => [
			$module => $module,
		],
		'utilities' => [
			$module => true,
		],
	],
	'view_manager' => [
		'controller_map' => [
			$module => is_dir($mvcPath) ? $mvcDir : false,
		],

		'template_path_stack' => [
			$module => $viewDir
		],
	],
	'libs' => [
		'require_js' => [
			'packages' => [
				$module,
			],
			'paths' => [
				$module => $package .'/js',
			],
		],
	],
];

