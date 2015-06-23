<?php

namespace App\FrontModule\Model\Localization;

use App\FrontModule\Model\BaseModel;

class CurrencyModel extends BaseModel
{
	public function getCurrencyByCode($currency)
	{
		$query = $this->database->query("SELECT DISTINCT * FROM " . self::PREFIX . "currency WHERE code = ? and status = ?", $currency, 1);

		$data = $query->fetch();
		return $data ? (array) $data : false;
	}

	public function getCurrencies()
	{
//		TODO: add cache
//		$currency_data = $this->cache->get('currency');
//
//		if (!$currency_data) {
		$currency_data = array();

		$query = $this->database->query("SELECT * FROM " . self::PREFIX . "currency WHERE status = ? ORDER BY title ASC", 1);

		foreach ($query as $result) {
			$currency_data[$result['code']] = array(
				'currency_id' => $result['currency_id'],
				'title' => $result['title'],
				'code' => $result['code'],
				'symbol_left' => $result['symbol_left'],
				'symbol_right' => $result['symbol_right'],
				'decimal_place' => $result['decimal_place'],
				'value' => $result['value'],
				'status' => $result['status'],
				'date_modified' => $result['date_modified']
			);
		}

//			$this->cache->set('currency', $currency_data);
//		}

		return $currency_data;
	}
}