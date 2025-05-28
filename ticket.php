<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ticket</title>
  <style>
    * {
      font-family: monospace;
      font-size: 12px;
    }
    .ticket {
      width: 80mm;
      padding: 5px;
    }
    .center {
      text-align: center;
    }
    .bold {
      font-weight: bold;
    }
    .line {
      border-top: 1px dashed black;
      margin: 5px 0;
    }
  </style>
</head>
<body onload="window.print()">
  <div class="ticket">
    <div class="center bold">Mi Tienda</div>
    <div class="center">RFC: XAXX010101000</div>
    <div class="center">Dirección de ejemplo</div>
    <div class="line"></div>

    <p>Folio: 12345</p>
    <p>Fecha: <?php echo date("d/m/Y H:i:s"); ?></p>

    <div class="line"></div>

    <p>Producto A &nbsp;&nbsp;&nbsp;&nbsp; $10.00</p>
    <p>Producto B &nbsp;&nbsp;&nbsp;&nbsp; $25.00</p>

    <div class="line"></div>

    <p>Total: <strong>$35.00</strong></p>

    <div class="line"></div>

    <div class="center">¡Gracias por su compra!</div>
  </div>
</body>
</html>
