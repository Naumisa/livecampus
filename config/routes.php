<?php

/**
 * @return array[]
 */
function routes_get_navigation(): array
{
	$path = app_get_path('controllers');
	return [
		'' => ['GET', "$path/HomeController.php", 'index'],
		'home' => ['GET', "$path/HomeController.php", 'index'],
		'services' => ['GET', "$path/ServicesController.php", 'index'],
		'contact' => ['GET', "$path/ContactController.php", 'index'],
		'login' => ['GET', "$path/LoginController.php", 'index'],
            'login/confirm' => ['POST', "$path/LoginController.php", 'login'],
		'register' => ['GET', "$path/RegisterController.php", 'index'],
		'user/profile' => ['GET', "$path/UserController.php", 'profile'],
        'file' => ['GET', "$path/FileController.php", 'index'],

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
	$isGoodRequest = $_SERVER['REQUEST_METHOD'] === $controller[0];

	if (!$isGoodRequest)
	{
		header('Location: /');
		die();
	}

	include_once ($controller[1]);

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
