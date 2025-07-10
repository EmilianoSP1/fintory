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
                <td>
                    @if($mov->egreso > 0)
                        -{{ number_format($mov->egreso, 2) }}
                    @else
                        +{{ number_format(($mov->efectivo + $mov->tarjeta + $mov->caldes + $mov->pagos_clientes + $mov->venta_transferencia + $mov->otros), 2) }}
                    @endif
                </td>
                <td>{{ $mov->egreso_nota ?? '-' }}</td>
                <td>{{ $mov->banco_personalizado ?? $mov->proveedor_nombre ?? '-' }}</td>
                <td>{{ $mov->egreso_vencimiento ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($mov->created_at)->setTimezone('America/Mexico_City')->format('H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
