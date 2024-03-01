<?php

/**
 * Retourne la liste des routes disponibles avec leurs paramètres.
 * Chaque route est définie par son chemin, la méthode HTTP, le chemin du contrôleur,
 * le nom de la fonction à appeler, son appellation et si l'authentification est nécessaire.
 * @return array[] La configuration des routes.
 */
function routes_get_navigation(): array
{
	return [
		'home' 						=> ['GET', 	"HomeController@index", 				'home', 					0],

		'user/login' 				=> ['GET', 	"UserController@login", 				'login', 					0],
		'user/login/confirm' 		=> ['POST', "UserController@login_attempt", 		'login.confirmation', 		0],
		'user/register' 			=> ['GET', 	"UserController@register", 				'register', 				0],
		'user/register/confirm' 	=> ['POST', "UserController@register_attempt", 		'register.confirmation', 	0],

		'user/profile' 				=> ['GET', 	"UserController@profile", 				'profile', 					1],
		'user/dashboard' 			=> ['GET', 	"UserController@dashboard", 			'dashboard', 				1],
		'user/logout' 				=> ['GET', 	"UserController@logout", 				'logout', 					1],
		'user/edit'             	=> ['POST', "UserController@edit", 					'profile.edit', 			1],

		'files/upload' 				=> ['POST', "FileController@upload", 				'file.upload', 				1],
		'files/delete/{id}' 		=> ['GET', 	"FileController@delete", 				'file.delete', 				1],
		'files/share/{id}/{email}' 	=> ['GET', 	"FileUserController@addUserToConsult", 	'file.share', 				1],
		'files/shared' 				=> ['GET', 	"FileUserController@addUserDownload", 	'files.shared', 			1],

		'admin/users'				=> ['GET', 	"UserController@show", 					'show-users', 				2],
		'admin/user/{id}'			=> ['GET', 	"UserController@show", 					'show-user', 				2],
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

	foreach ($routePaths as $segment) {
		if (str_starts_with($segment, ':')) {
			$hasParam = true;
			$params = explode(';', substr($segment, 1));
			$buildRequest .= "/{" . $params[0] . "}";
			$toStore[$params[0]] = $params[1] ?? null;
		} else {
			$buildRequest .= "/$segment";
		}
	}

	if (!$hasParam) {
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
	$user = auth_user();
	foreach ($routes as $route => $values) {
		if ($values[2] === $key) {
			if (isLoggedIn() && $user->role >= $values[3] - 1 || !isLoggedIn() && $values[3] === 0 ) {
				return "/$route";
			}
		}
	}

	if (isLoggedIn()) {
		return '/user/dashboard';
	} else {
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
	if ($request == null) {
		$request = routes_get_page();
	}

	$routes = routes_get_navigation();

	if (get_route_parameters($request)) {
		$request = $_REQUEST['route'];
	}

	if (array_key_exists($request, $routes)) {
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
{
	return routes_get_route()[2];
}

/**
 * Récupère un paramètre spécifique de la requête actuelle.
 *
 * @param string $key La clé du paramètre à récupérer.
 * @return string|null La valeur du paramètre, ou null si non trouvé.
 */
function routes_get_params(string $key): ?string
{
	return $_REQUEST['params'][$key] ?? null;
}

/**
 * Charge la vue correspondante à la route actuelle.
 * Gère l'authentification requise pour certaines routes et les erreurs de méthode HTTP.
 * @return void
 */
function routes_get_view(): void
{
	$route = routes_get_route();
	$method = $route[0];
	$controller = explode('@', $route[1]);
	$route_name = $route[2];
	$authLevel = $route[3];
	$user = auth_user();

	$isGoodRequest = $_SERVER['REQUEST_METHOD'] === $method;

	if (!$isGoodRequest) {
		log_file("Erreur dans la récupération des informations de la route demandée : l'utilisateur n'a pas les accès requis.");
		die();
	}

	if (!isLoggedIn() && $authLevel !== 0) {
		$route = routes_get_route('home');
		$controller = explode('@', $route[1]);
		$route_name = $route[2];
	}

	$path = app_get_path('controllers');
	include_once("$path/$controller[0].php");

	if (function_exists($controller[1])) {
		$result = $controller[1](app_get_path('views'));
		$file = app_get_path('views') . '/' . $result['view'] . '.php';

		if (isset($result['view']) && file_exists($file)) {
			$data = $result['data'];
			include_once($file);
		} else {
			log_file("View file " . $result['view'] . " not found.");
		}
	} else {
		log_file("Function $controller[1] not found.");
	}

	ob_end_flush();
}
