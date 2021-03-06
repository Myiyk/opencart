<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


class RouterFactory
{

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList();
		$router[] = new Route('', array(
			'presenter' => 'OpenCart',
			'action' => 'compatibility',
			'route' => 'common/home'
		));
		$router[] = new Route('index.php?route=<route .+>', 'OpenCart:compatibility');
		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default');
		return $router;
	}

}
