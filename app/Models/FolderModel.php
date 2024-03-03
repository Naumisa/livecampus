<?php

namespace app\Models;

/**
 * Modèle pour les dossiers stockés dans l'application.
 * Cette classe gère les dossiers utilisés pour organiser les fichiers. Elle permet de créer, retrouver,
 * et manipuler les informations relatives aux dossiers dans la base de données. Chaque dossier est unique
 * et appartient à un utilisateur spécifique, identifié par `owner_id`.
 */
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
	 * Prépare et retourne un tableau de données pour la création ou la mise à jour d'un dossier,
	 * basé sur le nom original du dossier et l'utilisateur propriétaire.
	 *
	 * @param string $name_origin Le nom original du dossier fourni par l'utilisateur.
	 * @param UserModel $user L'instance du modèle de l'utilisateur propriétaire du dossier.
	 * @return array Un tableau associatif contenant les données préparées du dossier.
	 */
	function fill(string $name_origin, UserModel $user): array
	{
		return [
			'name_origin' => $name_origin,
			'owner_id' => $user->id,
		];
	}

	/**
	 * Crée un nouveau dossier dans la base de données avec un nom aléatoire unique,
	 * en se basant sur les données fournies. Le dossier est physiquement créé sur le système de fichiers
	 * dans le répertoire de stockage public. Le nom aléatoire est généré à partir de l'identifiant de l'utilisateur,
	 * de son adresse email, et du nom original du dossier.
	 *
	 * @param array $data Un tableau associatif contenant les données nécessaires à la création du dossier,
	 *                    incluant le nom original et l'utilisateur propriétaire.
	 * @return Model|null Retourne une instance du modèle `FolderModel` représentant le dossier créé,
	 *                     ou null en cas d'échec.
	 */
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
