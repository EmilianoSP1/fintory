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
    Schema::table('movimientos', function (Blueprint $table) {
        $table->unsignedBigInteger('tienda_id')->default(1);
        $table->foreign('tienda_id')->references('id')->on('tiendas');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            //
        });
    }
};
