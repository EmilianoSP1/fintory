<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Movimientos registrados</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #bbb; padding: 4px 6px; text-align: left; }
        th { background: #e3e3e3; }
        tr:nth-child(even) { background: #f8f8f8; }
        .ingreso { color: #198754; font-weight: bold; }
        .egreso { color: #dc3545; font-weight: bold; }
        h2 { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Reporte de movimientos registrados</h2>
    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Día</th>
                <th>Fecha</th>
                <th>Otros</th>
                <th>Efectivo</th>
                <th>Tarjeta</th>
                <th>Vales</th>
                <th>Pagos</th>
                <th>Transferencia</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Nota/Factura</th>
                <th>Origen/Destino</th>
                <th>Vencimiento</th>
                <th>Hora</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movimientos as $mov)
            <tr>
                <td>{{ $mov->usuario->name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($mov->created_at)->locale('es')->isoFormat('dddd') }}</td>
                <td>{{ \Carbon\Carbon::parse($mov->created_at)->format('Y-m-d') }}</td>
                <td>{{ number_format($mov->otros, 2) }}</td>
                <td>{{ number_format($mov->efectivo, 2) }}</td>
                <td>{{ number_format($mov->tarjeta, 2) }}</td>
                <td>{{ number_format($mov->caldes, 2) }}</td>
                <td>{{ number_format($mov->pagos_clientes, 2) }}</td>
                <td>{{ number_format($mov->venta_transferencia, 2) }}</td>
                <td>{{ $mov->concepto }}</td>
                <td class="{{ $mov->egreso > 0 ? 'egreso' : 'ingreso' }}">
                    {{ $mov->egreso > 0 ? '-' : '+' }}
                    {{ number_format($mov->egreso > 0 ? $mov->egreso : ($mov->efectivo + $mov->tarjeta + $mov->caldes + $mov->pagos_clientes + $mov->venta_transferencia + $mov->otros), 2) }}
                </td>
                <td>{{ $mov->egreso_nota ?? '-' }}</td>
                <td>{{ $mov->credito_origen ?? $mov->banco_personalizado ?? $mov->proveedor_nombre ?? '-' }}</td>
                <td>
                    {{ $mov->egreso_vencimiento ? \Carbon\Carbon::parse($mov->egreso_vencimiento)->format('Y-m-d') : '-' }}
                </td>
                <td>{{ \Carbon\Carbon::parse($mov->created_at)->setTimezone('America/Mexico_City')->format('H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
