<?php
// Configuración inicial (igual que antes)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = __DIR__;
$tcpdfPath = $rootDir . '/vendor/tecnickcom/tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    die("Error: No se pudo encontrar TCPDF en: $tcpdfPath");
}

require_once $tcpdfPath;
include_once $rootDir . '/bd/conexion.php';

$id = isset($_GET['id']) ? $_GET['id'] : die('ID de presupuesto no especificado');

// Conexión y consultas (igual que antes)
$objeto = new conn();
$conexion = $objeto->connect();

$query_pres = "SELECT * FROM vpresupuesto WHERE id_pres = :id_pres";
$stmt_pres = $conexion->prepare($query_pres);
$stmt_pres->bindParam(':id_pres', $id, PDO::PARAM_INT);
$stmt_pres->execute();
$presupuesto = $stmt_pres->fetch(PDO::FETCH_ASSOC);

if (!$presupuesto) {
    die('No se encontró el presupuesto con ID: ' . $id);
}

$query_det = "SELECT * FROM detalle_pres WHERE id_pres = :id_pres ORDER BY id_reg";
$stmt_det = $conexion->prepare($query_det);
$stmt_det->bindParam(':id_pres', $id, PDO::PARAM_INT);
$stmt_det->execute();
$detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

// Crear PDF configurando para no mostrar header/footer automáticos
// Configuración inicial del PDF - AJUSTAR MÁRGENES
$pdf = new TCPDF('P', 'mm', 'Letter', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(15, 10, 15); // Reducir margen superior de 15 a 10
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->AddPage();
// Ajustar posición inicial del contenido
$pdf->SetY(10); // Posición inicial más arriba
// Logo (ajusta la ruta según tu estructura)
$logoPath = 'img/logoVerde.jpg';
$logoHTML = '';
if (file_exists($logoPath)) {
    $logoHTML = '<img src="' . $logoPath . '" style="height:15mm; float:left;">';
}

// Construir el HTML completo
$html = '
 <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 15px;
            width: 210mm; /* Tamaño carta */
        }
        
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 0mm;
        }
        
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        
        .company-info { 
            text-align: center; 
        }
        
        .company-name { 
            font-weight: bold; 
            font-size: 11px;
            margin-bottom: 100mm;
            text-align: center;
        }
        
        .company-details { 
            font-size: 10px; 
            line-height: 1.2;
            text-align: center;
        }
        
        .document-title {  
            color: #153510; 
            font-size: 14px; 
            font-weight: bold; 
            text-align: center; 
         
        }
        
        .folio { 
            font-size: 14px; 
            text-align: left; 
            margin-bottom: 0px;
        }
        
        .section-title { 
            background-color: #f5f5f5; 
            padding: 8px; 
            font-weight: bold; 
            font-size: 12px; 
            margin: 0; 
        }
        
        .client-table { 
            width: 100%; 
            font-size: 11px; 
            margin-bottom: 15px; 
        }
        
        .payment-table { 
            width: 100%; 
            font-size: 9px; 
            border-collapse: collapse; 
            margin-bottom: 2mm; 
        }
        
        .payment-table th { 
            background-color: #153510; 
            color: white; 
            padding: 8px; 
            text-align: center; 
        }
        
        .payment-table td { 
            padding: 6px; 
            border-bottom: 1px solid #ddd; 
        }
        
        .payment-table tr:nth-child(even) { 
            background-color: #f9f9f9; 
        }
        
        .totals-row { 
            background-color: #e9e9e9; 
            font-weight: bold; 
        }
        
        .terms { 
            font-size: 10px; 
            margin-top: 0px; 
        }
        
        .signature { 
            margin-top: 30px; 
            text-align: right; 
        }
        
        .footer { 
            font-size: 11px; 
            color: #666; 
            margin-top: 15px; 
            border-top: 1px solid #eee; 
            padding-top: 8px; 
        }
        
        .text-right { 
            text-align: right; 
        }
        
        .text-center { 
            text-align: center; 
        }
        
        img.logo {
            height: 40px;
        }
    </style>
</head>
<body>

