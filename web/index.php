<?php

// web/index.php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../config.php';

$app = new Silex\Application();
$app['debug'] = true;

// *** Service Providers ***
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
		"db.options" => array(
			'driver'   => 'pdo_mysql',
			'charset'  => 'utf8',
			'host'     => DB_HOST,
			'dbname'   => DB_NAME,
			'user'     => DB_USER,
			'password' => DB_PASS,
		),
	)
);

$app->register(new Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider());
$app['orm.proxies_dir'] = __DIR__.'/../cache/doctrine/proxies';
$app['orm.em.options'] = array(
	'mappings' => array(
		array(
			'type' => 'annotation',
			'path' => __DIR__ . '/../src/entities',
			'namespace' => 'AvantLink\\Entity\\',
		),
	),
);

$app->register(new JDesrosiers\Silex\Provider\JmsSerializerServiceProvider(), array(
	"serializer.srcDir" => __DIR__ . "/../vendor/jms/serializer/src",
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
	'twig.path' => __DIR__ . '/../src/views',
));

// *** Admin Routes ***
$app->get('/', 'AvantLink\\Controller\\AdminController::defaultAction');

// *** API Routes ***
$app->get('/api/get-task/{id}', 'AvantLink\\API\\AvantLinkAPI::getTask');
$app->post('/api/add-task', 'AvantLink\\API\\AvantLinkAPI::addTask');
$app->get('/api/remove-task/{id}', 'AvantLink\\API\\AvantLinkAPI::removeTask');
$app->get('/api/get-all-tasks', 'AvantLink\\API\\AvantLinkAPI::getAllTasks');

$app->run();
