<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Routing\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

		$router->addRoute('post/[/<id>]', array(
			'presenter' => 'Post', // presenter name
			'action' => 'show', // action name
					'postId' => [
				Route::PATTERN => '\d+',
			],
		));

		$router->addRoute('about', 'Homepage:about');
		$router->addRoute('login', 'Sign:in');
		$router->addRoute('logout', 'Sign:out');
		$router->addRoute('admin[/<id>]', 'Administration:default');
		$router->addRoute('new', 'Post:create');
		$router->addRoute('edit', 'Post:edit');
		$router->addRoute('delete', 'Post:remove');
		$router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
	
		
		return $router;

	}
}

