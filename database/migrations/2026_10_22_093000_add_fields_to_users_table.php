<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')
                ->index()
                ->after('password');
            $table->string('avatar')->nullable()->after('role');
            $table->string('display_name')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'avatar', 'display_name', 'is_active']);
        });
    }
};
