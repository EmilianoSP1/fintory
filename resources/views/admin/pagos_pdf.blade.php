<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Pagos</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Reporte de Pagos a Empleados</h2>
    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Concepto</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Método</th>
                <th>Descripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagos as $pago)
                <tr>
                    <td>{{ $pago->empleado->name ?? '-' }}</td>
                    <td>{{ $pago->concepto }}</td>
                    <td>${{ number_format($pago->monto, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $pago->metodo }}</td>
                    <td>{{ $pago->descripcion }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
