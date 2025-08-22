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
    Schema::table('pagos', function (Blueprint $table) {
        $table->unsignedBigInteger('tienda_id')->default(1)->after('id'); // O después del campo que gustes
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::table('pagos', function (Blueprint $table) {
        $table->dropColumn('tienda_id');
    });
}

};
