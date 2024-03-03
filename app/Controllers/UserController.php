<?php

use app\Models\UserModel;

/**
 * Affiche le profil d'un utilisateur spécifique par son ID.
 *
 * @return array Retourne les données de l'utilisateur et la vue associée.
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
 * Affiche le profil d'un utilisateur spécifique par son ID.
 *
 * @return array Retourne les données de l'utilisateur et la vue associée.
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
 * Prépare les données pour la vue du tableau de bord de l'utilisateur.
 *
 * @return array Retourne un tableau avec les données et la vue du tableau de bord.
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
 * Affiche le profil de l'utilisateur actuellement authentifié.
 *
 * @return array Retourne les données de l'utilisateur authentifié et la vue de profil.
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
 * Traite la soumission du formulaire de mise à jour du profil de l'utilisateur.
 * Met à jour les informations de l'utilisateur dans la base de données si elles sont modifiées.
 *
 * @return array Retourne les données mises à jour de l'utilisateur et redirige vers la vue de profil.
 */
function edit() : array
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
 * Prépare la vue de connexion pour l'utilisateur.
 *
 * @return array Retourne les données nécessaires et la vue de connexion.
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
 * Traite la tentative de connexion de l'utilisateur.
 * Vérifie les identifiants, met à jour le token de l'utilisateur en cas de succès, et redirige vers le profil.
 *
 * @return array Retourne les erreurs rencontrées ou redirige l'utilisateur en cas de succès.
 * @throws Exception En cas d'échec de génération de tokens aléatoires.
 */
function login_attempt(): array
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
		}
		else
		{
			header('Location: /user/login');
			log_session("Aucun utilisateur n'existe pour cette adresse e-mail.");
		}
	}
	else
	{
		header('Location: /user/login');
		log_session($error);
	}

	return [
		'data' => $result,
		'view' => "user/auth/login",
	];
}

/**
 * Déconnecte l'utilisateur.
 * Efface le token de l'utilisateur de la session et de la base de données, puis redirige.
 *
 * @return array Retourne les données nécessaires pour la vue après déconnexion.
 */
function logout(): array
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
 * Prépare la vue d'enregistrement pour un nouvel utilisateur.
 *
 * @return array Retourne les données nécessaires et la vue d'enregistrement.
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
 * Traite la tentative d'enregistrement d'un nouvel utilisateur.
 * Vérifie la validité des données soumises, crée le nouvel utilisateur si possible, et redirige vers le profil.
 *
 * @return array Retourne les erreurs rencontrées ou redirige l'utilisateur en cas de succès.
 * @throws Exception En cas d'échec de génération de tokens aléatoires.
 */
function register_attempt(): array
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
		}
	}
	else
	{
		header('Location: /user/register');
		log_session($error);
	}

	return [
		'data' => $result,
		'view' => "user/auth/register",
	];
}
