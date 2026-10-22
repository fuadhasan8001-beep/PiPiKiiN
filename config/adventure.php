<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Katalog Avatar
    |--------------------------------------------------------------------------
    | Desain orisinal berbasis SVG (bukan aset pihak lain). Nilai avatar
    | disimpan pada kolom `users.avatar` sebagai kunci string.
    */

    'avatars' => [
        'astro' => ['name' => 'Astro', 'color' => '#667eea', 'icon' => 'rocket'],
        'ninja' => ['name' => 'Ninja', 'color' => '#ff6b6b', 'icon' => 'star'],
        'wizard' => ['name' => 'Wizard', 'color' => '#a855f7', 'icon' => 'wand'],
        'pirate' => ['name' => 'Pirate', 'color' => '#f59e0b', 'icon' => 'anchor'],
        'dino' => ['name' => 'Dino', 'color' => '#10b981', 'icon' => 'leaf'],
        'fox' => ['name' => 'Fox', 'color' => '#f97316', 'icon' => 'flame'],
        'robot' => ['name' => 'Robot', 'color' => '#0ea5e9', 'icon' => 'cpu'],
        'bee' => ['name' => 'Bee', 'color' => '#eab308', 'icon' => 'sun'],
        'cat' => ['name' => 'Cat', 'color' => '#ec4899', 'icon' => 'heart'],
        'ghost' => ['name' => 'Ghost', 'color' => '#64748b', 'icon' => 'cloud'],
        'alien' => ['name' => 'Alien', 'color' => '#22c55e', 'icon' => 'sparkle'],
        'knight' => ['name' => 'Knight', 'color' => '#6366f1', 'icon' => 'shield'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Skor & Timer Default
    |--------------------------------------------------------------------------
    */

    'scoring' => [
        'speed_bonus_ratio' => 0.5,   // maksimal 50% dari poin dasar
        'empty_answer_penalty' => 0,
        'late_answer_penalty' => 0,
    ],

    'difficulty_timers' => [
        'easy' => 30,
        'medium' => 20,
        'hard' => 15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Mode Permainan
    |--------------------------------------------------------------------------
    */

    'modes' => [
        'live' => ['name' => 'Live Quiz', 'description' => 'Semua peserta menjawab bersamaan di satu sesi.'],
        'solo' => ['name' => 'Solo Practice', 'description' => 'Kerjakan sendiri, boleh diulang.'],
        'team' => ['name' => 'Team Quiz', 'description' => 'Bermain dalam tim, skor dijumlahkan.'],
        'final' => ['name' => 'Final Challenge', 'description' => 'Kuis gabungan lintas bab dengan batas kelulusan.'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Jenis Soal
    |--------------------------------------------------------------------------
    */

    'question_types' => [
        'multiple_choice' => ['name' => 'Pilihan Ganda', 'has_options' => true],
        'true_false' => ['name' => 'Benar / Salah', 'has_options' => true],
        'short_answer' => ['name' => 'Jawaban Singkat', 'has_options' => false],
        'image' => ['name' => 'Gambar', 'has_options' => true],
        'formula' => ['name' => 'Rumus / Simbol', 'has_options' => false],
    ],

];
