<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // --- EGRESO: Transferencia ---
            if (!Schema::hasColumn('movimientos', 'transferencia_destino')) {
                $table->string('transferencia_destino')->nullable()->after('egreso_nota');
            }
            if (!Schema::hasColumn('movimientos', 'transferencia_otro_banco')) {
                $table->string('transferencia_otro_banco')->nullable()->after('transferencia_destino');
            }

            // --- EGRESO: Crédito ---
            if (!Schema::hasColumn('movimientos', 'credito_origen')) {
                $table->string('credito_origen', 50)->nullable()->after('transferencia_otro_banco');
            }
            if (!Schema::hasColumn('movimientos', 'credito_otro_banco')) {
                $table->string('credito_otro_banco')->nullable()->after('credito_origen');
            }
            if (!Schema::hasColumn('movimientos', 'proveedor_nombre')) {
                $table->string('proveedor_nombre')->nullable()->after('credito_otro_banco');
            }
            if (!Schema::hasColumn('movimientos', 'egreso_vencimiento')) {
                $table->date('egreso_vencimiento')->nullable()->after('proveedor_nombre');
            }

            // Generales (por si faltan en tu esquema)
            if (!Schema::hasColumn('movimientos', 'egreso_tipo')) {
                $table->string('egreso_tipo', 30)->nullable()->after('otros');
            }
            if (!Schema::hasColumn('movimientos', 'pagado')) {
                $table->boolean('pagado')->default(false)->after('egreso_tipo');
            }
            if (!Schema::hasColumn('movimientos', 'motivo')) {
                $table->string('motivo')->nullable()->after('pagado');
            }
            if (!Schema::hasColumn('movimientos', 'forma')) {
                $table->string('forma')->nullable()->after('motivo');
            }
            if (!Schema::hasColumn('movimientos', 'tienda_id')) {
                $table->unsignedBigInteger('tienda_id')->nullable()->after('usuario_id');
                // Si tienes tabla 'tiendas', activá la FK:
                if (Schema::hasTable('tiendas')) {
                    $table->foreign('tienda_id')->references('id')->on('tiendas')->nullOnDelete();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            foreach ([
                'transferencia_destino',
                'transferencia_otro_banco',
                'credito_origen',
                'credito_otro_banco',
                'proveedor_nombre',
                'egreso_vencimiento',
                'egreso_tipo',
                'pagado',
                'motivo',
                'forma',
                // Para revertir tienda_id con FK segura:
                'tienda_id',
            ] as $col) {
                if (Schema::hasColumn('movimientos', $col)) {
                    // Quitar FK si existiera
                    if ($col === 'tienda_id') {
                        try { $table->dropForeign(['tienda_id']); } catch (\Throwable $e) {}
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};
