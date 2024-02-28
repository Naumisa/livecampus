<?php

function user_get_table (): string
{ return 'users'; }

/**
 * @return array[]
 */
function user_get_fields (): array
{
	return [
		'id' => [
			'type' => 'int',
			'required' => false,
			'unique' => true,
			'query' => 'INT AUTO_INCREMENT PRIMARY KEY',
		],
		'username' => [
			'type' => 'string',
			'required' => false,
			'unique' => false,
			'query' => 'VARCHAR(255) NOT NULL',
		],
		'email' => [
			'type' => 'string',
			'required' => true,
			'unique' => true,
			'query' => 'VARCHAR(255) NOT NULL UNIQUE',
		],
		'password' => [
			'type' => 'string',
			'required' => true,
			'unique' => false,
			'query' => 'VARCHAR(255) NOT NULL',
		],
		'remember_token' => [
			'type' => 'string',
			'required' => false,
			'unique' => false,
			'query' => 'VARCHAR(255)',
		],
		'role' => [
			'type' => 'int',
			'required' => false,
			'unique' => false,
			'query' => 'INT NOT NULL DEFAULT 0',
		],
		'created_at' => [
			'type' => 'timestamp',
			'required' => false,
			'unique' => false,
			'query' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
		],
		'updated_at' => [
			'type' => 'timestamp',
			'required' => false,
			'unique' => false,
			'query' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
		],
	];
}

/**
 * @param string $username
 * @param string $email
 * @param string $password
 * @param int $role
 * @return array
 */
function user_get_data_array(string $username, string $email, string $password, int $role): array
{
	return [
		'username' => $username,
		'email' => $email,
		'password' => password_hash($password, PASSWORD_DEFAULT),
		'role' => $role,
	];
}

/**
 * @return void
 */
function user_migrate(): void
{
	db_create_table(user_get_table(), user_get_fields());
}

/**
 * @param array $data
 * @return array|null
 */
function user_create(array $data): ?array
{
	if (!db_create_model(user_get_table(), user_get_fields(), $data))
	{
		log_file("Attempted to create a duplicate user.");
		return null;
	}
	else
	{
		return user_get_data_with_email($data['email']);
	}
}

/**
 * @param int $id
 * @param array $data
 * @return void
 */
function user_update(int $id, array $data): void
{
	if (!db_update(user_get_table(), $id, $data))
	{
		log_file("Attempted to create a duplicate user.");
	}
}

/**
 * @param int $id
 * @return array
 */
function user_get_data_with_id (int $id): array
{
	return db_fetch_data(user_get_table(), 'id', $id);
}

/**
 * @param string $email
 * @return array
 */
function user_get_data_with_email (string $email): array
{
	return db_fetch_data(user_get_table(), 'email', $email);
}

/**
 * @param string $token
 * @return array
 */
function user_get_data_with_token (string $token): array
{
	return db_fetch_data(user_get_table(), 'remember_token', $token);
}

/**
 * @return array|null
 */
function user_get_actual (): ?array
{
	if (isset($_SESSION['token']))
	{
		return user_get_data_with_token($_SESSION['token'])[0];
	}
	else
	{
		return null;
	}
}

/**
 * @param int $id
 * @param string $password
 * @return bool
 */
function user_confirm_password (int $id, string $password): bool
{
	$user = user_get_actual();
	return password_verify($password, $user['password']);
}

/**
 * @return string
 * @throws \Random\RandomException
 */
function user_generate_tokens(): string
{
	$selector = bin2hex(random_bytes(16));
	$validator = bin2hex(random_bytes(32));

	return $selector . ':' . $validator;
}
