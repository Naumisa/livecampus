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
	$data['user_storage_path'] = $user->storage_path();

	if (!empty($data['files']) > 0) {
		$disk_space = 0;

		foreach ($data['files'] as $file)
		{
			$disk_space += filesize($file->data['path'] . $file->name_random);
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

    # Update user's datas if modified
    if(
        $user->email !== $new_email ||
        $user->username !== $new_username
    ){
        $user->email = $new_email;
        $user->username = $new_username;
        $user->save();
    };
    $data['user'] = $user;

    # Go back to profile page
    return [
        'data' => $data,
        'view' => 'user/profile'
    ];
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
			$error .= "Le champ $field doit être rempli. ";
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

			$result['error'] = "Le mot de passe ne correspond pas.";
		}
		else
		{
			$result['error'] = "Aucun utilisateur n'existe pour cette adresse e-mail.";
		}
	}
	else
	{
		$result['error'] = [ 'error' => $error ];
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

	$data = [];

	return [
		'data' => $data,
		'view' => "user/auth/logout",
	];
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
			$error .= "Le champ $field doit être rempli. ";
		}
	}

	$isConform = $data['user_password'] === $data['user_password_confirmation'];
	if (!$isConform)
	{
		$error .= "Le champ confirmation ne correspond pas au mot de passe entré.";
		$isValid = false;
	}

	$result = [];
	if ($isValid) {
		$findUser = new UserModel;
		$findUser = $findUser->first('email', $data['user_email']);

		if ($findUser == null)
		{
			$user = new UserModel;

			$newData = [];
			foreach ($data as $key => $value)
			{
				$newData[str_replace('user_', '', $key)] = $value;
			}

			$user->create($newData);

			$user->generate_token();
			$user->save();

			header('Location: /user/profile');

			$_SESSION['token'] = $user->remember_token;
			$_SESSION['email'] = $user->email;

			die();
		}
		else
		{
			$result[] = [ 'error' => "Un autre utilisateur existe déjà pour cette adresse e-mail." ];
		}
	}
	else
	{
		$result[] = [ 'error' => $error ];
	}

	return [
		'data' => $result,
		'view' => "user/auth/register",
	];
}
