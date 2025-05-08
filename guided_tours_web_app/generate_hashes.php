<?php
// Simple script to generate password hashes for seed data
// Use the same password for all for simplicity in this example
$password = 'password123';

$users = [
    'config_admin' => 'hashed_password_config1',
    'config_manager' => 'hashed_password_config2',
    'volunteer_anna' => 'hashed_password_vol1',
    'volunteer_marco' => 'hashed_password_vol2',
    'user_paolo' => 'hashed_password_user1',
    'user_elena' => 'hashed_password_user2',
    'user_luca' => 'hashed_password_user3',
];

echo "-- Generated Hashes for 'password123':\n";
foreach ($users as $username => $placeholder) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    echo "-- User: " . $username . "\n";
    echo "-- Placeholder: '" . $placeholder . "'\n";
    echo "-- Generated Hash: '" . $hash . "'\n\n";
}

?>
