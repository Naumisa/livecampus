<?php

namespace app\Models;

class FileUserModel extends Model
{
    protected string $table = 'file_user';

    protected array $fields = [
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
    protected array $foreign_fields = [
        'user_id' => [
            'refer_to' => 'users(id)',
            'on_delete' => 'CASCADE',
        ],
        'file_id' => [
            'refer_to' => 'files(id)',
            'on_delete' => 'CASCADE',
        ],
    ];
}
