<?php
    $folio = (isset($_GET['id'])) ? $_GET['id'] : '';

   
require_once __DIR__.'/../vendor/autoload.php';

// Verificar si la clase existe
if (!class_exists('\Mpdf\Mpdf')) {
    die('Error: La clase Mpdf no está disponible. ¿Instalaste la librería con Composer?');
}
  
    
    $css=file_get_contents('../css/estilocotizacion2.css');

    require_once ('pcotiza.php');
    $plantilla= getPlantilla($folio);
   
    $mpdf = new \Mpdf\Mpdf(['format' => 'Letter']);

    
    
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
    $mpdf->WriteHTML($plantilla,\Mpdf\HTMLParserMode::HTML_BODY);
    $mpdf->Output("Presupuesto ".$folio.".pdf","I");

   
?>