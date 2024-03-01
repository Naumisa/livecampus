<?php

/**
 * Récupère le chemin d'accès spécifié à partir d'un ensemble prédéfini de chemins.
 * @param string $key Clé identifiant le chemin d'accès souhaité.
 * @return string Le chemin d'accès correspondant à la clé.
 */
function app_get_path (string $key): string
{
	$paths = [
		'resources' 		=> '../resources/',
		'views' 			=> '../resources/views/',
		'controllers' 		=> '../app/Controllers/',
		'models' 			=> '../app/Models/',
		'public_storage' 	=> '/storage/',
		'logs' 				=> '../storage/logs/',
	];

	return $paths[$key];
}

/**
 * Charge les variables d'environnement depuis un fichier .env pour une configuration centralisée.
 * @return void
 */
function app_get_environment(): void
{
	$path = "../.env";
	if (!file_exists($path)) {
		log_file("e fichier .env n'existe pas : $path");
	}

	$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		// Ignorer les commentaires
		if (str_starts_with(trim($line), '#')) {
			continue;
		}

		list($name, $value) = explode('=', $line, 2);
		$name = trim($name);
		$value = trim($value);

		if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
			putenv(sprintf('%s=%s', $name, $value));
			$_ENV[$name] = $value;
			$_SERVER[$name] = $value;
		}
	}
}

app_get_environment();

// Chargement des fichiers de configuration spécifiques à l'application
$configs = [
	'database',
	'auth',
	'routes',
	'languages',
	'logging',
];

foreach ($configs as $config)
{
	require_once ("../config/$config.php");
}

// TODO: Use this for Authentification
include_once (app_get_path('models') . "UserModel.php");

user_migrate();
if (count(user_get_data_with_id(1)) == 0)
{
	user_create(user_get_data_array("admin", "admin@email.com", "password", 1));
}

include_once (app_get_path('models') . "FileModel.php");

file_migrate();
// END-TODO

// Initialisation du système de liens de navigation selon le statut d'authentification
$loggedOutLinks = [
	'home' => 'navigation.home',
	'services' => 'navigation.services',
	'contact' => 'navigation.contact',
	'login' => 'navigation.login',
	'register' => 'navigation.register',
];

$loggedLinks = [
	'dashboard' => 'navigation.dashboard',
	'file' => 'navigation.file',
];

$adminLinks = [
	'users' => 'navigation.users',
];
