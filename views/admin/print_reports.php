<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Corporativo de Auditoría</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --corporate-dark: #1a2a3a;
            --corporate-blue: #004e92;
            --corporate-gray: #f4f6f8;
            --border-color: #e0e0e0;
        }

        body {
            background-color: #555; /* Fondo oscuro en pantalla para resaltar la hoja */
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            color: #333;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Simulación de Hoja A4 */
        .page-container {
            width: 210mm;
            min-height: 297mm;
            margin: 30px auto;
            background: white;
            padding: 15mm;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
        }

        /* Marca de Agua de Fondo */
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 120px;
            color: rgba(0,0,0,0.03);
            font-weight: 800;
            z-index: 0;
            white-space: nowrap;
            pointer-events: none;
            text-transform: uppercase;
        }

        /* Encabezado Corporativo */
        .header-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            align-items: center;
            border-bottom: 2px solid var(--corporate-dark);
            padding-bottom: 20px;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .logo-area img {
            max-height: 60px;
            max-width: 100%;
        }
        
        .logo-placeholder {
            font-size: 24px;
            color: var(--corporate-dark);
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .company-details {
            text-align: center;
        }

        .company-details h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: var(--corporate-dark);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .company-details p {
            margin: 2px 0;
            color: #666;
            font-size: 10px;
        }

        .doc-meta {
            text-align: right;
            font-size: 9px;
            color: #555;
            border-left: 1px solid #ddd;
            padding-left: 15px;
        }

        /* Título del Documento */
        .doc-title {
            background-color: var(--corporate-gray);
            padding: 10px 15px;
            border-left: 5px solid var(--corporate-blue);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .doc-title h3 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--corporate-blue);
            text-transform: uppercase;
        }

        .confidential-badge {
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 2px;
        }

        /* Tabla Profesional */
        table {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
            font-size: 10px;
        }

        thead {
            background-color: var(--corporate-dark);
            color: white;
        }

        th {
            padding: 10px 8px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 9px;
            text-align: left;
        }

        td {
            padding: 12px 8px;
            border-bottom: 1px solid var(--border-color);
            color: #444;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: rgba(244, 246, 248, 0.5);
        }

        /* Badges de Estado Sutiles */
        .status-dot {
            height: 8px;
            width: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .status-text {
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
        }

        /* Pie de Página */
        .footer {
            position: absolute;
            bottom: 15mm;
            left: 15mm;
            right: 15mm;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 8px;
        }

        .footer-legal {
            max-width: 70%;
            text-align: justify;
        }

        .qr-placeholder {
            width: 50px;
            height: 50px;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        /* Botones No Imprimibles */
        .no-print-bar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        @media print {
            body { background: white; margin: 0; }
            .page-container { margin: 0; box-shadow: none; border: none; width: 100%; max-width: 100%; padding: 0; }
            .no-print-bar { display: none !important; }
            .watermark { opacity: 0.1; } /* Ajuste para impresión */
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button onclick="window.print()" class="btn btn-dark btn-sm fw-bold me-2">
            <i class="fa-solid fa-file-pdf"></i> Guardar PDF
        </button>
        <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
            Cerrar
        </button>
    </div>

    <div class="page-container">
        
        <div class="watermark">Constructora S.A.S</div>

        <header class="header-grid">
            <div class="logo-area">
                <div class="logo-placeholder">
                    <i class="fa-solid fa-helmet-safety text-primary"></i> CONSTRUCTORA
                </div>
            </div>
            <div class="company-details">
                <h2>Constructora S.A.S</h2>
                <p>NIT: 900.123.456-1 | ISO 9001:2015 Certified</p>
                <p>Calle Principal #10-20, Zona Industrial</p>
                <p>Bogotá, Colombia | www.constructorasas.com</p>
            </div>
            <div class="doc-meta">
                <p><strong>CÓDIGO:</strong> REP-INV-2026</p>
                <p><strong>VERSIÓN:</strong> 2.4</p>
                <p><strong>FECHA:</strong> <?= date('d/m/Y') ?></p>
                <p><strong>PÁGINA:</strong> 1 de 1</p>
            </div>
        </header>

        <div class="doc-title">
            <h3>Reporte de Auditoría de Inventario & Solicitudes</h3>
            <span class="confidential-badge">Confidencial - Uso Interno</span>
        </div>

        <div class="row mb-4 px-2" style="font-size: 10px; color: #555;">
            <div class="col-6">
                <strong>GENERADO POR:</strong> <?= strtoupper($_SESSION['identity']->fullname ?? 'SISTEMA ADMINISTRATIVO') ?><br>
                <strong>CARGO:</strong> ADMINISTRADOR DEL SISTEMA
            </div>
            <div class="col-6 text-end">
                <strong>PERIODO:</strong> HISTÓRICO COMPLETO<br>
                <strong>TOTAL REGISTROS:</strong> <?= isset($reportes) ? $reportes->num_rows : 0 ?> MOVIMIENTOS
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">REF ID</th>
                    <th style="width: 15%;">FECHA / HORA</th>
                    <th style="width: 22%;">RESPONSABLE</th>
                    <th style="width: 15%;">TIPO OPERACIÓN</th>
                    <th style="width: 25%;">DETALLE / OBSERVACIÓN</th>
                    <th style="width: 15%; text-align: right;">ESTADO</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($reportes) && $reportes->num_rows > 0): ?>
                    <?php while($row = $reportes->fetch_object()): ?>
                        <?php 
                            $id = $row->request_unique_id ?? $row->id ?? '---';
                            $tipo = strtoupper(str_replace('_', ' ', $row->type));
                        ?>
                        <tr>
                            <td style="font-family: monospace; font-weight: bold;">#<?= str_pad($id, 6, '0', STR_PAD_LEFT) ?></td>
                            <td>
                                <?= date('d/m/Y', strtotime($row->created_at)) ?><br>
                                <span style="color: #888;"><?= date('H:i:s', strtotime($row->created_at)) ?></span>
                            </td>
                            <td>
                                <strong style="color: var(--corporate-dark);"><?= $row->fullname ?></strong><br>
                                <span style="color: #666; font-size: 8px;"><?= strtoupper($row->role) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; font-size: 8px; letter-spacing: 0.5px;">
                                    <?= $tipo ?>
                                </span>
                            </td>
                            <td>
                                <?= $row->description ?>
                            </td>
                            <td style="text-align: right;">
                                <?php 
                                    $color = '#6c757d'; // Default gris
                                    if($row->status == 'APROBADO') $color = '#198754';
                                    if($row->status == 'PENDIENTE') $color = '#ffc107';
                                    if($row->status == 'RECHAZADO') $color = '#dc3545';
                                ?>
                                <span class="status-dot" style="background-color: <?= $color ?>;"></span>
                                <span class="status-text" style="color: <?= $color ?>;"><?= $row->status ?></span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                            -- NO EXISTEN REGISTROS DE AUDITORÍA EN EL SISTEMA --
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="footer">
            <div class="footer-legal">
                <p><strong>AVISO LEGAL:</strong> Este documento contiene información confidencial y de propiedad de CONSTRUCTORA S.A.S. Cualquier divulgación, copia, distribución o acción tomada en base a este contenido está estrictamente prohibida. Documento generado electrónicamente por el Sistema ERP V2.0.</p>
            </div>
            <div style="text-align: right;">
                <div class="qr-placeholder"><i class="fa-solid fa-qrcode"></i></div>
                <div style="margin-top: 5px;">ID: <?= uniqid() ?></div>
            </div>
        </footer>

    </div>

    <script>
        // Auto-imprimir tras cargar recursos
        window.onload = function() {
            setTimeout(function(){
                window.print();
            }, 1000);
        }
    </script>
</body>
</html>