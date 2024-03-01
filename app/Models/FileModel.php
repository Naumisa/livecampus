<?php

namespace app\Models;

class FileModel extends Model
{
	protected string $table = 'files';

	protected array $fields = [
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

	protected array $foreign_fields = [
		'owner_id' => [
			'refer_to' => 'users(id)',
			'on_delete' => 'CASCADE',
		],
	];

	public string $name_origine = '';
	public string $name_random = '';
	public int $download_count = 0;
	public int $owner_id = 0;

	public function owner(): UserModel
	{
		$user = new UserModel;
		return $user->find($this->owner_id);
	}

	public function create(array $data): void
	{
		/* TODO-Hind: Changement pour gestion des fichiers
		$data['username'] = explode('@', $data['email'])[0];
		$data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
		*/

		parent::create($data);
	}

	/**
	 * @param int $id
	 * @param string $name_origine
	 * @param string $name_random
	 * @param int $download_count
	 * @return array
	 */
	function fill(string $name_origine, string $name_random, int $download_count): array
	{
		return [
			'name_origine' => $name_origine,
			'name_random' => $name_random,
			'download_count' => $download_count
		];
	}
}
