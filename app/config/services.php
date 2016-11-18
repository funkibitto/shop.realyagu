<?php

use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Dispatcher;
use Realyagu\Utils\Security as Security;
use Realyagu\Elements\Elements;
use Realyagu\Auth\Auth;
use Realyagu\Acl\Acl;
use Realyagu\Mail\Mail;
use Phalcon\Session\Adapter\Redis;
use Phalcon\Session\Adapter\Aerospike as SessionHandler;
use Phalcon\Session\Adapter\Files as SessionAdapter;


/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'compiledPath' => $config->application->cacheDir,
                'compiledSeparator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $connection = new $class([
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ]);
    return $connection;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () {
    $config = $this->getConfig();
    return new MetaDataAdapter([
        'metaDataDir' => $config->application->cacheDir . 'metaData/'
    ]);
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function () {
    $session = new Redis([
        'uniqueId' => getenv("REDIS_UNIQUEID"),
        'host' => getenv("REDIS_ADDR"),
        'port' => getenv("REDIS_PORT"),
        'auth' => getenv("REDIS_AUTH"),
        'persistent' => false,
        'lifetime' => 3600,
        'prefix' => 'shop_',
        'index'      => 1
    ]);
    $session->start();
    $session->set('var', 'some-value');
    echo $session->get('var');

    return $session;
});

/**
 * redis cash
 */
//$di->setShared("redis", function() {
//    $redis = new Redis();
//    $redis->connect(getenv("REDIS_ADDR"), getenv("REDIS_PORT"));
//    $redis->select(1);
//    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);
//    $redis->setOption(Redis::OPT_PREFIX, $_SERVER['HTTP_HOST'].":");
//    return $redis;
//});

/**
 * Crypt service
 */
$di->set('crypt', function () {
	$config = $this->getConfig();
	$crypt = new Crypt();
	$crypt->setKey($config->application->cryptSalt);
	return $crypt;
});

/**
 * Dispatcher use a default namespace
 */
$di->set('dispatcher', function () {
	$dispatcher = new Dispatcher();
	$dispatcher->setDefaultNamespace('Realyagu\Controllers');
	return $dispatcher;
});

/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
	return require APP_PATH . '/config/routes.php';
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Custom authentication component
 */
$di->set('auth', function () {
	return new Auth();
});

/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
	return new Mail();
});

/**
 * Setup the private resources, if any, for performance optimization of the ACL.
 */
$di->setShared('AclResources', function() {
	$pr = [];
	if (is_readable(APP_PATH . '/config/privateResources.php')) {
		$pr = include APP_PATH . '/config/privateResources.php';
	}
	return $pr;
});
	
/**
 * Access Control List
 * Reads privateResource as an array from the config object.
 */
$di->set('acl', function () {
	$acl = new Acl();
	$pr = $this->getShared('AclResources')->privateResources->toArray();
	$acl->addPrivateResources($pr);
	return $acl;
});

/**
 * Logger service
 */
$di->set('logger', function ($filename = null, $format = null) {
	$config = $this->getConfig();
	$format   = $format ?: $config->get('logger')->format;
	$filename = trim($filename ?: $config->get('logger')->filename, '\\/');
	$path     = rtrim($config->get('logger')->path, '\\/') . DIRECTORY_SEPARATOR;
	$formatter = new FormatterLine($format, $config->get('logger')->date);
	$logger    = new FileLogger($path . $filename);
	$logger->setFormatter($formatter);
	$logger->setLogLevel($config->get('logger')->logLevel);
	return $logger;
});

/**
 *  Register a user component
 */
$di->set('elements', function () {
	return new Elements();
});

$di->set('security', function() {
    $security = new Security();
    $security->setWorkFactor(12);
    return $security;
}, true);







