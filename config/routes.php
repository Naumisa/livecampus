<?php

/**
 * Définit et retourne la configuration des routes de l'application.
 * Chaque route est mappée à un contrôleur et une méthode, avec des informations supplémentaires telles que la
 * méthode HTTP attendue, le besoin d'authentification, et une appellation unique.
 *
 * @return array Configuration des routes avec leurs chemins, méthodes HTTP, contrôleurs, noms, et niveaux
 *              d'authentification requis.
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
		'files/download/{id}'		=> ['FILE', "FileController@download", 				'file.download',			1],
		'files/delete/{id}' 		=> ['GET', 	"FileController@delete", 				'file.delete', 				1],
		'files/share' 	            => ['POST', "FileController@share", 				'file.share', 				1],
		'files/shared' 				=> ['GET', 	"FileController@shared", 				'files.shared', 			1],

		'admin/users'				=> ['GET', 	"UserController@show_users", 			'show-users', 				2],
		'admin/user/{id}'			=> ['GET', 	"UserController@show", 					'show-user', 				2],
	];
}

/**
 * Analyse une requête URL pour extraire les paramètres dynamiques et les stocke dans $_REQUEST.
 * Les paramètres sont identifiés par des préfixes ':' dans les segments de l'URL.
 *
 * @param string $request La requête URL à analyser.
 * @return bool True si des paramètres ont été extraits et stockés, false sinon.
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
 * Construit une URL pour rediriger vers une route spécifique identifiée par sa clé.
 * Peut inclure des paramètres dynamiques pour construire l'URL finale.
 *
 * @param string $key La clé de la route cible.
 * @param array|null $params Paramètres optionnels pour la route.
 * @return string L'URL construite pour la redirection.
 */
function routes_go_to_route(string $key, ?array $params = null): string
{
	$routes = routes_get_navigation();
	$user = auth_user();

	foreach ($routes as $route => $values) {
		if ($values[2] === $key) {
			if (isLoggedIn() && $user->role >= $values[3] - 1 && $values[3] !== 0 ||
				!isLoggedIn() && $values[3] === 0 ) {
				if (isset($params))
				{
					foreach ($params as $param => $value) {
						$route = str_replace("{", ":", $route);
						$route = str_replace("}", ";$value", $route);
					}

					return "/$route";
				}

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
 * Extrait le chemin de la page actuelle de l'URL de la requête.
 * Utile pour déterminer la route actuelle basée sur l'URL demandée.
 *
 * @return string Le chemin extrait de l'URL actuelle.
 */
function routes_get_page(): string
{
	$request = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$page = parse_url($request, PHP_URL_PATH);
	return ltrim($page, '/');
}

/**
 * Vérifie si la route actuelle correspond à une requête de type fichier.
 * Utilisé pour distinguer les requêtes de fichiers des autres types de requêtes dans le système de routage.
 *
 * @return bool True si la route actuelle est de type 'FILE', false sinon.
 */
function routes_is_file(): bool
{
	$route = routes_get_route();
	return $route[0] === "FILE";
}

/**
 * Détermine et retourne la configuration de la route actuelle basée sur l'URL demandée.
 * Traite également les paramètres dynamiques dans l'URL et les stocke pour un accès ultérieur.
 *
 * @param string|null $request L'URL demandée, ou null pour utiliser l'URL actuelle.
 * @return array Configuration de la route actuelle, incluant le contrôleur et la méthode à appeler.
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
 * Récupère le nom de la route actuelle basée sur la configuration déterminée par routes_get_route().
 *
 * @return string Le nom de la route actuelle.
 */
function routes_get_route_name(): string
{
	return routes_get_route()[2];
}

/**
 * Récupère une valeur de paramètre spécifique pour la requête actuelle, basée sur une clé.
 * Les paramètres sont extraits et stockés par la fonction get_route_parameters().
 *
 * @param string $key La clé du paramètre à récupérer.
 * @return string|null La valeur du paramètre, ou null si non trouvé.
 */
function routes_get_params(string $key): ?string
{
	return $_REQUEST['params'][$key] ?? null;
}

/**
 * Charge et affiche la vue correspondante à la route actuelle.
 * Vérifie l'authentification et les permissions requises pour accéder à la route,
 * redirige en cas d'accès non autorisé, et inclut le fichier de vue approprié.
 *
 * @return void
 */
function routes_get_view(): void
{
	$route = routes_get_route();
	$method = $route[0];
	$controller = explode('@', $route[1]);
	$authLevel = $route[3];
	$user = auth_user();

	$isGoodRequest = $_SERVER['REQUEST_METHOD'] === $method || routes_is_file();

	if (!isLoggedIn() && $authLevel !== 0) {
		$route = routes_get_route('home');
		$controller = explode('@', $route[1]);
		$route_name = $route[2];
		$message = "L'utilisateur n'a pas les accès requis pour afficher cette page.";
		$isGoodRequest = false;
	}
	elseif (isLoggedIn() && $authLevel === 0) {
		$route = routes_get_route('dashboard');
		$controller = explode('@', $route[1]);
		$route_name = $route[2];
		$message = "L'utilisateur n'a pas les accès requis pour afficher cette page.";
		$isGoodRequest = false;
	}

	if (!$isGoodRequest) {
		header("Location: " . routes_go_to_route($route_name ?? 'home'));
		log_file($message ?? "La requête qui a été passée ne correspond pas à celle attendue.");
		die();
	}

	$route_name = $route[2];

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
