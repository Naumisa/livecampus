<?php

/**
 * @return array
 */
function index(): array
{
    $data = [];

    return [
        'data' => $data,
        'view' => "user/auth/login/index",
    ];
}

/**
 * @return array
 * @throws \Random\RandomException
 */
function login(): array
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
			$result[] = [ 'user' => $user ];

			$token = user_generate_tokens();

			user_update((int) $user[0]['id'], [ 'remember_token' => $token ]);
			$_SESSION['token'] = $token;

			header('Location: /user/profile');
			return [
				'data' => $result,
				'view' => "user/auth/login/confirm",
			];
		}
		else
		{
			$result[] = [ 'error' => "Aucun utilisateur n'existe pour cette adresse e-mail." ];
		}
	}
	else
	{
		$result[] = [ 'error' => "Les champs fournis ne sont pas valides." ];
	}

	return [
		'data' => $result,
		'view' => "user/auth/login/index",
	];
}
