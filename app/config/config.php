<?php
/*
 * Modified: preppend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
use Phalcon\Logger;

require_once BASE_PATH . '/vendor/autoload.php';
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();

return new \Phalcon\Config([
    'database' => [
        'adapter'     => 'Mysql',
        'host'        =>  getenv('DATABASE_HOST'),
        'username'    =>  getenv('DATABASE_USER'),
        'password'    =>  getenv('DATABASE_PASS'),
        'dbname'      =>  getenv('DATABASE_NAME'),
        'charset'     => 'utf8',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
    	'formsDir'       => APP_PATH . '/forms/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => '/',
    	'publicUrl'      => getenv('DOMAIN'),
    	'cryptSalt'      => 'eEAfR|_&G&f,+vU]kdikeij:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ],
	'mail' => [
		'fromName' => getenv('MAIL_FROM_NAME'),
		'fromEmail' => getenv('MAIL_FROM_EMAIL'),
		'smtp' => [
				'server' => 'smtp.gmail.com',
                'port' => 587,  //465, 587
 				'security' => 'tls', //ssl, tls
				'username' => getenv('MAIL_SMTP_USER_NAME'),
				'password' => getenv('MAIL_SMTP_PASSWORD')
		]
	],
	'logger' => [
			'path'     => BASE_PATH . '/logs/',
			'format'   => '%date% [%type%] %message%',
			'date'     => 'D j H:i:s',
			'logLevel' => Logger::DEBUG,
			'filename' => 'application.log',
	],
	'grade' => [
		'default' => 0, 
		'administrator' => 1,
		'staff' => 2,			
	],

    'userStatus' => [
		'notAuth' => 0,
		'live' => 1,
		'gone' => 2
	],
	// 메일 보내기 활성화 여부
	'useMail' => true
]);
