<?php

/**
 * @return array[]
 */
function routes_get_navigation(): array
{
	$path = app_get_path('controllers');
	return [
		'' => ["$path/HomeController.php", 'index'],
		'home' => ["$path/HomeController.php", 'index'],
		'services' => ["$path/ServicesController.php", 'index'],
		'contact' => ["$path/ContactController.php", 'index'],
		'login' => ["$path/LoginController.php", "index"],
		'register' => ["$path/RegisterController.php", "index"],
	];
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
 * @return array
 */
function routes_get_controller(): array
{
	$routes = routes_get_navigation();

	if (!array_key_exists(routes_get_page(), $routes))
	{
		log_file("/" . routes_get_page() . " was attempted to be loaded but Controller doesn't exist.");
		// Redirige vers la page d'accueil dans le cas où la page souhaitée n'existe pas.
		header("Location: /");
		die();
	}

	return $routes[routes_get_page()];
}

/**
 * @param array $controller
 * @return void
 */
function routes_get_view(array $controller): void
{
	include_once ($controller[0]);

	$functionName = $controller[1];

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
