<?php

namespace app\Models;

use Random\RandomException;

/**
 * Représente le modèle de données pour les utilisateurs dans l'application.
 * Cette classe étend le modèle de base et inclut des méthodes spécifiques pour la gestion des utilisateurs,
 * y compris la création d'utilisateurs, la génération de tokens d'authentification, et la récupération des dossiers
 * et fichiers associés à un utilisateur.
 */
class UserModel extends Model
{
	protected static string $table = 'users';

	protected static array $fields = [
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
		'default_folder_id' => [
			'type' => 'int',
			'required' => false,
			'unique' => true,
			'query' => 'INT',
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

	public string $username = '';
	public string $email = '';
	public string $password = '';
	public int $default_folder_id = 0;
	public ?string $remember_token = '';
	public int $role = 0;

	/**
	 * Crée une nouvelle instance de `UserModel` dans la base de données avec les données fournies.
	 * Génère automatiquement un nom d'utilisateur à partir de l'email, hache le mot de passe, et crée un dossier
	 * par défaut pour l'utilisateur.
	 *
	 * @param array $data Données pour créer un nouvel utilisateur.
	 * @return UserModel|null Retourne une instance de `UserModel` si la création est réussie, sinon null.
	 */
	public static function create(array $data): ?Model
	{
		$data['username'] = explode('@', $data['email'])[0];
		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		$data['default_folder_id'] = 0;

		unset($data['password_confirmation']);

		static::$db->set_foreign_key_check(false);

		$user = parent::create($data);

		$folder = FolderModel::create(['name_origin' => '/', 'user' => $user]);
		$user->default_folder_id = $folder->id;
		$user->save();

		static::$db->set_foreign_key_check(true);
		return $user;
	}

	/**
	 * Prépare un tableau avec les données d'un utilisateur pour l'insertion ou la mise à jour,
	 * incluant le hachage du mot de passe.
	 *
	 * @param string $username Le nom d'utilisateur.
	 * @param string $email L'adresse email de l'utilisateur.
	 * @param string $password Le mot de passe de l'utilisateur (sera haché).
	 * @param int $role Le rôle de l'utilisateur dans l'application.
	 * @return array Un tableau associatif contenant les données de l'utilisateur.
	 */
	function fill(string $username, string $email, string $password, int $role): array
	{
		return [
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'role' => $role,
		];
	}

	/**
	 * Génère un token d'authentification pour l'utilisateur, composé d'un sélecteur et d'un validateur.
	 * Ce token peut être utilisé pour des fonctionnalités telles que la réinitialisation du mot de passe ou la
	 * persistance de la session utilisateur.
	 *
	 * @return void Attribue le token généré à la propriété `remember_token` de l'utilisateur.
	 * @throws RandomException Si la génération de bytes aléatoires échoue.
	 */
	function generate_token(): void
	{
		$selector = bin2hex(random_bytes(16));
		$validator = bin2hex(random_bytes(32));

		$this->remember_token = $selector . ':' . $validator;
	}

	/**
	 * Récupère le dossier par défaut associé à l'utilisateur.
	 *
	 * @return FolderModel Retourne une instance de `FolderModel` représentant le dossier par défaut de l'utilisateur.
	 */
	function folder(): Model
	{
		return FolderModel::find($this->default_folder_id);
	}

	/**
	 * Récupère tous les dossiers appartenant à l'utilisateur.
	 *
	 * @return array Un tableau d'instances de `FolderModel` représentant les dossiers de l'utilisateur.
	 */
	function folders(): array
	{
		return FolderModel::where('owner_id', $this->default_folder_id);
	}

	/**
	 * Récupère tous les fichiers appartenant à l'utilisateur.
	 *
	 * @return array|null Un tableau d'instances de `FileModel` représentant les fichiers de l'utilisateur,
	 *                    ou null si aucun fichier n'est trouvé.
	 */
	function files(): ?array
	{
		$files = FileModel::where('owner_id', $this->id);

		if (!empty($files) > 0) {
			foreach ($files as $file)
			{
				$file->data['path'] = $this->folder();
			}

			return $files;
		}

		return null;
	}

	/**
	 * Récupère tous les fichiers partagés avec l'utilisateur par d'autres utilisateurs.
	 *
	 * @return array|null Un tableau d'instances de `FileModel` représentant les fichiers partagés avec l'utilisateur,
	 *                    ou null si aucun fichier partagé n'est trouvé.
	 */
	function sharedFiles(): ?array
	{
		$filesUsers = FileUserModel::where('user_id', auth_user()->id);
		$files = [];

		if (!empty($filesUsers) > 0) {
			foreach ($filesUsers as $fileUser)
			{
				$file_id = $fileUser->file_id;
				$file = FileModel::find($file_id);
				$owner_id = $file->owner_id;
				$owner = UserModel::find($owner_id);
				$file->data['path'] = $owner->folder();
				$file->data['owner_email'] = $owner->email;
				$files[] = $file;
			}

			return $files;
		}

		return null;
	}
}
