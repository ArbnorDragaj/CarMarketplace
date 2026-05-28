<?php


return [
    'enabled' => true,

    // Email ku do te pranohen mesazhet nga forma Contact
    'to_email' => 'carmarketplace@gmail.com',
    'to_name' => 'CarMarketplace Team',

    // Email nga i cili dergohet njoftimi
    'from_email' => 'no-reply@carmarketplace.local',
    'from_name' => 'CarMarketplace Website',

    'smtp' => [
        'enabled' => false,
        'host' => 'smtp.gmail.com',
        'username' => '',
        'password' => '',
        'port' => 587,
        'encryption' => 'tls'
    ]
];