<?php

use app\Models\UserModel;
use JetBrains\PhpStorm\NoReturn;

/**
 * Affiche le profil d'un utilisateur spécifique identifié par son ID. Cette fonction récupère
 * l'ID de l'utilisateur à partir des paramètres de la route, recherche l'utilisateur dans la base
 * de données, et prépare les données de l'utilisateur pour la vue de profil.
 *
 * @return array Retourne un tableau associatif contenant les données de l'utilisateur (`'user'`)
 *               et le nom de la vue associée (`"user/profile"`).
 */
function show(): array
{
	$data = [];

	//TODO: Make a real show profile view
	$id = routes_get_params('id');

	if ($id != null)
	{
		$user = new UserModel();
		$user = $user->find((int) $id);

		$data['user'] = $user;
	}

	return [
		'data' => $data,
		'view' => "user/profile",
	];
}

/**
 * Récupère et affiche une liste de tous les utilisateurs enregistrés. Cette fonction est
 * utilisée pour afficher tous les utilisateurs dans l'interface d'administration.
 *
 * @return array Retourne un tableau associatif contenant la liste des utilisateurs (`'users'`)
 *               et le nom de la vue associée (`"admin/users"`).
 */
function show_users(): array
{
	$data = [];

	$users = UserModel::all();

	$data['users'] = $users;

	return [
		'data' => $data,
		'view' => "admin/users",
	];
}

/**
 * Prépare et affiche le tableau de bord de l'utilisateur authentifié. Cette fonction récupère
 * les fichiers de l'utilisateur, calcule l'espace disque utilisé, et prépare les données pour la vue du tableau de bord.
 *
 * @return array Retourne un tableau associatif contenant les fichiers de l'utilisateur (`'files'`),
 *               le chemin de stockage des fichiers (`'user_storage_path'`), l'espace disque utilisé (`'disk_space'`),
 *               et le nom de la vue du tableau de bord (`"user/dashboard"`).
 */
function dashboard(): array
{
	$user = auth_user();
	$data['files'] = $user->files();
	$data['user_storage_path'] = $user->folder()->name_random;

	if (!empty($data['files']) > 0) {
		$disk_space = 0;

		foreach ($data['files'] as $file)
		{
			$disk_space += filesize($file->path());
		}

		$data['disk_space'] = $disk_space;
	}

	return [
		'data' => $data,
		'view' => "user/dashboard",
	];
}

/**
 * Affiche le profil de l'utilisateur actuellement authentifié. Cette fonction récupère les données
 * de l'utilisateur authentifié et prépare les données pour la vue de profil.
 *
 * @return array Retourne un tableau associatif contenant les données de l'utilisateur (`'user'`)
 *               et le nom de la vue associée (`"user/profile"`).
 */
function profile(): array
{
	$data['user'] = auth_user();

	return [
		'data' => $data,
		'view' => "user/profile",
	];
}

/**
 * Traite la soumission du formulaire de mise à jour du profil de l'utilisateur. Vérifie et met à jour
 * les informations de l'utilisateur dans la base de données. Gère également la validation du mot de passe et
 * la confirmation du mot de passe.
 *
 * @return void Redirige vers la vue de profil après la mise à jour des informations de l'utilisateur.
 *               Les messages de session peuvent être utilisés pour indiquer le succès ou les erreurs.
 * @throws Exception Si une erreur survient lors de la mise à jour des informations de l'utilisateur.
 */
#[NoReturn] function edit() : void
{
	# Get current user's datas
	$user = auth_user();

	# Get user's new datas from the form
	$new_username = trim(
		filter_input(
			INPUT_POST,
			'username',
			FILTER_DEFAULT
		)
	);
	$new_email = trim(
		filter_input(
			INPUT_POST,
			'email',
			FILTER_DEFAULT
		)
	);
	$new_password = trim(
		filter_input(
			INPUT_POST,
			'password',
			FILTER_DEFAULT
		)
	);
	$confirm_password = trim(
		filter_input(
			INPUT_POST,
			'password_confirm',
			FILTER_DEFAULT
		)
	);

	# Update user's datas if modified
	if (
		$user->email !== $new_email && !empty($new_email) ||
		$user->username !== $new_username && !empty($new_username)||
		!empty($new_password) && !empty($confirm_password)
	) {
		$user->email = !empty($new_email) ? $new_email : $user->email;
		$user->username = !empty($new_username) ? $new_username : $user->username;
		if ($new_password !== $confirm_password)
		{
			log_session("Le mot de passe ne correspond pas.");
			header('Location: /user/profile');
			die();
		}
		$user->password = !empty($new_password) ? password_hash($new_password, PASSWORD_DEFAULT) : $user->password;
		if (!$user->save()) {
			log_session("L'adresse mail que vous venez d'entrer existe déjà.");
		}
	}
	else {
		log_session("Vous n'avez changé aucun champ.");
	}

	header('Location: /user/profile');
	die();
}

