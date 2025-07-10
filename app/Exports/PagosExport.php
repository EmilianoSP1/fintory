<?php

namespace App\Exports;

use App\Models\Pago;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PagosExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pagos;

    public function __construct($pagos)
    {
        $this->pagos = $pagos;
    }

    public function collection()
    {
        return $this->pagos;
    }

    public function map($pago): array
    {
        return [
            $pago->empleado->name ?? '-',
            $pago->concepto,
            $pago->monto,
            $pago->fecha,
            $pago->metodo,
            $pago->descripcion,
        ];
    }

    public function headings(): array
    {
        return [
            'Empleado',
            'Concepto',
            'Monto',
            'Fecha',
            'Método',
            'Descripción',
        ];
    }
}
