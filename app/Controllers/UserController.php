<?php

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
		$user = user_get_data_with_id((int) $id);

		$data['user'] = $user;
	}

	return [
		'data' => $data,
		'view' => "user/profile",
	];
}

/**
 * Prépare les données pour la vue du tableau de bord de l'utilisateur.
 *
 * @return array Retourne un tableau avec les données et la vue du tableau de bord.
 */
function dashboard(): array
{
	$data = [];

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
    $data['user'] = auth_user();

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
		$data['user']['email'] !== $new_email ||
		$data['user']['username'] !== $new_username
	){
    	$data['user']['email'] = $new_email;
		$data['user']['username'] = $new_username;
    	user_update(
    	    $data['user']['id'],
    	    [
    	        'username' => $new_username,
    	        'email' => $new_email
    	    ]
    	);
	};

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
		$user = user_get_data_with_email($data['user_email']);

		if ($user != null)
		{
			if (password_verify($data['user_password'], $user['password']))
			{
				$token = user_generate_tokens();

				user_update((int) $user['id'], [ 'remember_token' => $token ]);

				header('Location: /user/profile');

				$_SESSION['token'] = $token;
				$_SESSION['email'] = $user['email'];

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
	$newData = [ 'remember_token' => null ];

	$user = auth_user();
	user_update($user['id'], $newData);

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
		$user = user_get_data_with_email($data['user_email']);

		if ($user == null)
		{
			$token = user_generate_tokens();

			$username = explode('@', $data['user_email'])[0];
			$newData = [];
			$newData['username'] = $username;
			foreach ($data as $key => $value)
			{
				$newData[str_replace('user_', '', $key)] = $value;
			}
			$newData['password'] = password_hash($newData['password'], PASSWORD_DEFAULT);
			$newData['rememberToken'] = $token;

			$user = user_create($newData);

			header('Location: /user/profile');

			$_SESSION['token'] = $token;
			$_SESSION['email'] = $user['email'];

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
