<?php
namespace App\FrontModule\Model;

use Nette\Database\Context;
use Nette\Http\Request;

class Config extends BaseModel
{
	/** @var Request */
	private $request;
	private $data = array();
	private $loaded = false;

	public function __construct(Context $database, Request $request)
	{
		parent::__construct($database);
		$this->database = $database;
		$this->request = $request;
	}

	/**
	 * @param $key
	 * @return null
	 */
	public function get($key)
	{
		$this->load();
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * @param $key
	 * @return bool
	 */
	public function has($key)
	{
		$this->load();
		return isset($this->data[$key]);
	}

	/**
	 * Load configuration from database
	 */
	private function load()
	{
		if ($this->loaded) {
			return;
		}
		$this->loaded = true;

		$type = $this->request->url->scheme == "https" ? 'ssl' : 'url';

		$url = $this->request->url;
		$baseUrl = str_replace('www.', '', $url->getHost()) . rtrim($url->getPath(), '/') . '/';

		$store = $this->database
			->query("SELECT store_id FROM " . self::PREFIX . "store WHERE REPLACE(`{$type}`, 'www.', '') = ?", $baseUrl)
			->fetch();

		$store_id = $store ? $store->store_id : 0;
		$this->set('config_store_id', $store_id);

		$settings = $this->database
			->table(self::PREFIX . 'setting')
			->where('store_id', array_unique(array(0, $store_id)))
			->order('store_id ASC');

		foreach($settings as $result) {
			if($result->serialized) {
				$this->set($result->key, unserialize($result->value));
			} else {
				$this->set($result->key, $result->value);
			}
		}

//		if (!$store_query->num_rows) { // TODO
//			$this->set('config_url', HTTP_SERVER);
//			$this->set('config_ssl', HTTPS_SERVER);
//		}
	}
}