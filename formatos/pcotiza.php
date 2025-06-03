



<?php



function getPlantilla($folio)
{



	include_once '../bd/conexion.php';

	$plantilla = "";

	if ($folio != "") {
		$objeto = new conn();
		$conexion = $objeto->connect();


		$consulta = "SELECT * FROM vpresupuesto WHERE id_pres='$folio'";

		$resultado = $conexion->prepare($consulta);
		$resultado->execute();


		$data = $resultado->fetchAll(PDO::FETCH_ASSOC);

		foreach ($data as $dt) {
			$folio = $dt['id_pres'];

			$fecha = $dt['fecha_pres'];
			$idpros = $dt['id_pros'];
			$prospecto = $dt['nombre_pros'];
			$proyecto = $dt['nproyecto'];
			$manzana = $dt['nmanzana'];
			$lote = $dt['nlote'];
			$concepto = $proyecto . ' - ' . $manzana . ' - ' . $lote;
			$ubicacion = "";
			$total = $dt['totalpagar'];
			$tasa = $dt['tasa'];
			$importe = $dt['importe'];
			$descuento = $dt['descuento'];
			$valorop = $dt['valorop'];
			$enganche = $dt['enganche'];
			$nenganche = $dt['nenganche'];
			$msi = $dt['nmsi'];
			$mci = $dt['nmci'];
			$totalcapital = $dt['totalcapital'];
			$totalinteres = $dt['totalinteres'];
			$totalpagar = $dt['totalpagar'];
		}






		$consultadet = "SELECT * FROM detalle_pres WHERE id_pres='$folio' ORDER BY id_reg";
		$resultadodet = $conexion->prepare($consultadet);
		$resultadodet->execute();
		$datadet = $resultadodet->fetchAll(PDO::FETCH_ASSOC);
	} else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="../inicio.php";';
		echo '</script>';
	}

	$plantilla .= '
<body>

	
		<header class="">
           <table>
           <tr>
           <td>
            <div class="logo_factura">
                <img style="width:180px;" src="../img/logoVerde.png">
            </div>
        </td>
        <td class="textcenter">
            <div class="info_empresa" >
                <p><span class="empresa"><b>INMOBILIARIA BOSQUE DE LAS ANIMAS S.A. DE C.V.<b></span><br>
                     BLVD. CRISTOBAL COLON 5 INT 501<br>
                COL FUENTE DE LAS ANIMAS<br>
                Tel: (55) 1234-5678<br>
                RFC: IBA040421EB4
            </div>
        </td>
        <td class="round">
            <div class=" info_factura">
                
                <p>No. Presupuesto: <strong>' . $folio . '</strong></p>
                <p>Fecha: ' . $fecha . '</p> 
				<p>T.I. Anual: ' . $tasa . '%</p>

            </div>
            </td>
    			<tr>				
			</table>
        </header>
        <main>
		<br>
		<div>
			<table class="factura_cliente">
				<tr>
					<td class="info_cliente">
						<div class="round">
							<span class="encabezado">Cliente: <b>' . $prospecto . '</b> </span><br>
							<span class="encabezado">Proyecto: <b>' . $concepto . '</b> </span><br>
							<table class="detalle_pres">
								<thead>
									<tr>
										<th class="textcenter">Valor</th>
										<th class="textcenter">Descuento</th>
										<th class="textcenter">Importe Total</th>
										<th class="textcenter">Enganche</th>
										<th class="textcenter">M-Eng.</th>
										<th class="textcenter">MSI</th>
										<th class="textcenter">MCI</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="textright">$ ' . number_format($importe, 2) . '</td>
										<td class="textright">$ ' . number_format($descuento, 2) . '</td>
										<td class="textright">$ ' . number_format($valorop, 2) . '</td>
										<td class="textright">$ ' . number_format($enganche, 2) . '</td>
										<td class="textcenter">' . number_format($nenganche, 0) . '</td>
										<td class="textcenter"> ' . number_format($msi, 0) . '</td>
										<td class="textcenter"> ' . number_format($mci, 0) . '</td>
									</tr>	
								</tbody>
							</table>
									

							
							
						</div>
					</td>

				</tr>
			</table>
		</div>
		<div>
			<table class="factura_detalle">
				<thead class="" style="width:100%">
					<tr>
						<th class="textcenter">No. Pago</th>
						<th class="textcenter">Fecha</th>
						<th class="textcenter">Capital</th>
						<th class="textcenter">Interes</th>
						<th class="textcenter">Total</th>
						<th class="textcenter">Tipo</th>
						<th class="textcenter">Saldo</th>

					</tr>
				</thead>

                <tbody class="detalle_productos">';
	foreach ($datadet as $row) {
		$plantilla .= '<tr>
							<td>' . $row['id_reg'] . '</td>
							<td>' . $row['fecha'] . '</td>
							<td class="textright">$ ' . number_format($row['capital'], 2) . '</td>
							<td class="textright">$ ' . number_format($row['interes'], 2) . '</td>
							<td class="textright">$ ' . number_format($row['importe'], 2) . '</td>
							<td class="textright">' . $row['tipo'] . '</td>
							<td class="textright">$ ' . number_format($row['saldo'], 2) . '</td>
						</tr>';
	}
	$plantilla .= '</tbody>
				<br>

				<tfoot class="detalle_totales">

					<tr>
						<th colspan="2" class="textright"><span>TOTAL:</span></th>
						<th class="textright"><span>$ ' . number_format($totalcapital, 2) . '</span></th>
						<th class="textright"><span>$ ' . number_format($totalinteres, 2) . '</span></th>
						<th class="textright"><span>$ ' . number_format($totalpagar, 2) . '</span></th>
					</tr>
				</tfoot>

			</table>
		</div>
		<div>
		
		</div>
	</main>


</body>
';
	return $plantilla;
}
