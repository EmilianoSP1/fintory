<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Agregar columna solo si NO existe
        if (! Schema::hasColumn('movimientos', 'tienda_id')) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->unsignedBigInteger('tienda_id')->default(1);
            });
        }

        // 2) Asegurar índice
        $hasIndex = collect(DB::select("SHOW INDEX FROM movimientos WHERE Column_name = 'tienda_id'"))->isNotEmpty();
        if (! $hasIndex) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->index('tienda_id');
            });
        }

        // 3) Asegurar FK a tiendas.id (solo si no existe)
        $hasFk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'movimientos'
              AND COLUMN_NAME = 'tienda_id'
              AND REFERENCED_TABLE_NAME = 'tiendas'
            LIMIT 1
        ");

        if (! $hasFk) {
            Schema::table('movimientos', function (Blueprint $table) {
                $table->foreign('tienda_id')->references('id')->on('tiendas');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('movimientos', 'tienda_id')) {
            Schema::table('movimientos', function (Blueprint $table) {
                try { $table->dropForeign(['tienda_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex(['tienda_id']); } catch (\Throwable $e) {}
                $table->dropColumn('tienda_id');
            });
        }
    }
};
