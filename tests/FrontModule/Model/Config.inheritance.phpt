<?php

/**
 * Test: App\FrontModule\Model\Config
 */

use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


$container = getContainer();

/** @var \Nette\Database\Context $context */
$context = $container->getByType('\Nette\Database\Context');

lockDatabase();
loadDatabaseStructure($context);

$store = array(
	array(
		'store_id' => 1,
		'name' => 'store1',
		'url' => 'example.com/',
		'ssl' => 'example.com/',
	),
	array(
		'store_id' => 2,
		'name' => 'store2',
		'url' => 'example.com/folder/',
		'ssl' => 'example.com/folder/',
	), array(
		'store_id' => 3,
		'name' => 'store3',
		'url' => 'domain.tld/',
		'ssl' => 'domain.tld/',
	),
);

$context->table('oc_store')->insert($store);

$settings = array(
	array(
		'store_id' => 0,
		'group' => '',
		'key' => 'test-key',
		'value' => 'test-value-store0',
		'serialized' => 0,
	),
	array(
		'store_id' => 1,
		'group' => '',
		'key' => 'test-key',
		'value' => 'test-value-store1',
		'serialized' => 0,
	),
	array(
		'store_id' => 2,
		'group' => '',
		'key' => 'test-key',
		'value' => 'test-value-store2',
		'serialized' => 0,
	),
	array(
		'store_id' => 0,
		'group' => '',
		'key' => 'test-key2',
		'value' => 'test-value2-store0',
		'serialized' => 0,
	),
	array(
		'store_id' => 3,
		'group' => '',
		'key' => 'test-key2',
		'value' => 'test-value2-store2',
		'serialized' => 0,
	),
);

$context->table('oc_setting')->insert($settings);


$model = new \App\FrontModule\Model\Config($context, new \Nette\Http\Request(
	new \Nette\Http\UrlScript('http://www.example.com')
));

Assert::same(1, $model->get('config_store_id')); // address recognized as store 1


Assert::same('test-value-store1', $model->get('test-key'));

Assert::same('test-value2-store0', $model->get('test-key2'));

unset($model);
$model = new \App\FrontModule\Model\Config($context, new \Nette\Http\Request(
	new \Nette\Http\UrlScript('http://www.example.com/folder')
));

Assert::same(2, $model->get('config_store_id')); // address recognized as store 2

Assert::same('test-value-store2', $model->get('test-key'));

Assert::same('test-value2-store0', $model->get('test-key2'));
