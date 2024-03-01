<?php

/**
 * Retourne le nom de la table des utilisateurs.
 * @return string Le nom de la table.
 */
function user_get_table (): string
{ return 'users'; }

/**
 * Définit la structure de la table des utilisateurs, y compris les types de données,
 * les contraintes et les valeurs par défaut des champs.
 * @return array La définition des champs de la table des utilisateurs.
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
 * Prépare un tableau avec les données d'un utilisateur pour insertion ou mise à jour,
 * en incluant le hachage du mot de passe.
 * @param string $username Le nom d'utilisateur.
 * @param string $email L'email de l'utilisateur.
 * @param string $password Le mot de passe de l'utilisateur (sera haché).
 * @param int $role Le rôle de l'utilisateur.
 * @return array Le tableau des données de l'utilisateur.
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
 * Crée ou met à jour la table des utilisateurs dans la base de données selon la
 * structure définie dans user_get_fields.
 * @return void
 */
function user_migrate(): void
{
	db_create_table(user_get_table(), user_get_fields());
}

/**
 * Crée un nouvel utilisateur dans la base de données avec les données fournies.
 * Retourne les données de l'utilisateur créé ou null en cas d'échec.
 * @param array $data Les données de l'utilisateur à créer.
 * @return array|null Les données de l'utilisateur créé ou null en cas d'échec.
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
 * Met à jour les informations d'un utilisateur spécifié par son ID avec les données fournies.
 * @param int $id L'ID de l'utilisateur à mettre à jour.
 * @param array $data Les données à mettre à jour.
 * @return void
 */
function user_update(int $id, array $data): void
{
	if (!db_update(user_get_table(), $id, $data))
	{
		log_file("Error when attempted to update user($id).");
	}
}

/**
 * Récupère les informations d'un utilisateur spécifié par son ID.
 * @param int $id L'ID de l'utilisateur.
 * @return array Les données de l'utilisateur.
 */
function user_get_data_with_id (int $id): array
{
	return db_fetch_data(user_get_table(), 'id', $id)[0];
}

/**
 * Récupère les informations d'un utilisateur spécifié par son email.
 * @param string $email L'email de l'utilisateur.
 * @return array Les données de l'utilisateur.
 */
function user_get_data_with_email (string $email): array
{
	return db_fetch_data(user_get_table(), 'email', $email)[0];
}

/**
 * Vérifie si le mot de passe fourni correspond au mot de passe de l'utilisateur authentifié.
 * @param int $id L'ID de l'utilisateur.
 * @param string $password Le mot de passe à vérifier.
 * @return bool True si le mot de passe correspond, false sinon.
 */
function user_confirm_password (int $id, string $password): bool
{
	$user = auth_user();
	return password_verify($password, $user['password']);
}

/**
 * Génère un sélecteur et un validateur pour un token.
 * Utile pour les fonctionnalités d'authentification ou de réinitialisation de mot de passe.
 * @return string Le token généré sous forme 'sélecteur:validateur'.
 * @throws Exception Si la génération de bytes aléatoires échoue.
 */
function user_generate_tokens(): string
{
	$selector = bin2hex(random_bytes(16));
	$validator = bin2hex(random_bytes(32));

	return $selector . ':' . $validator;
}
