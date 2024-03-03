<?php

namespace app\Models;

/**
 * Modèle représentant un fichier dans l'application.
 * Cette classe gère les interactions avec la base de données pour les fichiers, incluant la création,
 * la recherche, et la manipulation des informations de fichier. Elle utilise une structure définie
 * pour représenter les champs du fichier dans la base de données.
 */
class FileModel extends Model
{
	protected static string $table = 'files';

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
		'type' => [
			'type' => 'string',
			'required' => true,
			'unique' => false,
			'query' => 'VARCHAR(255) NOT NULL',
		],
		'download_count' => [
			'type' => 'int',
			'required' => false,
			'unique' => false,
			'query' => 'INT DEFAULT 0',
		],
		'owner_id' => [
			'type' => 'int',
			'required' => false,
			'unique' => false,
			'query' => 'INT NOT NULL',
		],
		'folder_id' => [
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
		'folder_id' => [
			'refer_to' => 'folders(id)',
		],
	];

	public string $name_origin = '';
	public string $name_random = '';
	public string $type = '';
	public int $download_count = 0;
	public int $owner_id = 0;
	public int $folder_id = 0;

	/**
	 * Prépare un tableau de données de fichier avec les noms d'origine et aléatoires, ainsi que l'ID du dossier.
	 *
	 * @param string $name_origin Nom d'origine du fichier tel que téléchargé par l'utilisateur.
	 * @param string $name_random Nom aléatoire généré pour stocker le fichier de manière unique.
	 * @param int $folder_id Identifiant du dossier où le fichier est stocké.
	 * @return array Retourne un tableau associatif avec les données préparées pour l'enregistrement ou la mise à jour.
	 */
	function fill(string $name_origin, string $name_random, int $folder_id): array
	{
		return [
            'name_origin' => $name_origin,
            'name_random'  => $name_random,
			'folder_id' => $folder_id,
        ];
	}

	/**
	 * Récupère et retourne le modèle de l'utilisateur propriétaire de ce fichier.
	 *
	 * @return Model Instance du modèle `UserModel` représentant le propriétaire du fichier.
	 */
	public function owner(): Model
	{
		return UserModel::find($this->owner_id);
	}

	/**
	 * Récupère et retourne tous les utilisateurs ayant accès à ce fichier, incluant le propriétaire.
	 *
	 * @return array Un tableau associatif des utilisateurs ayant accès au fichier, indexé par leur ID.
	 */
	function users(): array
	{
		$shared = FileUserModel::where('file_id', $this->id);
		$data = [];

		if (!empty($shared)) {
			foreach ($shared as $file_user)
			{
				$user = UserModel::find($file_user->user_id);
				$data[$user->id] = $user;
			}
		}

		$data[$this->owner()->id] = $this->owner();

		return $data;
	}

	/**
	 * Récupère et retourne le modèle du dossier contenant ce fichier.
	 *
	 * @return Model Instance du modèle `FolderModel` représentant le dossier contenant le fichier.
	 */
	function folder(): Model
	{
		return FolderModel::find($this->folder_id);
	}

	/**
	 * Construit et retourne le chemin d'accès complet du fichier sur le système de fichiers.
	 *
	 * @return string Le chemin d'accès complet du fichier, incluant le chemin de base, le chemin du dossier,
	 *                et le nom de fichier aléatoire.
	 */
	function path(): string
	{
		global $root;
		return $root . app_get_path('public_storage') . "uploads/" . $this->folder()->name_random . "/$this->name_random";
	}
}
