<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'empleado_id', 'concepto', 'monto', 'fecha', 'descripcion', 'metodo'
    ];

    public function empleado()
    {
        return $this->belongsTo(\App\Models\Usuario::class, 'empleado_id');
    }
}
