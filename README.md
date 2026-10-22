# ADVENTURE EDU

Platform kuis edukasi multiplayer berbasis Laravel + Livewire.

## Setup
1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. `php artisan key:generate`
5. `php artisan migrate --force`
6. `npm run build`

## Struktur Database
- `users` (profiles dengan role)
- `subjects`
- `quizzes`
- `questions` (dengan options JSON)
- `rooms` (kode unik)
- `room_participants`
- `answers`
- `audit_logs`

## Peran
- `admin`: semua akses
- `teacher`: buat soal dan kuis
- `student`: bermain kuis

## Jalankan Tes
`vendor/bin/pest`
