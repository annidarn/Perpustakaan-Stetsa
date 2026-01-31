<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nis', 20)->unique()->nullable();
            $table->string('nip', 20)->nullable(); // ← TAMBAH INI
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->integer('enrollment_year')->nullable(); // ← TAMBAH INI
            $table->string('phone', 15)->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->enum('type', ['student', 'teacher', 'staff'])->default('student');
            $table->enum('status', ['active', 'inactive', 'graduated'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};