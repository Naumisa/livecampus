<?php

/**
 * @return array[] Retourne la liste des routes disponibles avec leurs paramètres
 */
function routes_get_navigation(): array
{
	$path = app_get_path('controllers');
	return [
		'home' 					=> ['GET', "$path/HomeController.php", 'index', 'home', false],

		'services' 				=> ['GET', "$path/ServicesController.php", 'index', 'services', false],

		'contact' 				=> ['GET', "$path/ContactController.php", 'index', 'contact', false],

		'user/{id}'				=> ['GET', "$path/UserController.php", 'show', 'show-user', true],
		'user/profile' 			=> ['GET', "$path/UserController.php", 'profile', 'profile', true],
		'user/dashboard' 		=> ['GET', "$path/UserController.php", 'dashboard', 'dashboard', true],
		'user/login' 			=> ['GET', "$path/UserController.php", 'login', 'login', false],
		'user/login/confirm' 	=> ['POST', "$path/UserController.php", 'login_attempt', 'login.confirmation', false],
		'user/register' 		=> ['GET', "$path/UserController.php", 'register', 'register', false],
		'user/register/confirm' => ['POST', "$path/UserController.php", 'register_attempt', 'register.confirmation', false],
		'user/logout' 			=> ['GET', "$path/UserController.php", 'logout', 'logout', true],
		'user/edit'             => ['POST', "$path/UserController.php", 'edit', 'profile.edit', true],

		'file/upload' 			=> ['POST', "$path/FileController.php", 'upload', 'file.upload', true],
		'file/delete/{id}' 		=> ['GET', "$path/FileController.php", 'delete', 'file.delete', true],
	];
}

/**
 * @param string $request
 * @return bool
 */
function get_route_parameters(string $request): bool
{
	$routePaths = explode('/', $request);


	$hasParam = false;
	$buildRequest = "";
	$toStore = [];
	foreach ($routePaths as $lastPath)
	{
		if ($lastPath[0] != ':')
		{
			$buildRequest .= sprintf("/%s", $lastPath);
		}
		else
		{
			$hasParam = true;

			$params = explode(';', ltrim($lastPath, ':'));

			$buildRequest .= sprintf("/{%s}", $params[0]);
			$toStore[$params[0]] = $params[1];
		}
	}

	if (!$hasParam)
	{
		return false;
	}

	$_REQUEST['params'] = $toStore;
	$_REQUEST['route'] = substr($buildRequest, 1);
	var_dump($buildRequest);

	return true;
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
		return '/user/dashboard';
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
function routes_get_route($request = null): array
{
	if ($request == null)
	{
		$request = routes_get_page();
	}

	$routes = routes_get_navigation();

	if (get_route_parameters($request))
	{
		$request = $_REQUEST['route'];
	}

	if (array_key_exists($request, $routes))
	{
		return $routes[$request];
	}

	return isLoggedIn() ? $routes['user/dashboard'] : $routes['home'];
}

/**
 * @return string
 */
function routes_get_route_name(): string
{ return routes_get_route()[3]; }

/**
 * @param string $key
 * @return string|null
 */
function routes_get_params (string $key): ?string
{
	return $_REQUEST['params'][$key] != null ? $_REQUEST['params'][$key] : null;
}

/**
 * @return void
 */
function routes_get_view(): void
{
	$route = routes_get_route();
	$method = $route[0];
	$controller = $route[1];
	$function = $route[2];
	$route_name = $route[3];
	$needAuth = $route[4];

	$isGoodRequest = $_SERVER['REQUEST_METHOD'] === $method;

	if (!$isGoodRequest)
	{
		on_error();
		die();
	}

	if ($needAuth && !isLoggedIn())
	{
		$route = routes_get_route('home');
		$controller = $route[1];
		$function = $route[2];
		$route_name = $route[3];
	}

	include_once ($controller);

	if (function_exists($function))
	{
		$result = $function(app_get_path('views'));
		$file = app_get_path('views') . '/' . $result['view'] . '.php';

		if (isset($result['view']) && file_exists($file))
		{
			$data = $result['data'];
			include_once ($file);
		}
		else
		{
			log_file("View file " . $result['view'] . " not found.");
		}
	}
	else
	{
		log_file("Function $function not found.");
	}
}
