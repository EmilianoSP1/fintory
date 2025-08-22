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
    });
}

public function down()
{
    Schema::table('movimientos', function (Blueprint $table) {
        $table->dropColumn([
            'transferencia_destino',
            'transferencia_otro_banco',
            'credito_origen',
            'credito_otro_banco',
            'proveedor_nombre',
            'egreso_vencimiento'
        ]);
    });
}

};
