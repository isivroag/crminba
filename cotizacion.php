<?php
// Incluir autoload de Composer para TCPDF
// Habilitar visualización de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir la ruta base del proyecto
$rootDir = __DIR__; // O usa dirname(__DIR__) si necesitas subir un nivel

// Verificar existencia del archivo TCPDF
$tcpdfPath = $rootDir . '/vendor/tecnickcom/tcpdf/tcpdf.php';
if (!file_exists($tcpdfPath)) {
    die("Error: No se pudo encontrar TCPDF en: $tcpdfPath");
}

// Incluir TCPDF
require_once $tcpdfPath;

// No necesitas la línea 'use TCPDF' - puedes eliminarla

// Incluir conexión
$conexionPath = $rootDir . '/bd/conexion.php';
if (!file_exists($conexionPath)) {
    die("Error: No se pudo encontrar el archivo de conexión en: $conexionPath");
}
include_once $conexionPath;
// Obtener ID del presupuesto
$id = isset($_GET['id']) ? $_GET['id'] : die('ID de presupuesto no especificado');

// Conexión a BD
$objeto = new conn();
$conexion = $objeto->connect();

// 1. Obtener datos principales del presupuesto
$query_pres = "SELECT * FROM vpresupuesto WHERE id_pres = :id_pres";

$stmt_pres = $conexion->prepare($query_pres);
$stmt_pres->bindParam(':id_pres', $id, PDO::PARAM_INT);
$stmt_pres->execute();
$presupuesto = $stmt_pres->fetch(PDO::FETCH_ASSOC);

if (!$presupuesto) {
    die('No se encontró el presupuesto con ID: ' . $id);
}

// 2. Obtener detalles de pagos
$query_det = "SELECT * FROM detalle_pres WHERE id_pres = :id_pres ORDER BY id_reg";
$stmt_det = $conexion->prepare($query_det);
$stmt_det->bindParam(':id_pres', $id, PDO::PARAM_INT);
$stmt_det->execute();
$detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

// 3. Crear PDF con TCPDF
$pdf = new TCPDF('P', 'mm', 'Letter', true, 'UTF-8', false);

// Configuración del documento
$pdf->SetCreator('Sistema de Cotizaciones');
$pdf->SetAuthor($presupuesto['nombre_pros']);
$pdf->SetTitle('Presupuesto #' . $presupuesto['id_pres']);
$pdf->SetSubject('Presupuesto de Inmueble');

// Margenes más ajustados para mejor uso del espacio
$pdf->SetMargins(10, 35, 10); // Izquierda, Arriba, Derecha
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, 15);

// Agregar página
$pdf->AddPage();

// --- ENCABEZADO CON LOGO --- //
$logoPath = __DIR__ . '/logo.jpg'; // Asegúrate de tener el logo en la raíz
if (file_exists($logoPath)) {
    // Logo a la izquierda
    $pdf->Image($logoPath, 10, 10, 30, 0, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    
    // Información de la empresa a la derecha
    $pdf->SetXY(140, 10);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(60, 5, 'TU EMPRESA S.A. DE C.V.', 0, 1, 'R');
    $pdf->SetX(140);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Cell(60, 5, 'Dirección: Calle Ejemplo #123', 0, 1, 'R');
    $pdf->SetX(140);
    $pdf->Cell(60, 5, 'Col. Centro, Ciudad, Estado', 0, 1, 'R');
    $pdf->SetX(140);
    $pdf->Cell(60, 5, 'Tel: (55) 1234-5678', 0, 1, 'R');
    $pdf->SetX(140);
    $pdf->Cell(60, 5, 'RFC: XAXX010101000', 0, 1, 'R');
}

// Línea decorativa
$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(51, 122, 183)));
$pdf->Line(10, 30, 200, 30);

// --- TÍTULO DEL DOCUMENTO --- //
$pdf->SetY(35);
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(51, 122, 183); // Color azul corporativo
$pdf->Cell(0, 10, 'PRESUPUESTO DE INMUEBLE', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(0, 0, 0); // Volver a color negro
$pdf->Cell(0, 10, 'Folio: ' . $presupuesto['id_pres'], 0, 1, 'C');
$pdf->Ln(5);

// --- INFORMACIÓN DEL CLIENTE --- //
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(240, 240, 240); // Fondo gris claro
$pdf->Cell(0, 10, '  DATOS DEL CLIENTE', 0, 1, 'L', true);
$pdf->SetFont('helvetica', '', 10);

$html = '<table border="0" cellpadding="4">
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
</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(8);

// --- TABLA DE PAGOS MEJORADA --- //
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(51, 122, 183); // Azul corporativo
$pdf->SetTextColor(255, 255, 255); // Texto blanco
$pdf->Cell(0, 10, '  PLAN DE PAGOS', 0, 1, 'L', true);
$pdf->SetTextColor(0, 0, 0); // Volver a texto negro

// Estilo para la tabla
$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => '2',
    'vpadding' => '2',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false,
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 0
);

