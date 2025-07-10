<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_pagos_table.php
public function up()
{
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('empleado_id')->constrained('users')->onDelete('cascade');
        $table->string('concepto');
        $table->decimal('monto', 10, 2);
        $table->date('fecha');
        $table->string('metodo')->nullable();
        $table->text('descripcion')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
