<?php

/**
 * @return array[]
 */
function getRoutes(): array
{
	global $controllersPath;

	return [
		'' => ["$controllersPath/HomeController.php", 'index'],
		'home' => ["$controllersPath/HomeController.php", 'index'],
		'services' => ["$controllersPath/ServicesController.php", 'index'],
		'contact' => ["$controllersPath/ContactController.php", 'index'],
		'login' => ["$controllersPath/LoginController.php", "index"],
		'register' => ["$controllersPath/RegisterController.php", "index"],
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
	global $page;
	$routes = getRoutes();

	if (!array_key_exists($page, $routes))
	{
		logFile("[" . date('Y-m-d H:i:s') . "] /$page was attempted to be loaded but Controller doesn't exist.\n");
		// Redirige vers la page d'accueil dans le cas où la page souhaitée n'existe pas.
		header("Location: /");
		die();
	}

	return $routes[$page];
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
		global $viewsPath;
		$result = $functionName($viewsPath);
		$file = $viewsPath . '/' . $result['view'] . '.php';

		if (isset($result['view']) && file_exists($file))
		{
			$data = $result['data'];
			include_once ($file);
		}
		else
		{
			$logMessage = "[" . date('Y-m-d H:i:s') . "] View file " . $result['view'] . " not found.\n";
			logFile($logMessage);
		}
	}
	else
	{
		$logMessage = "[" . date('Y-m-d H:i:s') . "] Function $functionName not found.\n";
		logFile($logMessage);
	}
}
