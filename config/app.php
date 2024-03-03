<?php

use app\Models\FileModel;
use app\Models\FileUserModel;
use app\Models\FolderModel;
use app\Models\UserModel;
use back\Models\DatabaseModel;

/**
 * Récupère le chemin d'accès spécifié à partir d'un ensemble prédéfini de chemins.
 * @param string $key Clé identifiant le chemin d'accès souhaité.
 * @return string Le chemin d'accès correspondant à la clé.
 */
function app_get_path(string $key): string
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
	'auth',
	'routes',
	'languages',
	'logging',
];

foreach ($configs as $config) {
	require_once("../config/$config.php");
}

// Création automatique des tables et d'un utilisateur admin.
$pdo = new DatabaseModel;
$pdo->set_foreign_key_check(false);

$auto_user = new UserModel();
$auto_user->migrate();

$auto_folder = new FolderModel();
$auto_folder->migrate();

$auto_file = new FileModel();
$auto_file->migrate();

$auto_file_user = new FileUserModel();
$auto_file_user->migrate();

$pdo->set_foreign_key_check(true);

if (!UserModel::find(1)) {
	$userArray = $auto_user->fill('admin', 'admin@email.com', 'password', 1);
	$auto_user->create($userArray);
}

// Initialisation du système de liens de navigation selon le statut d'authentification
$loggedOutLinks = [
	'home' => 'navigation.home',
	'login' => 'navigation.login',
	'register' => 'navigation.register',
];

$loggedLinks = [
	'dashboard' => 'navigation.dashboard',
	'files.shared' => 'navigation.files_shared',
];

$adminLinks = [
	'show-users' => 'navigation.users',
];

if (routes_is_file())
{
	routes_get_view();
}

ob_start();
