<?php

/**
 * @return array
 */
function profile(): array
{
	$data = [ 'user' => user_get_actual() ];

    return [
        'data' => $data,
        'view' => "user/profile",
    ];
}