/**
 * Prépare la vue de connexion pour l'utilisateur. Cette fonction est appelée pour afficher
 * le formulaire de connexion.
 *
 * @return array Retourne un tableau associatif vide (`'data'`) et le nom de la vue de connexion
 *               (`"user/auth/login"`), car aucune donnée préalable n'est nécessaire pour cette vue.
 */
function login(): array
{
	$data = [];

	return [
		'data' => $data,
		'view' => "user/auth/login",
	];
}

/**
 * Traite la tentative de connexion de l'utilisateur. Vérifie les identifiants soumis, met à jour le token
 * de l'utilisateur en cas de succès, et redirige vers le profil de l'utilisateur ou retourne à la page de connexion
 * en cas d'échec.
 *
 * @return void En cas de succès, redirige l'utilisateur vers son profil. En cas d'échec, redirige vers la page de
 *               connexion avec un message d'erreur approprié.
 * @throws Exception En cas d'échec de la génération de tokens aléatoires.
 */
#[NoReturn] function login_attempt(): void
{
	$fields = ["user_email", "user_password"];
	$isValid = true;
	$error = '';
	$data = [];

	foreach ($fields as $field) {
		$data[$field] = filter_input(INPUT_POST, $field);
		if (!$data[$field]) {
			$isValid = false;
			header('Location: /user/login');
			log_session("Le champ $field doit être rempli. ");
		}
	}

	$result = [];
	if ($isValid) {
		$user = UserModel::first('email', $data['user_email']);

		if ($user != null)
		{
			if (password_verify($data['user_password'], $user->password))
			{
				$user->generate_token();
				$user->save();

				header('Location: /user/profile');

				$_SESSION['token'] = $user->remember_token;
				$_SESSION['email'] = $user->email;

				die();
			}

			header('Location: /user/login');
			log_session("Le mot de passe ne correspond pas.");
			die();
		}
		else
		{
			header('Location: /user/login');
			log_session("Aucun utilisateur n'existe pour cette adresse e-mail.");
			die();
		}
	}
	else
	{
		header('Location: /user/login');
		log_session($error);
		die();
	}
}

/**
 * Déconnecte l'utilisateur en effaçant son token de la session et de la base de données, puis redirige
 * vers la page d'accueil.
 *
 * @return void Cette fonction ne retourne pas de tableau mais effectue une redirection vers la page d'accueil
 *              après déconnexion.
 */
#[NoReturn] function logout(): void
{
	$user = auth_user();
	$user->remember_token = null;
	$user->save();

	unset($_SESSION['token']);
	unset($_SESSION['email']);

	header('Location: /home');
	die();
}

/**
 * Prépare la vue d'enregistrement pour un nouvel utilisateur. Cette fonction est appelée pour afficher
 * le formulaire d'enregistrement.
 *
 * @return array Retourne un tableau associatif vide (`'data'`) et le nom de la vue d'enregistrement
 *               (`"user/auth/register"`), car aucune donnée préalable n'est nécessaire pour cette vue.
 */
function register(): array
{
	$data = [];

	return [
		'data' => $data,
		'view' => "user/auth/register",
	];
}

/**
 * Traite la tentative d'enregistrement d'un nouvel utilisateur. Vérifie la validité des données soumises,
 * crée le nouvel utilisateur si possible, et redirige vers le profil de l'utilisateur ou retourne à la page
 * d'enregistrement en cas d'échec.
 *
 * @return void En cas de succès, redirige l'utilisateur vers son profil. En cas d'échec, redirige vers la page d'enregistrement
 *               avec un message d'erreur approprié.
 * @throws Exception En cas d'échec de la génération de tokens aléatoires.
 */
#[NoReturn] function register_attempt(): void
{
	$fields = ["user_email", "user_password", "user_password_confirmation"];
	$isValid = true;
	$error = '';
	$data = [];

	foreach ($fields as $field) {
		$data[$field] = filter_input(INPUT_POST, $field);
		if (!$data[$field]) {
			$isValid = false;
			header('Location: /user/register');
			log_session("Le champ $field doit être rempli.");
		}
	}

	$isConform = $data['user_password'] === $data['user_password_confirmation'];
	if (!$isConform)
	{
		header('Location: /user/register');
		log_session("Le champ confirmation ne correspond pas au mot de passe entré.");
		$isValid = false;
		die();
	}

	$result = [];
	if ($isValid) {
		$findUser = UserModel::first('email', $data['user_email']);

		if (empty($findUser))
		{
			$newData = [];
			foreach ($data as $key => $value)
			{
				$newData[str_replace('user_', '', $key)] = $value;
			}

			$user = UserModel::create($newData);
			$user->generate_token();
			$user->save();

			header('Location: /user/profile');

			$_SESSION['token'] = $user->remember_token;
			$_SESSION['email'] = $user->email;

			die();
		}
		else
		{
			header('Location: /user/register');
			log_session("Un autre utilisateur existe déjà pour cette adresse e-mail.");
			die();
		}
	}
	else
	{
		header('Location: /user/register');
		log_session($error);
		die();
	}
}
