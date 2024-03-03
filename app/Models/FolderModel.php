<?php

namespace app\Models;

class FolderModel extends Model
{
	protected static string $table = 'folders';

	protected static array $fields = [
		'id' => [
			'type' => 'int',
			'required' => false,
			'unique' => true,
			'query' => 'INT AUTO_INCREMENT PRIMARY KEY',
		],
		'name_origin' => [
			'type' => 'string',
			'required' => true,
			'unique' => false,
			'query' => 'VARCHAR(255) NOT NULL',
		],
		'name_random' => [
			'type' => 'string',
			'required' => true,
			'unique' => true,
			'query' => 'VARCHAR(255) NOT NULL UNIQUE',
		],
		'owner_id' => [
			'type' => 'int',
			'required' => false,
			'unique' => false,
			'query' => 'INT NOT NULL',
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

	protected static array $foreign_fields = [
		'owner_id' => [
			'refer_to' => 'users(id)',
		],
		'id' => [
			'referred_by' => [
				'table' => 'users',
				'column' => 'default_folder_id',
			],
		]
	];

	public string $name_origin = '';
	public string $name_random = '';
	public int $owner_id = 0;

	/**
	 * Prépare un tableau avec les données d'un utilisateur pour insertion ou mise à jour,
	 * en incluant le hachage du mot de passe.
	 * @param string $name_origin
	 * @param UserModel $user
	 * @return array Le tableau des données de l'utilisateur.
	 */
	function fill(string $name_origin, UserModel $user): array
	{
		return [
			'name_origin' => $name_origin,
			'owner_id' => $user->id,
		];
	}

	public static function create(array $data): ?Model
	{
		$user = $data['user'];
		$name_origin = $data['name_origin'];
		$data['name_random'] = md5("$user->id:$user->email:$name_origin");

		global $root;
		$targetDir = $root . app_get_path('public_storage') . "/uploads/" . $data['name_random'];
		if (!file_exists($targetDir))
		{
			mkdir($targetDir, 0777, true);
		}

		$data['owner_id'] = $user->id;

		unset($data['user']);

		return parent::create($data);
	}
}
