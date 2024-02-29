<?php
function file_user_get_fields(): array
{
    return [
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
}
