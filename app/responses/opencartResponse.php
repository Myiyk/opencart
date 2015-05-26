<?php

namespace App\Response;

use Nette;


class OpenCartResponse extends Nette\Object implements Nette\Application\IResponse
{
	/** @var mixed */
	private $source;


	/**
	 * @param  mixed  renderable variable
	 */
	public function __construct($source)
	{
		$this->source = $source;
	}


	/**
	 * @return mixed
	 */
	public function getSource()
	{
		return $this->source;
	}


	/**
	 * Sends response to output.
	 * @return void
	 */
	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse)
	{
		$httpResponse->setContentType('text/html; charset=utf-8');
		if ($this->source instanceof Nette\Application\UI\ITemplate) {
			$this->source->render();

		} else {
			echo $this->source;
		}
	}

}
