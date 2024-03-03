<?php

namespace app\Models;

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
	 * @param string $name_origin
	 * @param string $name_random
	 * @param int $folder_id
	 * @return array
	 */
	function fill(string $name_origin, string $name_random, int $folder_id): array
	{
		return [
            'name_origin' => $name_origin,
            'name_random'  => $name_random,
			'folder_id' => $folder_id,
        ];
	}

	public function owner(): Model
	{
		return UserModel::find($this->owner_id);
	}

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
	 * @return Model Returns the FolderModel of the user's files.
	 */
	function folder(): Model
	{
		return FolderModel::find($this->folder_id);
	}

	function path(): string
	{
		global $root;
		return $root . app_get_path('public_storage') . "uploads/" . $this->folder()->name_random . "/$this->name_random";
	}
}
