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
            $table->unsignedBigInteger('roles_id')->after('email');
            $table->foreign('roles_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles_id');
        });
    }

//     public function down()
// {
//     Schema::table('users', function (Blueprint $table) {
//         if (Schema::hasTable('users')) { // Cek apakah tabel ada
//             if (Schema::hasColumn('users', 'roles_id')) { // Cek apakah kolom ada
//                 $table->dropForeign(['roles_id']); // Hapus foreign key jika ada
//                 $table->dropColumn('roles_id'); // Hapus kolom jika ada
//             }
//         }
//     });
// }
};
