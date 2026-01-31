<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 2); // "10", "11", "12"
            $table->string('class_name'); // "Gotong Royong", "Medical Science A"
            $table->integer('academic_year'); // 2024
            $table->timestamps();
            
            // Tidak boleh ada kelas dengan kombinasi sama
            $table->unique(['grade', 'class_name', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};