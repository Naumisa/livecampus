<?php

/**
 * Retourne la liste des routes disponibles avec leurs paramètres.
 * Chaque route est définie par son chemin, la méthode HTTP, le chemin du contrôleur,
 * le nom de la fonction à appeler, son appellation et si l'authentification est nécessaire.
 * @return array[] La configuration des routes.
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
 * Extrait les paramètres d'une requête et les stocke dans $_REQUEST.
 * @param string $request La requête à analyser.
 * @return bool Retourne true si des paramètres ont été trouvés et traités, false sinon.
 */
function get_route_parameters(string $request): bool
{
	$routePaths = explode('/', $request);
	$hasParam = false;
	$buildRequest = "";
	$toStore = [];

	foreach ($routePaths as $segment)
	{
		if (str_starts_with($segment, ':'))
		{
			$hasParam = true;
			$params = explode(';', substr($segment, 1));
			$buildRequest .= "/{" . $params[0] . "}";
			$toStore[$params[0]] = $params[1] ?? null;
		}
		else
		{
			$buildRequest .= "/$segment";
		}
	}

	if (!$hasParam)
	{
		return false;
	}

	$_REQUEST['params'] = $toStore;
	$_REQUEST['route'] = trim($buildRequest, '/');

	return true;
}


/**
 * Redirige vers une route spécifique basée sur une clé donnée.
 * @param string $key La clé identifiant la route cible.
 * @return string L'URL de redirection.
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
 * Récupère le chemin de la page actuelle à partir de l'URL.
 * @return string Le chemin de la page actuelle.
 */
function routes_get_page(): string
{
	$request = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$page = parse_url($request, PHP_URL_PATH);
	return ltrim($page, '/');
}

/**
 * Détermine la route actuelle en se basant sur l'URL demandée.
 * Si des paramètres sont présents dans l'URL, ils sont traités et stockés.
 * @param string|null $request L'URL demandée, ou null pour utiliser l'URL actuelle.
 * @return array La configuration de la route correspondante.
 */
function routes_get_route(string $request = null): array
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
 * Récupère le nom de la route actuelle.
 *
 * @return string Le nom de la route.
 */
function routes_get_route_name(): string
{ return routes_get_route()[3]; }

/**
 * Récupère un paramètre spécifique de la requête actuelle.
 *
 * @param string $key La clé du paramètre à récupérer.
 * @return string|null La valeur du paramètre, ou null si non trouvé.
 */
function routes_get_params (string $key): ?string
{ return $_REQUEST['params'][$key] ?? null; }

/**
 * Charge la vue correspondante à la route actuelle.
 * Gère l'authentification requise pour certaines routes et les erreurs de méthode HTTP.
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
