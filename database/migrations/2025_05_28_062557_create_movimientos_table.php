<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable(); // Relación con usuarios
            $table->string('concepto')->default('Cierre diario')->nullable();

            // Campos de INGRESOS
            $table->decimal('efectivo', 10, 2)->default(0);
            $table->decimal('tarjeta', 10, 2)->default(0);
            $table->decimal('caldes', 10, 2)->default(0); // Vales
            $table->decimal('pagos_clientes', 10, 2)->default(0);
            $table->decimal('venta_transferencia', 10, 2)->default(0);
            $table->decimal('otros', 10, 2)->default(0);
            $table->string('otros_descripcion')->nullable();

            // Campos de EGRESOS
            $table->decimal('egreso', 10, 2)->default(0);
            $table->string('egreso_tipo')->nullable();
            $table->string('egreso_descripcion')->nullable();
            $table->string('egreso_nota')->nullable();
            $table->string('credito_origen')->nullable();
            $table->string('credito_otro_banco')->nullable();
            $table->string('banco_personalizado')->nullable();
            $table->string('proveedor_nombre')->nullable();
            $table->date('egreso_vencimiento')->nullable();
            $table->boolean('pagado')->default(false);

            // Relación con usuarios (puedes agregar foreign si quieres)
            // $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
