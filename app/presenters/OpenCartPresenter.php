<?php

namespace App\Presenters;

use App\Response\OpenCartResponse;
use Nette;


class OpenCartPresenter extends Nette\Application\UI\Presenter
{

	public function startup()
	{
		parent::startup();
		$this->canonicalize(); // optimize url
	}

	public function actionCompatibility()
	{
		ob_start();

		define('OPEN_CART_ENABLE', true);
		include __DIR__ . '/../../www/oc-index.php';

		$html = ob_get_clean();
		$this->sendResponse(new OpenCartResponse($html));
	}

}
