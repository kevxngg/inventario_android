<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Auditoría | SICOT ERP</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --doc-black: #000000;
            --doc-dark-gray: #333333;
            --doc-light-gray: #f2f2f2;
            --doc-accent: #004b87;
        }

        body {
            background-color: #dcdcdc;
            font-family: 'Roboto', sans-serif;
            font-size: 10px;
            color: var(--doc-black);
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Configuración de Hoja A4 Profesional */
        .document-page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #ffffff;
            padding: 15mm;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            position: relative;
        }

        /* Encabezado Estilo ISO */
        .iso-header {
            display: table;
            width: 100%;
            border: 1px solid var(--doc-black);
            margin-bottom: 20px;
        }

        .iso-cell {
            display: table-cell;
            vertical-align: middle;
            border: 1px solid var(--doc-black);
            padding: 10px;
        }

        .iso-logo { width: 25%; text-align: center; }
        .iso-title { width: 45%; text-align: center; text-transform: uppercase; font-weight: 900; font-size: 12px; }
        .iso-meta { width: 30%; font-size: 8px; line-height: 1.4; }

        /* Información de Generación */
        .report-summary {
            background-color: var(--doc-light-gray);
            border: 1px solid #ccc;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .summary-label { font-weight: 700; color: var(--doc-dark-gray); text-transform: uppercase; font-size: 8px; }
        .summary-value { font-weight: 400; color: #000; }

        /* Tabla de Movimientos Técnica */
        .tech-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .tech-table thead th {
            background-color: var(--doc-dark-gray);
            color: #ffffff;
            border: 1px solid var(--doc-black);
            padding: 8px;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        .tech-table tbody td {
            border: 1px solid #999;
            padding: 7px;
            vertical-align: middle;
        }

        .tech-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .id-mono { font-family: 'Roboto Mono', monospace; font-weight: 700; font-size: 9px; }

        /* Sección de Firmas */
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding: 0 20px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1.5px solid var(--doc-black);
            padding-top: 10px;
        }

        .signature-label { font-weight: 700; text-transform: uppercase; font-size: 9px; margin-bottom: 2px; }
        .signature-sub { color: #666; font-size: 8px; }

        /* Marcas de Seguridad */
        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(0,0,0,0.03);
            z-index: 0;
            pointer-events: none;
            text-transform: uppercase;
        }

        /* Estilos para impresión */
        @media print {
            body { background: #ffffff; }
            .document-page { margin: 0; box-shadow: none; border: none; width: 100%; }
            .no-print { display: none !important; }
            .tech-table thead th { background-color: #333 !important; color: #fff !important; }
        }

        .btn-floating {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>

    <div class="no-print btn-floating">
        <button onclick="window.print()" class="btn btn-dark fw-bold px-4 shadow">
            <i class="fa-solid fa-print me-2"></i> Ejecutar Impresión
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary fw-bold px-3 ms-2">
            Cerrar
        </button>
    </div>

    <div class="document-page">
        <div class="watermark-text">Documento Oficial</div>

        <div class="iso-header">
            <div class="iso-cell iso-logo">
                <div class="fw-bold" style="font-size: 16px; color: var(--doc-accent);">
                    <i class="fa-solid fa-building-circle-check me-1"></i> SICOT
                </div>
                <div style="font-size: 7px; font-weight: bold;">GESTIÓN DE INFRAESTRUCTURA</div>
            </div>
            <div class="iso-cell iso-title">
                ACTA DE AUDITORÍA Y CONTROL DE MOVIMIENTOS<br>
                <span style="font-size: 9px; font-weight: 400;">DEPARTAMENTO DE LOGÍSTICA E INVENTARIOS</span>
            </div>
            <div class="iso-cell iso-meta">
                <strong>CÓDIGO:</strong> FOR-LOG-INV-001<br>
                <strong>VERSIÓN:</strong> 3.0.2<br>
                <strong>FECHA EMISIÓN:</strong> <?= date('d/m/Y') ?><br>
                <strong>ORIGEN:</strong> SISTEMA ERP CENTRALIZADO
            </div>
        </div>

        <div class="report-summary">
            <div class="row g-3">
                <div class="col-4">
                    <div class="summary-label">Generado por:</div>
                    <div class="summary-value"><?= strtoupper($_SESSION['identity']->fullname ?? 'USUARIO AUTORIZADO') ?></div>
                </div>
                <div class="col-4 text-center">
                    <div class="summary-label">Fecha de Reporte:</div>
                    <div class="summary-value"><?= date('l, d F Y') ?></div>
                </div>
                <div class="col-4 text-end">
                    <div class="summary-label">Total Movimientos Auditados:</div>
                    <div class="summary-value fw-bold"><?= isset($reportes) ? $reportes->num_rows : 0 ?> REGISTROS</div>
                </div>
            </div>
        </div>

        <table class="tech-table">
            <thead>
                <tr>
                    <th style="width: 10%;">ID TRNS.</th>
                    <th style="width: 12%;">FECHA / HORA</th>
                    <th style="width: 20%;">RESPONSABLE OPERATIVO</th>
                    <th style="width: 15%;">TIPO ACCIÓN</th>
                    <th style="width: 30%;">DESCRIPCIÓN TÉCNICA DEL MOVIMIENTO</th>
                    <th style="width: 13%; text-align: center;">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                    <?php while($row = $reportes->fetch_object()): ?>
                        <?php 
                            $id = $row->request_unique_id ?? $row->id ?? 0;
                            $tipo = strtoupper(str_replace('_', ' ', $row->type));
                        ?>
                        <tr>
                            <td class="id-mono">#<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($row->created_at)) ?></strong><br>
                                <span class="text-muted" style="font-size: 8px;"><?= date('H:i:s', strtotime($row->created_at)) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold"><?= $row->fullname ?></div>
                                <div class="text-muted" style="font-size: 7px;"><?= strtoupper($row->role) ?></div>
                            </td>
                            <td>
                                <span class="fw-bold" style="font-size: 8px;"><?= $tipo ?></span>
                            </td>
                            <td style="font-size: 9px; line-height: 1.2;">
                                <?= $row->description ?>
                            </td>
                            <td style="text-align: center;">
                                <div style="font-weight: 900; border: 1px solid #000; padding: 2px; font-size: 8px;">
                                    <?= $row->status ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted fw-bold">
                            NO SE REGISTRAN ACTIVIDADES DE INVENTARIO EN EL PERIODO ESPECIFICADO.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div style="margin-top: 20px; font-size: 8px; color: #555;">
            <strong>NOTAS TÉCNICAS:</strong> Este documento es un extracto fidedigno de la base de datos de SICOT ERP. Las transacciones aquí descritas cuentan con marcas de tiempo inmutables y vinculación de usuario responsable. La alteración de este documento físico no tiene validez legal frente a los registros digitales del servidor central.
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label"><?= strtoupper($_SESSION['identity']->fullname) ?></div>
                <div class="signature-sub">Administrador de Inventarios (Despacho)</div>
                <div class="text-muted" style="font-size: 7px; margin-top: 5px;">FIRMA Y SELLO DE AUTORIZACIÓN</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">__________________________________</div>
                <div class="signature-sub">Residente de Obra / Funcionario (Recepción)</div>
                <div class="text-muted" style="font-size: 7px; margin-top: 5px;">ACEPTACIÓN DE CARGO Y RESPONSABILIDAD</div>
            </div>
        </div>

        <div style="position: absolute; bottom: 15mm; left: 15mm; right: 15mm; border-top: 1px solid #000; padding-top: 8px; display: flex; justify-content: space-between; align-items: center; font-size: 7px;">
            <div style="max-width: 80%;">
                <strong>CONFIDENCIALIDAD:</strong> La información contenida en este documento está sujeta a políticas de privacidad empresarial. El mal uso de esta información será sancionado conforme a la ley de protección de datos vigente.
            </div>
            <div style="text-align: right;">
                <i class="fa-solid fa-qrcode fa-3x mb-1"></i><br>
                VERIFICACIÓN ELECTRÓNICA: <?= strtoupper(uniqid()) ?>
            </div>
        </div>
    </div>

    <script>
        // Lanzamiento automático de la ventana de impresión
        window.onload = function() {
            setTimeout(function(){
                window.print();
            }, 800);
        }
    </script>
</body>
</html>