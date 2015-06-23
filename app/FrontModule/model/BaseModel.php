<?php
namespace App\FrontModule\Model;


use Nette\Database\Context;
use Nette\Object;

abstract class BaseModel extends Object
{
	const PREFIX = 'oc_';

	/** @var Context */
	protected $database;

	public function __construct(Context $database)
	{
		$this->database = $database;
	}
}