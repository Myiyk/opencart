<?php

namespace App\Presenters;

use App\Bridges\DatabaseTracy\ConnectionPanel;
use App\Response\OpenCartResponse;
use Nette;
use Tracy\Bar;


class OpenCartPresenter extends Nette\Application\UI\Presenter
{

	/** @var Bar @inject */
	public $tracyBar;

	public function startup()
	{
		parent::startup();
		$this->canonicalize(); // optimize url
	}

	public function actionCompatibility()
	{
		ob_start();

		$this->tracyBar->addPanel(new ConnectionPanel());

		define('OPEN_CART_ENABLE', true);
		include __DIR__ . '/../../www/oc-index.php';

		$html = ob_get_clean();
		$this->sendResponse(new OpenCartResponse($html));
	}

}
