<?php
// app/Models/Movimiento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimiento extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $fillable = [
        'batch',
        'usuario_id',  
        'concepto',
        'efectivo',
        'tarjeta',
        'caldes',
        'pagos_clientes',
        'venta_transferencia',
        'otros',
        'otros_descripcion',
        'egreso',
        'egreso_tipo',
        'egreso_descripcion',
        'egreso_nota',
        'transferencia_destino', // Si usas estos, recuerda migrarlos
        'transferencia_otro_banco',
        'credito_origen',
        'credito_otro_banco',
        'proveedor_nombre',
        'egreso_vencimiento',
        'pagado',
        'motivo',
        'forma',
        'tienda_id',
    ];

    protected $attributes = [
        'concepto'            => '',
        'efectivo'            => 0,
        'tarjeta'             => 0,
        'caldes'              => 0,
        'pagos_clientes'      => 0,
        'venta_transferencia' => 0,
        'otros'               => 0,
        'egreso'              => 0,
        'pagado'              => false,
    ];

    protected $casts = [
        'efectivo'            => 'float',
        'tarjeta'             => 'float',
        'caldes'              => 'float',
        'pagos_clientes'      => 'float',
        'venta_transferencia' => 'float',
        'otros'               => 'float',
        'egreso'              => 'float',
        'egreso_vencimiento'  => 'date',
        'pagado'              => 'boolean',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    // Relación: un movimiento pertenece a un usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
