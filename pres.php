<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Crédito con Manejo Correcto de Fechas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #2980b9;
        }

        .results {
            margin-top: 30px;
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .totals {
            margin-top: 20px;
            padding: 15px;
            background: #e8f4fc;
            border-radius: 4px;
        }

        .error {
            color: #e74c3c;
            margin-top: 5px;
        }

        .info {
            color: #3498db;
            margin-top: 5px;
            font-size: 0.9em;
        }

        .rounded-row {
            background-color: #e8f8f5;
        }

        .adjustment-detail {
            font-size: 0.8em;
            color: #27ae60;
        }

        .date-adjustment {
            font-size: 0.8em;
            color: #f39c12;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Simulador de Crédito con Manejo Correcto de Fechas</h1>

        <form id="creditForm">
            <div class="form-group">
                <label for="folio">Número de Folio/Referencia:</label>
                <input type="text" id="folio" name="folio" required>
            </div>

            <div class="form-group">
                <label for="fechaInicio">Fecha de Inicio (día de pago):</label>
                <input type="date" id="fechaInicio" name="fechaInicio" required>
                <div class="info">Los pagos se realizarán el mismo día cada mes. Si el día no existe, se usará el último día del mes.</div>
            </div>

            <div class="form-group">
                <label for="montoTotal">Monto Total del Crédito:</label>
                <input type="number" id="montoTotal" name="montoTotal" min="1" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="montoEnganche">Monto de Enganche:</label>
                <input type="number" id="montoEnganche" name="montoEnganche" min="0" step="0.01" required>
                <div id="engancheError" class="error"></div>
            </div>

            <div class="form-group">
                <label for="plazosEnganche">Plazos para Pagar Enganche (meses):</label>
                <input type="number" id="plazosEnganche" name="plazosEnganche" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="plazosSinInteres">Plazos sin Interés (meses):</label>
                <input type="number" id="plazosSinInteres" name="plazosSinInteres" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="plazosConInteres">Plazos con Interés (meses):</label>
                <input type="number" id="plazosConInteres" name="plazosConInteres" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="tasaInteresAnual">Tasa de Interés Anual (%):</label>
                <input type="number" id="tasaInteresAnual" name="tasaInteresAnual" min="0" step="0.01" value="12.00">
                <div class="info">El interés se calcula sobre saldos insolutos (mensual = anual/12)</div>
            </div>

            <div class="info">Los montos se redondearán al múltiplo de 5 más cercano y las diferencias se sumarán al capital</div>

            <button type="button" onclick="calcularCorrida()">Calcular Corrida Financiera</button>
        </form>

        <div id="results" class="results">
            <h2>Plan de Pagos</h2>
            <div id="paymentTable"></div>

            <div class="totals">
                <h3>Totales</h3>
                <p><strong>Total Capital:</strong> $<span id="totalCapital">0.00</span></p>
                <p><strong>Total Intereses:</strong> $<span id="totalIntereses">0.00</span></p>
                <p><strong>Total a Pagar:</strong> $<span id="totalPagar">0.00</span></p>
                <p><strong>CAT (Costo Anual Total):</strong> <span id="cat">0.00</span>%</p>
            </div>
        </div>
    </div>

    <script>
        // Función para redondear al múltiplo de 5 más cercano
        function redondearA5(monto) {
            return Math.round(monto / 5) * 5;
        }

        // Función para obtener la fecha de pago ajustada CORREGIDA
        function obtenerFechaPago(fechaBase, mesesAdicionales) {
            // Creamos una copia de la fecha base
            let fecha = new Date(fechaBase);
            let diaOriginal = fecha.getDate();

            // Obtenemos el año y mes inicial
            let year = fecha.getFullYear();
            let month = fecha.getMonth();

            // Calculamos el nuevo mes y año
            month += mesesAdicionales;
            year += Math.floor(month / 12);
            month = month % 12;

            // Creamos la nueva fecha
            let nuevaFecha = new Date(year, month, 1);

            // Obtenemos el último día del mes
            let ultimoDiaMes = new Date(year, month + 1, 0).getDate();

            // Determinamos el día a usar
            let diaUsar = Math.min(diaOriginal, ultimoDiaMes);

            // Establecemos el día correcto
            nuevaFecha.setDate(diaUsar);

            return {
                fecha: nuevaFecha,
                ajustada: diaUsar !== diaOriginal,
                diaOriginal: diaOriginal
            };
        }

        // Función para formatear fecha como dd/mm/aaaa
        function formatearFecha(fecha) {
            let dia = fecha.getDate().toString().padStart(2, '0');
            let mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
            let año = fecha.getFullYear();
            return `${dia}/${mes}/${año}`;
        }

        function calcularCorrida() {
            // Obtener valores del formulario
            let folio = document.getElementById('folio').value;
            let fechaInicio = new Date(document.getElementById('fechaInicio').value);
            let montoTotal = parseFloat(document.getElementById('montoTotal').value);
            let montoEnganche = parseFloat(document.getElementById('montoEnganche').value);
            let plazosEnganche = parseInt(document.getElementById('plazosEnganche').value) || 0;
            let plazosSinInteres = parseInt(document.getElementById('plazosSinInteres').value) || 0;
            let plazosConInteres = parseInt(document.getElementById('plazosConInteres').value) || 0;
            let tasaInteresAnual = parseFloat(document.getElementById('tasaInteresAnual').value) / 100;
            let tasaInteresMensual = tasaInteresAnual / 12;

            // Validar que el enganche no sea mayor al monto total
            if (montoEnganche > montoTotal) {
                document.getElementById('engancheError').textContent = 'El enganche no puede ser mayor al monto total';
                return;
            } else {
                document.getElementById('engancheError').textContent = '';
            }

            // Inicializar variables
            let saldoInsoluto = montoTotal - montoEnganche;
            let saldoEnganche = montoEnganche;
            let tablaPagos = [];
            let totales = {
                capital: 0,
                intereses: 0,
                total: 0
            };
            let capitalPendiente = montoTotal - montoEnganche;
            let montoBasePago = 0;

            // Función para agregar un pago a la tabla
            const agregarPago = (numero, fecha, capital, interes, total, tipo, saldo, esRedondeado = false, ajuste = 0, infoFecha = null) => {
                const pago = {
                    numero,
                    fecha: formatearFecha(fecha.fecha),
                    capital: capital.toFixed(2),
                    interes: interes.toFixed(2),
                    total: total.toFixed(2),
                    tipo,
                    saldo: saldo.toFixed(2),
                    esRedondeado,
                    ajuste: ajuste.toFixed(2),
                    infoFecha
                };

                tablaPagos.push(pago);

                totales.capital += capital;
                totales.intereses += interes;
                totales.total += total;

                return pago;
            };

            // Procesar pagos de enganche
            if (plazosEnganche > 0 && saldoEnganche > 0) {
                let montoBaseEnganche = saldoEnganche / plazosEnganche;

                for (let i = 0; i < plazosEnganche; i++) {
                    const esUltimoPago = (i === plazosEnganche - 1);
                    let montoPago;

                    if (esUltimoPago) {
                        montoPago = saldoEnganche;
                    } else {
                        montoPago = Math.min(montoBaseEnganche, saldoEnganche);
                    }

                    // Redondear a múltiplo de 5
                    let montoRedondeado = redondearA5(montoPago);
                    let diferencia = montoRedondeado - montoPago;

                    // Si el redondeo es mayor que el saldo disponible, ajustar
                    if (montoRedondeado > saldoEnganche) {
                        montoRedondeado = saldoEnganche;
                        diferencia = 0;
                    }

                    // Sumar la diferencia al capital pendiente
                    if (diferencia > 0) {
                        capitalPendiente += diferencia;
                    }

                    // Actualizar saldos
                    saldoEnganche -= montoRedondeado;
                    let saldoCapital = saldoEnganche + saldoInsoluto + (diferencia > 0 ? diferencia : 0);

                    // Obtener fecha de pago CORREGIDA
                    let fechaPago = obtenerFechaPago(fechaInicio, i + 1);

                    // Agregar pago
                    agregarPago(
                        i + 1,
                        fechaPago,
                        montoRedondeado,
                        0,
                        montoRedondeado,
                        'ENGANCHE',
                        saldoCapital,
                        montoRedondeado !== montoPago,
                        diferencia,
                        fechaPago.ajustada ? {
                            diaOriginal: fechaPago.diaOriginal
                        } : null
                    );
                }
            }

            // Procesar pagos normales (con y sin intereses)
            if (saldoInsoluto > 0) {
                const totalPlazos = plazosConInteres + plazosSinInteres;

                if (totalPlazos > 0) {
                    montoBasePago = capitalPendiente / totalPlazos;
                    const puntoCambioInteres = plazosSinInteres;

                    for (let i = 0; i < totalPlazos; i++) {
                        const esUltimoPago = (i === totalPlazos - 1);
                        const conInteres = (i >= puntoCambioInteres);
                        let montoPago;

                        if (esUltimoPago) {
                            montoPago = capitalPendiente;
                        } else {
                            montoPago = Math.min(montoBasePago, capitalPendiente);
                        }

                        // Redondear a múltiplo de 5
                        let montoRedondeado = redondearA5(montoPago);
                        let diferencia = montoRedondeado - montoPago;

                        // Asegurar que no exceda el capital pendiente
                        if (montoRedondeado > capitalPendiente) {
                            montoRedondeado = capitalPendiente;
                            diferencia = 0;
                        }

                        // Distribuir la diferencia en los pagos restantes
                        if (!esUltimoPago && diferencia > 0) {
                            const pagosRestantes = totalPlazos - i - 1;
                            if (pagosRestantes > 0) {
                                montoBasePago += diferencia / pagosRestantes;
                            }
                        }

                        // Calcular interés sobre saldo insoluto (tasa mensual)
                        let interes = conInteres ? saldoInsoluto * tasaInteresMensual : 0;
                        let interesRedondeado = redondearA5(interes);

                        // Calcular total a pagar
                        let totalPago = montoRedondeado + interesRedondeado;

                        // Actualizar saldos
                        capitalPendiente -= montoRedondeado;
                        saldoInsoluto -= montoRedondeado;

                        // Obtener fecha de pago CORREGIDA
                        let mesesAdicionales = plazosEnganche + i + 1;
                        let fechaPago = obtenerFechaPago(fechaInicio, mesesAdicionales);

                        // Agregar pago
                        const tipoPago = conInteres ? 'CON INTERES' : 'SIN INTERES';
                        agregarPago(
                            (plazosEnganche > 0 ? plazosEnganche : 0) + i + 1,
                            fechaPago,
                            montoRedondeado,
                            interesRedondeado,
                            totalPago,
                            tipoPago,
                            saldoInsoluto,
                            montoRedondeado !== montoPago,
                            diferencia,
                            fechaPago.ajustada ? {
                                diaOriginal: fechaPago.diaOriginal
                            } : null
                        );
                    }
                }
            }

            // Calcular CAT aproximado (fórmula simplificada)
            const calcularCAT = () => {
                if (totales.intereses <= 0 || montoTotal <= 0) return 0;

                const plazoTotalMeses = plazosEnganche + plazosConInteres + plazosSinInteres;
                if (plazoTotalMeses === 0) return 0;

                const plazoTotalAnios = plazoTotalMeses / 12;
                const catAproximado = (Math.pow(1 + (totales.intereses / montoTotal), 1 / plazoTotalAnios) - 1) * 100;

                return catAproximado.toFixed(2);
            };

            // Generar tabla HTML
            let tablaHTML = '<table>';
            tablaHTML += `
                <thead>
                    <tr>
                        <th>No. Pago</th>
                        <th>Fecha</th>
                        <th>Capital</th>
                        <th>Interés</th>
                        <th>Total Pago</th>
                        <th>Tipo</th>
                        <th>Saldo Insoluto</th>
                        
                    </tr>
                </thead>
                <tbody>
            `;

            tablaPagos.forEach(pago => {
                let detalle = '';
                let rowClass = '';

                // Función para formatear como moneda
                const formatoMoneda = (valor) => {
                    return '$' + parseFloat(valor).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                };

                if (pago.esRedondeado) {
                    detalle = `Redondeado a múltiplo de 5`;
                    rowClass = 'rounded-row';
                }
                if (Math.abs(pago.ajuste) > 0.01) {
                    detalle += detalle ? '<br>' : '';
                    detalle += `<span class="adjustment-detail">(+${formatoMoneda(pago.ajuste)} al capital)</span>`;
                }
                if (pago.infoFecha) {
                    detalle += detalle ? '<br>' : '';
                    detalle += `<span class="date-adjustment">Ajuste de fecha: día ${pago.infoFecha.diaOriginal} no disponible</span>`;
                }

                tablaHTML += `
        <tr class="${rowClass}">
            <td>${pago.numero}</td>
            <td>${pago.fecha}</td>
            <td class='text-right'>${formatoMoneda(pago.capital)}</td>
            <td class='text-right'>${formatoMoneda(pago.interes)}</td>
            <td class='text-right'>${formatoMoneda(pago.total)}</td>
            <td class='text-right'>${pago.tipo}</td>
            <td class='text-right'>${formatoMoneda(pago.saldo)}</td>
           
        </tr>
    `;
            });

            tablaHTML += '</tbody></table>';

            // Mostrar resultados
            document.getElementById('paymentTable').innerHTML = tablaHTML;
            document.getElementById('totalCapital').textContent = totales.capital.toFixed(2);
            document.getElementById('totalIntereses').textContent = totales.intereses.toFixed(2);
            document.getElementById('totalPagar').textContent = totales.total.toFixed(2);
            document.getElementById('cat').textContent = calcularCAT();
            document.getElementById('results').style.display = 'block';

            // Scroll a los resultados
            document.getElementById('results').scrollIntoView({
                behavior: 'smooth'
            });
        }

        // Validar enganche en tiempo real
        document.getElementById('montoEnganche').addEventListener('input', function() {
            const montoTotal = parseFloat(document.getElementById('montoTotal').value) || 0;
            const montoEnganche = parseFloat(this.value) || 0;

            if (montoEnganche > montoTotal) {
                document.getElementById('engancheError').textContent = 'El enganche no puede ser mayor al monto total';
            } else {
                document.getElementById('engancheError').textContent = '';
            }
        });

        // Establecer fecha mínima como hoy
        document.getElementById('fechaInicio').min = new Date().toISOString().split('T')[0];

        // Calcular automáticamente cuando se cambian valores clave
        const inputsCalculo = [
            'montoTotal', 'montoEnganche', 'plazosEnganche',
            'plazosSinInteres', 'plazosConInteres', 'tasaInteresAnual'
        ];

        inputsCalculo.forEach(id => {
            document.getElementById(id).addEventListener('change', function() {
                if (document.getElementById('results').style.display === 'block') {
                    calcularCorrida();
                }
            });
        });
    </script>
</body>

</html>