<!-- Encabezado con logo y datos de empresa -->
<table class="header-table">
    <tr>
        <td style="width: 20%; text-align: left;">
            <img src="img/logoVerde.jpg" class="logo">
        </td>
          <td style="width: 60%;">
            <div class="company-name">INMOBILIARIA BOSQUE DE LAS ANIMAS S.A. DE C.V.</div>
            <div class="company-details">
                BLVD. CRISTOBAL COLON 5 INT 501<br>
                COL FUENTE DE LAS ANIMAS<br>
                Tel: (55) 1234-5678<br>
                RFC: IBA040421EB4
            </div>
        </td>
        <td style="width: 20%; text-align: right;">
            <img src="img/logoVerde.jpg" class="logo">
        </td>
    </tr>
</table>


</div>
<div class="document-title">PRESUPUESTO</div>
<div class="folio">Folio: ' . $presupuesto['id_pres'] . '</div>
<div class="section-title">DATOS DEL CLIENTE</div>
<table class="client-table">
    <tr>
        <td width="20%"><strong>Nombre:</strong></td>
        <td width="30%">' . htmlspecialchars($presupuesto['nombre_pros']) . '</td>
        <td width="20%"><strong>Fecha:</strong></td>
        <td width="30%">' . date('d/m/Y', strtotime($presupuesto['fecha_pres'])) . '</td>
    </tr>
    <tr>
        <td><strong>Proyecto:</strong></td>
        <td>' . htmlspecialchars($presupuesto['nproyecto']) . '</td>
        <td><strong>Manzana:</strong></td>
        <td>' . htmlspecialchars($presupuesto['nmanzana']) . '</td>
    </tr>
    <tr>
        <td><strong>Lote:</strong></td>
        <td>' . htmlspecialchars($presupuesto['nlote']) . '</td>
        <td><strong>Valor Total:</strong></td>
        <td>$' . number_format($presupuesto['totalpagar'], 2) . '</td>
    </tr>
</table>

<div class="section-title">PLAN DE PAGOS</div>
<table class="payment-table">
    <thead>
        <tr>
            <th>No.</th>
            <th>Fecha</th>
            <th class="text-right">Capital</th>
            <th class="text-right">Interés</th>
            <th class="text-right">Total</th>
            <th>Tipo</th>
            <th class="text-right">Saldo</th>
        </tr>
    </thead>
    <tbody>';

foreach ($detalles as $det) {
    $html .= '
    <tr>
        <td class="text-center">' . $det['id_reg'] . '</td>
        <td class="text-center">' . date('d/m/Y', strtotime($det['fecha'])) . '</td>
        <td class="text-right">$' . number_format($det['capital'], 2) . '</td>
        <td class="text-right">$' . number_format($det['interes'], 2) . '</td>
        <td class="text-right">$' . number_format($det['importe'], 2) . '</td>
        <td class="text-center">' . $det['tipo'] . '</td>
        <td class="text-right">$' . number_format($det['saldo'], 2) . '</td>
    </tr>';
}

$html .= '
    <tr class="totals-row">
        <td colspan="2" class="text-right">TOTALES:</td>
        <td class="text-right">$' . number_format($presupuesto['totalcapital'], 2) . '</td>
        <td class="text-right">$' . number_format($presupuesto['totalinteres'], 2) . '</td>
        <td class="text-right">$' . number_format($presupuesto['totalpagar'], 2) . '</td>
        <td colspan="2"></td>
    </tr>
    </tbody>
</table>

<div class="section-title">TÉRMINOS Y CONDICIONES</div>
<div class="terms">
    <p>• Este presupuesto es válido por 15 días naturales a partir de la fecha de emisión.</p>
    <p>• Los precios están sujetos a cambios sin previo aviso.</p>
    <p>• El enganche deberá ser cubierto en un máximo de ' . $presupuesto['nenganche'] . ' mensualidades.</p>
    <p>• El financiamiento está sujeto a aprobación crediticia.</p>
    <p>• La entrega del inmueble está sujeta al cumplimiento de todos los pagos.</p>
    <p>• Cualquier modificación a este plan de pagos deberá ser autorizada por escrito.</p>
</div>

<div class="signature">
    ___________________________________<br>
    <strong>FIRMA DEL CLIENTE</strong><br>
    Nombre y Fecha
</div>

<div class="footer">
    <div style="float:left;">Documento generado el ' . date('d/m/Y H:i:s') . '</div>
    <div style="float:right;">Página ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages() . '</div>
    <div style="clear:both;"></div>
</div>';

// Escribir todo el HTML de una vez
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('Presupuesto_' . $presupuesto['id_pres'] . '.pdf', 'I');
