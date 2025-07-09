<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('nama')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['student', 'admin'])->default('student');
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');
            $table->string('deskripsi')->nullable();
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable();
            $table->foreignId('major_id')->nullable()->constrained('majors', 'major_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semesters', 'semester_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('faculty_id')->nullable()->constrained('faculties', 'faculty_id')->cascadeOnUpdate()->cascadeOnDelete();
            // $table->string('matkul_favorit')->nullable();
            $table->foreignId('matkul_favorit')->nullable()->constrained('courses', 'course_id')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('foto_profil')->nullable();
            // $table->decimal('rating', 2, 1)->default(0);
            // $table->timestamp('email_verified_at')->nullable();
            // $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
