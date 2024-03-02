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
		'name_origine' => [
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
	];

	public string $name_origine = '';
	public string $name_random = '';
	public int $download_count = 0;
	public int $owner_id = 0;

	public function owner(): Model
	{
		return UserModel::find($this->owner_id);
	}

	/**
	 * @param string $name_origine
	 * @param string $name_random
	 * @return array
	 */
	function fill(string $name_origine, string $name_random): array
	{
		return [
            'name_origine' => $name_origine,
            'name_random'  => $name_random
        ];
	}
}
