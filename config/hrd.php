<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Interview HRD - Youth Ranger Indonesia
|--------------------------------------------------------------------------
| Ubah kuota TOP per divisi atau daftar regional di sini.
| Kuota mengikuti file lama top_divisi.php.
*/

return [

    'regionals' => [
        'Sumatera Selatan',
        'Lampung',
        'Jambi',
        'Bengkulu',
        'Bangka Belitung',
    ],

    // Login tim HR (satu akun). Isi lewat .env: HRD_USERNAME dan HRD_PASSWORD
    // Kosongkan HRD_PASSWORD hanya untuk pemakaian lokal tanpa login.
    'auth' => [
        'username' => env('HRD_USERNAME', 'hrd'),
        'password' => env('HRD_PASSWORD'),
    ],

    'max_score' => 25,

    'divisi' => [
        'Graphic Design'          => ['kuota' => 4, 'icon' => 'pen-tool', 'color' => '#ede9fe'],
        'Content Creator'         => ['kuota' => 3, 'icon' => 'video', 'color' => '#e0e7ff'],
        'Finance'                 => ['kuota' => 4, 'icon' => 'dollar', 'color' => '#fce7f3'],
        'Project Management'      => ['kuota' => 7, 'icon' => 'compass', 'color' => '#dbeafe'],
        'Human Resource'          => ['kuota' => 7, 'icon' => 'users', 'color' => '#f3e8ff'],
        'Public Relation'         => ['kuota' => 7, 'icon' => 'radio', 'color' => '#e0f2fe'],
        'Secretary'               => ['kuota' => 5, 'icon' => 'clipboard', 'color' => '#fae8ff'],
        'Vice Leader'             => ['kuota' => 5, 'icon' => 'shield', 'color' => '#ede9fe'],
        'Leader'                  => ['kuota' => 5, 'icon' => 'award', 'color' => '#e9d5ff'],
        'Social Media Management' => ['kuota' => 4, 'icon' => 'smartphone', 'color' => '#ddd6fe'],
    ],

];
