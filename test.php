<?php
// $pw = password_hash('admin123', PASSWORD_DEFAULT);  // Ganti 'password123' dengan password Anda
// echo "/ $pw /";

$roles = [5, 10, 12, 13];

function randomUsername() {
    $adjectives = ["cool", "fast", "silent", "dark", "bright", "mighty", "crazy", "happy", "smart"];
    $nouns = ["lion", "tiger", "wolf", "eagle", "dragon", "fox", "panda", "otter", "hawk"];

    $adj = $adjectives[array_rand($adjectives)];
    $noun = $nouns[array_rand($nouns)];
    $num = rand(10, 9999);

    return $adj . $noun . $num;
}

function randomPassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $pass;
}

// Nama file CSV
$filename = 'users.csv';

// Buka file untuk ditulis
$fp = fopen($filename, 'w');

// Tulis header
fputcsv($fp, ['id_role', 'username', 'email', 'password'], '|');

// Generate 100 baris
for ($i = 0; $i < 100; $i++) {
    $id_role = $roles[array_rand($roles)];
    $username = randomUsername();
    $domains = ["mail.com", "gmail.com", "example.net", "test.org"];
    $domain = $domains[array_rand($domains)];
    $email = strtolower($username) . '@' . $domain;

    $plainPassword = randomPassword();
    $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

    fputcsv($fp, [$id_role, $username, $email, $hashedPassword], '|');
}

// Tutup file
fclose($fp);

echo "CSV berhasil dibuat: $filename\n";
