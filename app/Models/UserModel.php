<?php

namespace app\Models;

use back\Models\DatabaseModel;
use Random\RandomException;

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
	 * Prépare un tableau avec les données d'un utilisateur pour insertion ou mise à jour,
	 * en incluant le hachage du mot de passe.
	 * @param string $username Le nom d'utilisateur.
	 * @param string $email L'email de l'utilisateur.
	 * @param string $password Le mot de passe de l'utilisateur (sera haché).
	 * @param int $role Le rôle de l'utilisateur.
	 * @return array Le tableau des données de l'utilisateur.
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
	 * Génère un sélecteur et un validateur pour un token.
	 * Utile pour les fonctionnalités d'authentification ou de réinitialisation de mot de passe.
	 * @return void Attribue un token généré sous forme 'sélecteur:validateur'.
	 * @throws RandomException
	 */
	function generate_token(): void
	{
		$selector = bin2hex(random_bytes(16));
		$validator = bin2hex(random_bytes(32));

		$this->remember_token = $selector . ':' . $validator;
	}

	/**
	 * @return Model Returns the FolderModel of the user's files.
	 */
	function folder(): Model
	{
		return FolderModel::find($this->default_folder_id);
	}

	/**
	 * @return array Returns all the user's folders.
	 */
	function folders(): array
	{
		return FolderModel::where('owner_id', $this->default_folder_id);
	}

	/**
	 * @return array|null Returns the informations of the user's files.
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
