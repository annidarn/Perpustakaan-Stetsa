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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        // Otomatis isi username: Gunakan NIS/NIP jika ada, jika tidak gunakan prefix email
        \App\Models\User::with('member')->get()->each(function ($user) {
            if (empty($user->username)) {
                $username = null;
                
                if ($user->member) {
                    $username = $user->member->nis ?: $user->member->nip;
                }
                
                if (empty($username)) {
                    $username = explode('@', $user->email)[0];
                }

                $user->username = $username;
                $user->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
