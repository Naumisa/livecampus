<?php

/**
 * @return array[]
 */
function routes_get_navigation(): array
{
	$path = app_get_path('controllers');
	return [
		// 'lien/lien'			=> ['Methode requise'], "Controller", 'function du Controller', '<nom de la route>', isLoggedIn()],

		'' 						=> ['GET', "$path/HomeController.php", 'index', 'home', false],
		'home' 					=> ['GET', "$path/HomeController.php", 'index', 'home', false],

		'services' 				=> ['GET', "$path/ServicesController.php", 'index', 'services', false],

		'contact' 				=> ['GET', "$path/ContactController.php", 'index', 'contact', false],

		'user/profile' 			=> ['GET', "$path/UserController.php", 'index', 'profile', true],
		'user/dashboard' 		=> ['GET', "$path/UserController.php", 'dashboard', 'dashboard', true],
		'user/login' 			=> ['GET', "$path/UserController.php", 'login', 'login', false],
		'user/login/confirm' 	=> ['POST', "$path/UserController.php", 'login_attempt', 'login.confirmation', false],
		'user/register' 		=> ['GET', "$path/UserController.php", 'register', 'register', false],
		'user/register/confirm' => ['POST', "$path/UserController.php", 'register_attempt', 'register.confirmation', false],
		'user/logout' 			=> ['GET', "$path/UserController.php", 'logout', 'logout', true],

        'file' 					=> ['GET', "$path/FileController.php", 'index', 'file', true],
	];
}

/**
 * @param string $key
 * @return string
 */
function routes_go_to_route(string $key): string
{
	$routes = routes_get_navigation();
	foreach ($routes as $route => $values)
	{
		if ($values[3] === $key)
		{
			if ($values[4] && isLoggedIn() || !$values[4] && !isLoggedIn())
			{
				return "/$route";
			}
		}
	}

	if (isLoggedIn())
	{
		return '/dashboard';
	}
	else
	{
		return '/home';
	}
}

/**
 * @return string
 */
function routes_get_page(): string
{
	$request = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$page = parse_url($request, PHP_URL_PATH);
	return ltrim($page, '/');
}

/**
 * @return string
 */
function routes_get_route(): string
{
	$request = routes_get_page();
	$routes = routes_get_navigation();

	foreach ($routes as $route => $value)
	{
		if ($route === $request)
		{
			return $value[3];
		}
	}

	return '';
}

/**
 * @return array
 */
function routes_get_controller(): array
{
	$routes = routes_get_navigation();

	if (!array_key_exists(routes_get_page(), $routes))
	{
		log_file("/" . routes_get_page() . " was attempted to be loaded but Controller doesn't exist.");
		// Redirige vers la page d'accueil dans le cas où la page souhaitée n'existe pas.
		return $routes['home'];
	}

	if ($routes[routes_get_page()][4] && isLoggedIn() || !$routes[routes_get_page()][4] && !isLoggedIn())
	{
		return $routes[routes_get_page()];
	}

	if (isLoggedIn())
	{
		return $routes['user/dashboard'];
	}
	else
	{
		return $routes['home'];
	}
}

/**
 * @param array $controller
 * @return void
 */
function routes_get_view(array $controller): void
{
	$isGoodRequest = $_SERVER['REQUEST_METHOD'] === $controller[0];

	include_once ($controller[1]);

	if (!$isGoodRequest)
	{
		on_error();
		die();
	}

	$functionName = $controller[2];

	if (function_exists($functionName))
	{
		$result = $functionName(app_get_path('views'));
		$file = app_get_path('views') . '/' . $result['view'] . '.php';

		if (isset($result['view']) && file_exists($file))
		{
			$data = $result['data'];
			include_once ($file);
		}
		else
		{
			$logMessage = "View file " . $result['view'] . " not found.";
			log_file($logMessage);
		}
	}
	else
	{
		$logMessage = "Function $functionName not found.";
		log_file($logMessage);
	}
}
