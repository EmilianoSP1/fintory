<?php

// database/migrations/2025_07_01_000000_add_usuario_id_to_movimientos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Si ya existe el campo, bórralo primero (o hazlo manual en phpMyAdmin).
            if (! Schema::hasColumn('movimientos', 'usuario_id')) {
                $table->foreignId('usuario_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
    }
};
