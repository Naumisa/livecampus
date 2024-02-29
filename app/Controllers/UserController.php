<?php

/**
 * @return array
 */
function index(): array
{
	$data = [ 'user' => auth_user() ];

	return [
		'data' => $data,
		'view' => "user/profile",
	];
}

/**
 * @return array
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
 * @return array
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
 * @return array
 * @throws \Random\RandomException
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
			$token = user_generate_tokens();

			user_update((int) $user[0]['id'], [ 'remember_token' => $token ]);

			header('Location: /user/profile');

			$_SESSION['token'] = $token;
			$_SESSION['email'] = $user[0]['email'];

			die();
		}
		else
		{
			$result[] = [ 'error' => "Aucun utilisateur n'existe pour cette adresse e-mail." ];
		}
	}
	else
	{
		$result[] = [ 'error' => $error ];
	}

	return [
		'data' => $result,
		'view' => "user/auth/login",
	];
}

/**
 * @return array
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
 * @return array
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
 * @return array
 * @throws \Random\RandomException
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
			$username = explode('@', $data['user_email'])[0];
			$newData = [];
			$newData['username'] = $username;
			foreach ($data as $key => $value)
			{
				$newData[str_replace('user_', '', $key)] = $value;
			}
			$newData['password'] = password_hash($newData['password'], PASSWORD_DEFAULT);

			$user = user_create($newData);

			$token = user_generate_tokens();

			user_update((int) $user[0]['id'], [ 'remember_token' => $token ]);

			header('Location: /user/profile');

			$_SESSION['token'] = $token;

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

function dashboard(): array
{
	$data = [];

	return [
		'data' => $data,
		'view' => "user/auth/dashboard",
	];
}
