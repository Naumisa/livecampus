<?php

namespace app\Models;

class FileUserModel extends Model
{
    protected static string $table = 'file_user';

    protected static array $fields = [
        'user_id' => [
            'type' => 'int',
            'required' => true,
            'unique' => true,
            'query' => 'INT',
        ],
        'file_id' => [
            'type' => 'int',
            'required' => true,
            'unique' => true,
            'query' => 'INT',
        ],
    ];
    protected static array $foreign_fields = [
        'user_id' => [
            'refer_to' => 'users(id)',
	        'on_delete' => 'cascade',
        ],
        'file_id' => [
            'refer_to' => 'files(id)',
	        'on_delete' => 'cascade',
        ],
    ];

	public int $user_id = 0;
	public int $file_id = 0;
}
