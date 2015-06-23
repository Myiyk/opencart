<?php

/**
 * Test: App\FrontModule\Model\Config
 */

use Tester\Assert;

require __DIR__ . '/../../bootstrap.php';


$container = getContainer();

/** @var \Nette\Database\Context $context */
$context = $container->getByType('\Nette\Database\Context');

$request = new \Nette\Http\Request(
	new \Nette\Http\UrlScript('http://www.example.com')
);

/** @var \App\FrontModule\Model\Config $model */
$model = new \App\FrontModule\Model\Config($context, $request);

lockDatabase();
loadDatabaseStructure($context);

$settings = array(
	array(
		'store_id' => 0,
		'group' => '',
		'key' => 'test-key2',
		'value' => 'test-value2',
		'serialized' => 0,
	),
	array(
		'store_id' => 0,
		'group' => '',
		'key' => 'key-serialized',
		'value' => serialize(array(1, 2, 3)),
		'serialized' => 1,
	),
);

$context->table('oc_setting')->insert($settings);

Assert::false($model->has('test-key'));
Assert::null($model->get('test-key'));
$model->set('test-key', 'test-value');
Assert::true($model->has('test-key'));
Assert::same('test-value', $model->get('test-key'));

Assert::true($model->has('test-key2')); // information is saved in database
Assert::same('test-value2', $model->get('test-key2'));

Assert::same(array(1, 2, 3), $model->get('key-serialized'));
Assert::true($model->has('key-serialized'));

Assert::false($model->has('key-serialized2'));
Assert::null($model->get('key-serialized2'));
$model->set('key-serialized2', array(4, 5, 6));
Assert::true($model->has('key-serialized2'));
Assert::same(array(4, 5, 6), $model->get('key-serialized2'));
