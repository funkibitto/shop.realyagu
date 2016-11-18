<?php
phpinfo();
exit;
$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */

$loader->registerNamespaces([
	'Realyagu\Models'      => $config->application->modelsDir,
	'Realyagu\Controllers' => $config->application->controllersDir,
	'Realyagu\Forms'       => $config->application->formsDir,
	'Realyagu'             => $config->application->libraryDir
]);
$loader->register();



