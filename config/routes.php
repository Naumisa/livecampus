<?php

/**
 * @return array[]
 */
function getRoutes(): array
{
	return [
		'' => [getControllersPath() . "/HomeController.php", 'index'],
		'home' => [getControllersPath() . "/HomeController.php", 'index'],
		'services' => [getControllersPath() . "/ServicesController.php", 'index'],
		'contact' => [getControllersPath() . "/ContactController.php", 'index'],
		'login' => [getControllersPath() . "/LoginController.php", "index"],
		'register' => [getControllersPath() . "/RegisterController.php", "index"],
	];
}

/**
 * @return string
 */
function getPage(): string
{
	$request = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$page = parse_url($request, PHP_URL_PATH);
	return ltrim($page, '/');
}

/**
 * @return array
 */
function getController(): array
{
	$routes = getRoutes();

	if (!array_key_exists(getPage(), $routes))
	{
		logFile("/" . getPage() . " was attempted to be loaded but Controller doesn't exist.");
		// Redirige vers la page d'accueil dans le cas où la page souhaitée n'existe pas.
		header("Location: /");
		die();
	}

	return $routes[getPage()];
}

/**
 * @param array $controller
 * @return void
 */
function getView(array $controller): void
{
	include_once ($controller[0]);

	$functionName = $controller[1];

	if (function_exists($functionName))
	{
		$result = $functionName(getViewsPath());
		$file = getViewsPath() . '/' . $result['view'] . '.php';

		if (isset($result['view']) && file_exists($file))
		{
			$data = $result['data'];
			include_once ($file);
		}
		else
		{
			$logMessage = "View file " . $result['view'] . " not found.";
			logFile($logMessage);
		}
	}
	else
	{
		$logMessage = "Function $functionName not found.";
		logFile($logMessage);
	}
}
