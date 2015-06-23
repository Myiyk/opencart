<?php

/**
 * Test: App\FrontModule\Model\Localization\CurrencyModel
 */

use Tester\Assert;

require __DIR__ . '/../../../bootstrap.php';


$container = getContainer();

/** @var \App\FrontModule\Model\Localization\CurrencyModel $model */
$model = $container->getByType('\App\FrontModule\Model\Localization\CurrencyModel');

/** @var \Nette\Database\Context $context */
$context = $container->getByType('\Nette\Database\Context');

lockDatabase();
loadDatabaseStructure($context);

Assert::same(array(), $model->getCurrencies());

$data = array(
	array(
		'title' => 'US Dollar',
		'code' => 'USD',
		'symbol_left' => '$',
		'symbol_right' => '',
		'decimal_place' => '2',
		'value' => 1.0,
		'status' => 1
	),
	array(
		'title' => 'Euro',
		'code' => 'EUR',
		'symbol_left' => '',
		'symbol_right' => '€',
		'decimal_place' => '2',
		'value' => 0.5,
		'status' => 1
	),
);

$context->table('oc_currency')->insert($data);

$currencies = $model->getCurrencies();

Assert::count(2, $currencies);

usort($currencies, function ($a, $b) {
	return $b['value'] - $a['value'];
});

foreach ($currencies as $key => &$currency) {
	unset($currency['currency_id'], $currency['date_modified']);
	Assert::equal($data[$key], $currency);
}

$context->table('oc_currency')->insert(array(
	'title' => 'Koruna česká',
	'code' => 'CZK',
	'symbol_left' => '',
	'symbol_right' => 'Kč',
	'decimal_place' => '2',
	'value' => 2,
	'status' => 0 // disabled
));

$currencies = $model->getCurrencies();

Assert::count(2, $currencies);

loadDatabaseStructure($context);

$context->table('oc_currency')->insert($data);
$context->table('oc_currency')->insert(array(
	'title' => 'Koruna česká',
	'code' => 'CZK',
	'symbol_left' => '',
	'symbol_right' => 'Kč',
	'decimal_place' => '2',
	'value' => 2,
	'status' => 0 // disabled
));

$currency = $model->getCurrencyByCode('USD');
unset($currency['currency_id'], $currency['date_modified']);
Assert::equal($data[0], $currency);

$currency = $model->getCurrencyByCode('EUR');
unset($currency['currency_id'], $currency['date_modified']);
Assert::equal($data[1], $currency);

$currency = $model->getCurrencyByCode('CZK');
Assert::false($currency);
