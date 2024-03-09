<?php

declare(strict_types=1);

return [
    'pdo' => [
        'driver' => 'mysql',
        'host' => 'localhost',
        'db_name' => 'bug_app',
        'db_username' => 'bug',
        'db_user_password' => 'bug',
        'default_fetch' => PDO::FETCH_OBJ
    ],
    'mysqli' => [
        'host' => 'localhost',
        'db_name' => 'bug_app',
        'db_username' => 'bug',
        'db_user_password' => 'bug',
        'default_fetch' => MYSQLI_ASSOC
    ]
];