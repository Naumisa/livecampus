<?php

use app\Models\FileModel;
use app\Models\FileUserModel;
use app\Models\FolderModel;
use app\Models\UserModel;
use back\Models\DatabaseModel;

/**
 * Récupère le chemin d'accès spécifié à partir d'un ensemble prédéfini de chemins.
 * Utile pour accéder à différentes ressources de l'application en fournissant une clé descriptive.
 *
 * @param string $key Clé identifiant le chemin d'accès souhaité parmi les chemins prédéfinis.
 * @return string Le chemin d'accès absolu correspondant à la clé fournie.
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
 * Charge les variables d'environnement depuis un fichier .env pour une configuration centralisée de l'application.
 * Permet de définir des paramètres tels que les informations de connexion à la base de données, les chemins de ressources,
 * et d'autres variables d'environnement essentielles au fonctionnement de l'application.
 *
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

/**
 * Initialise la base de données en désactivant temporairement la vérification des clés étrangères,
 * migre les modèles de données pour créer les tables nécessaires si elles n'existent pas déjà,
 * et crée un utilisateur administrateur par défaut si aucun utilisateur n'est présent.
 *
 * @return void
 */
function app_auto_creation(): void
{
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
}

// Initialisation de l'environnement
app_get_environment();

// Chargement des fichiers de configuration.
// Cela inclut la configuration de l'authentification, des routes, des langues, et du système de logs.
$configs = [
	'auth',
	'routes',
	'languages',
	'logging',
];

foreach ($configs as $config) {
	require_once("../config/$config.php");
}

app_auto_creation();

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