// Encabezados de la tabla
$header = array(
    'No.' => 'C',
    'Fecha' => 'C',
    'Capital' => 'R',
    'Interés' => 'R',
    'Total' => 'R',
    'Tipo' => 'C',
    'Saldo' => 'R'
);

// Crear tabla
$html = '<table border="0" cellpadding="3" cellspacing="0" style="font-size:8pt;">
    <thead>
        <tr style="background-color:#337ab7;color:#ffffff;font-weight:bold;">';

foreach ($header as $col => $align) {
    $html .= '<th style="text-align:'.$align.';padding:4px;">' . $col . '</th>';
}

$html .= '</tr>
    </thead>
    <tbody>';

// Llenar con datos de pagos
foreach ($detalles as $det) {
    // Alternar colores de fila para mejor lectura
    $bgcolor = ($det['id_reg'] % 2 == 0) ? '#ffffff' : '#f9f9f9';
    
    $html .= '<tr style="background-color:'.$bgcolor.';">';
    $html .= '<td style="text-align:center;padding:4px;">' . $det['id_reg'] . '</td>';
    $html .= '<td style="text-align:center;padding:4px;">' . date('d/m/Y', strtotime($det['fecha'])) . '</td>';
    $html .= '<td style="text-align:right;padding:4px;">$' . number_format($det['capital'], 2) . '</td>';
    $html .= '<td style="text-align:right;padding:4px;">$' . number_format($det['interes'], 2) . '</td>';
    $html .= '<td style="text-align:right;padding:4px;">$' . number_format($det['importe'], 2) . '</td>';
    $html .= '<td style="text-align:center;padding:4px;">' . $det['tipo'] . '</td>';
    $html .= '<td style="text-align:right;padding:4px;">$' . number_format($det['saldo'], 2) . '</td>';
    $html .= '</tr>';
}

// Totales
$html .= '<tr style="background-color:#e9e9e9;font-weight:bold;border-top:1px solid #333;">
    <td colspan="2" style="text-align:right;padding:4px;">TOTALES:</td>
    <td style="text-align:right;padding:4px;">$' . number_format($presupuesto['totalcapital'], 2) . '</td>
    <td style="text-align:right;padding:4px;">$' . number_format($presupuesto['totalinteres'], 2) . '</td>
    <td style="text-align:right;padding:4px;">$' . number_format($presupuesto['totalpagar'], 2) . '</td>
    <td colspan="2"></td>
</tr>';

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(8);

// --- SECCIÓN DE TÉRMINOS Y CONDICIONES --- //
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 8, 'TÉRMINOS Y CONDICIONES', 0, 1);
$pdf->SetFont('helvetica', '', 8);

$terms = array(
    'Este presupuesto es válido por 15 días naturales a partir de la fecha de emisión.',
    'Los precios están sujetos a cambios sin previo aviso.',
    'El enganche deberá ser cubierto en un máximo de ' . $presupuesto['nenganche'] . ' mensualidades.',
    'El financiamiento está sujeto a aprobación crediticia.',
    'La entrega del inmueble está sujeta al cumplimiento de todos los pagos.',
    'Cualquier modificación a este plan de pagos deberá ser autorizada por escrito.'
);

foreach ($terms as $term) {
    $pdf->Cell(5, 5, '', 0, 0);
    $pdf->Cell(5, 5, '•', 0, 0);
    $pdf->MultiCell(0, 5, $term, 0, 'L');
    $pdf->Ln(1);
}

// --- FIRMA --- //
$pdf->Ln(10);
$html = '<table border="0" cellpadding="4">
    <tr>
        <td width="60%"></td>
        <td width="40%" style="text-align:center;">
            <br><br><br>
            ___________________________________<br>
            <strong>FIRMA DEL CLIENTE</strong><br>
            Nombre y Fecha
        </td>
    </tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// --- PIE DE PÁGINA --- //
$pdf->SetY(-15);
$pdf->SetFont('helvetica', 'I', 7);
$pdf->SetTextColor(100, 100, 100); // Texto gris
$pdf->Cell(0, 5, 'Documento generado el ' . date('d/m/Y H:i:s'), 0, 0, 'L');
$pdf->Cell(0, 5, 'Página ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'R');

// Salida del PDF
$pdf->Output('Presupuesto_' . $presupuesto['id_pres'] . '.pdf', 'I');
?>