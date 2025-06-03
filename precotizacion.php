<!DOCTYPE html>
<html>
<head>
    <title>Presupuesto - Vista Previa</title>
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
            margin-bottom: 5px;
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
            font-size: 14px;
            margin-bottom: 3px;
            text-align: center;
        }
        
        .company-details { 
            font-size: 12px; 
            line-height: 1.3;
            text-align: center;
        }
        
        .document-title {  
            color: #153510; 
            font-size: 18px; 
            font-weight: bold; 
            text-align: center; 
            margin: 10px 0 5px 0;
        }
        
        .folio { 
            font-size: 14px; 
            text-align: center; 
            margin-bottom: 10px;
        }
        
        .section-title { 
            background-color: #f5f5f5; 
            padding: 8px; 
            font-weight: bold; 
            font-size: 14px; 
            margin: 10px 0; 
        }
        
        .client-table { 
            width: 100%; 
            font-size: 12px; 
            margin-bottom: 15px; 
        }
        
        .payment-table { 
            width: 100%; 
            font-size: 11px; 
            border-collapse: collapse; 
            margin-bottom: 15px; 
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
            font-size: 12px; 
            margin-top: 15px; 
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

<!-- Título del documento -->
<div class="document-title">PRESUPUESTO</div>
<div class="folio">Folio: PR-2023-001</div>

<!-- Datos del cliente -->
<div class="section-title">DATOS DEL CLIENTE</div>
<table class="client-table">
    <tr>
        <td width="20%"><strong>Nombre:</strong></td>
        <td width="30%">Juan Pérez Hernández</td>
        <td width="20%"><strong>Fecha:</strong></td>
        <td width="30%">15/03/2023</td>
    </tr>
    <tr>
        <td><strong>Proyecto:</strong></td>
        <td>Bosque de las Ánimas</td>
        <td><strong>Manzana:</strong></td>
        <td>5</td>
    </tr>
    <tr>
        <td><strong>Lote:</strong></td>
        <td>25</td>
        <td><strong>Valor Total:</strong></td>
        <td>$1,250,000.00</td>
    </tr>
</table>

<!-- Plan de pagos -->
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
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td class="text-center">15/04/2023</td>
            <td class="text-right">$25,000.00</td>
            <td class="text-right">$5,000.00</td>
            <td class="text-right">$30,000.00</td>
            <td class="text-center">Enganche</td>
            <td class="text-right">$1,225,000.00</td>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td class="text-center">15/05/2023</td>
            <td class="text-right">$30,000.00</td>
            <td class="text-right">$4,500.00</td>
            <td class="text-right">$34,500.00</td>
            <td class="text-center">Mensual</td>
            <td class="text-right">$1,195,000.00</td>
        </tr>
        <!-- Más filas de pagos... -->
        <tr class="totals-row">
            <td colspan="2" class="text-right">TOTALES:</td>
            <td class="text-right">$1,250,000.00</td>
            <td class="text-right">$150,000.00</td>
            <td class="text-right">$1,400,000.00</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>

<!-- Términos y condiciones -->
<div class="section-title">TÉRMINOS Y CONDICIONES</div>
<div class="terms">
    <p>• Este presupuesto es válido por 15 días naturales a partir de la fecha de emisión.</p>
    <p>• Los precios están sujetos a cambios sin previo aviso.</p>
    <p>• El enganche deberá ser cubierto en un máximo de 3 mensualidades.</p>
    <p>• El financiamiento está sujeto a aprobación crediticia.</p>
    <p>• La entrega del inmueble está sujeta al cumplimiento de todos los pagos.</p>
    <p>• Cualquier modificación a este plan de pagos deberá ser autorizada por escrito.</p>
</div>

<!-- Firma -->
<div class="signature">
    ___________________________________<br>
    <strong>FIRMA DEL CLIENTE</strong><br>
    Nombre y Fecha
</div>

<!-- Pie de página -->
<div class="footer">
    <div style="float:left;">Documento generado el 15/03/2023 14:30:00</div>
    <div style="float:right;">Página 1/1</div>
    <div style="clear:both;"></div>
</div>

</body>
</html>