$(document).ready(function () {
  $("#btnCalcular").click(function () {
    calcularCorrida();
  });

  // 1. Abrir modal al hacer clic en el botón de búsqueda
  $("#btnBuscar").click(function () {
    // Limpiar filtros y tabla al abrir el modal
    $("#bproyecto").val("").trigger("change");
    $("#tablaLote").DataTable().clear().draw();
    $("#modalLote").modal("show");
    if ($("#bproyecto option").length > 1) {
      $("#bproyecto").prop("selectedIndex", 1).trigger("change");
    }
  });

  // 2. Inicializar DataTable para la tabla de lotes
  var table = $("#tablaLote").DataTable({
    columns: [
      { data: "id_lote", visible: false },
      { data: "clave_lote" },
      {
        data: "superficie",
        className: "text-right",
        render: function (data) {
          return parseFloat(data).toFixed(2) + " m²";
        },
      },
      {
        data: "preciom",
        className: "text-right",
        render: function (data) {
          return parseFloat(data).toLocaleString("es-MX", {
            style: "currency",
            currency: "MXN",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          });
        },
      },
      {
        data: "valortotal",
        className: "text-right",
        render: function (data) {
          return parseFloat(data).toLocaleString("es-MX", {
            style: "currency",
            currency: "MXN",
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
          });
        },
      },
      {
        data: "status",
        render: function (data) {
          const statusMap = {
            DISPONIBLE: "badge-success",
            APARTADO: "badge-warning",
            VENDIDO: "badge-danger",
          };

          const status = data ? data.toUpperCase() : "";
          const badgeClass = statusMap[status] || "badge-secondary";

          return `<span class="badge ${badgeClass}">${data}</span>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return `<button class="btn btn-sm btn-primary seleccionar-lote" 
                            data-id="${row.id_lote}" 
                            data-clave="${row.clave_lote}"
                            data-superficie="${row.superficie}"
                            data-preciom="${row.preciom}"
                            data-valortotal="${row.valortotal}">
                            <i class="fas fa-check mr-1"></i> Seleccionar
                        </button>`;
        },
        orderable: false,
      },
    ],
    dom: '<"top"f>rt<"bottom"lip><"clear">',
    responsive: true,
  });

  // 3. Cargar manzanas cuando cambia el proyecto
  $("#bproyecto").change(function () {
    var idProyecto = $(this).val();
    var selectManzana = $("#bmanzana");

    if (!idProyecto) {
      selectManzana
        .empty()
        .append('<option value="">-- Seleccione Manzana --</option>')
        .prop("disabled", true);
      table.clear().draw();
      return;
    }

    $.ajax({
      url: "bd/get_manzanas.php",
      method: "POST",
      data: { id_proy: idProyecto },
      dataType: "json",
      success: function (response) {
        selectManzana.empty().prop("disabled", false);
        if (response.length > 0) {
          $.each(response, function (index, manzana) {
            selectManzana.append(
              `<option value="${manzana.id_man}">${manzana.descripcion}</option>`
            );
            selectManzana.prop("selectedIndex", 0).trigger("change");
          });
        } else {
          selectManzana.append(
            '<option value="">-- No hay manzanas --</option>'
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar manzanas:", error);
        selectManzana
          .empty()
          .append('<option value="">Error al cargar</option>');
      },
    });
  });

  // 4. Cargar lotes cuando cambia la manzana
  $("#bmanzana").change(function () {
    var idProyecto = $("#bproyecto").val();
    var idManzana = $(this).val();

    if (!idProyecto) return;

    $.ajax({
      url: "bd/get_lotes.php",
      method: "POST",
      data: {
        id_proy: idProyecto,
        id_man: idManzana,
      },
      dataType: "json",
      beforeSend: function () {
        // Mostrar loading en la tabla
        table.clear().draw();
        $("#tablaLote tbody").html(
          '<tr><td colspan="7" class="text-center">' +
            '<i class="fas fa-spinner fa-spin mr-2"></i>Cargando lotes...</td></tr>'
        );
      },
      success: function (response) {
        if (response.length > 0) {
          table.clear().rows.add(response).draw();
        } else {
          table.clear().draw();
          $("#tablaLote tbody").html(
            '<tr><td colspan="7" class="text-center">No se encontraron lotes</td></tr>'
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar lotes:", error);
        $("#tablaLote tbody").html(
          '<tr><td colspan="7" class="text-center text-danger">' +
            '<i class="fas fa-exclamation-triangle mr-2"></i>Error al cargar los datos</td></tr>'
        );
      },
    });
  });

  // 5. Seleccionar lote y cerrar modal
  $("#tablaLote").on("click", ".seleccionar-lote", function () {
    var row = $("#tablaLote").DataTable().row($(this).closest("tr")).data();

    if (row.status.toUpperCase() !== "DISPONIBLE") {
      Swal.fire({
        icon: "warning",
        title: "Lote no disponible",
        text: `El lote seleccionado está ${row.status.toLowerCase()}. Por favor, elige uno disponible.`,
      });
      return;
    }

    var loteData = {
      id: $(this).data("id"),
      clave: $(this).data("clave"),
      superficie: $(this).data("superficie"),
      preciom: $(this).data("preciom"),
      valortotal: $(this).data("valortotal"),
    };

    var proyectoTexto = $("#bproyecto option:selected").text();
    var manzanaTexto = $("#bmanzana option:selected").text();

    // Llenar campos
    $("#id_lote").val(loteData.id);
    $("#clave_lote").val(loteData.clave);
    $("#superficie").val(loteData.superficie);
    $("#precio_m2").val(loteData.preciom);
    $("#montoTotal").val(loteData.valortotal);
    $("#valorop").val(loteData.valortotal);
    $("#proyecto").val(proyectoTexto);
    $("#manzana").val(manzanaTexto);
    $("#lote").val(loteData.clave);
    $("#id_proyecto").val($("#bproyecto").val());
    $("#id_manzana").val($("#bmanzana").val());

    $("#modalLote").modal("hide");
  });

  // 6. Limpiar filtros al cerrar el modal
  $("#modalLote").on("hidden.bs.modal", function () {
    $("#bproyecto").val("").trigger("change");
  });

  // Función para redondear al múltiplo de 5 más cercano
  function redondearA5(monto) {
    return Math.round(monto / 5) * 5;
  }

  // Función para obtener la fecha de pago ajustada
  function obtenerFechaPago(fechaBase, mesesAdicionales) {
    let fecha = new Date(fechaBase);
    let diaOriginal = fecha.getDate();

    let year = fecha.getFullYear();
    let month = fecha.getMonth();

    month += mesesAdicionales-1;
    year += Math.floor(month / 12);
    month = month % 12;

    let nuevaFecha = new Date(year, month, 1);
    let ultimoDiaMes = new Date(year, month + 1, 0).getDate();
    let diaUsar = Math.min(diaOriginal, ultimoDiaMes);

    nuevaFecha.setDate(diaUsar);

    return {
      fecha: nuevaFecha,
      ajustada: diaUsar !== diaOriginal,
      diaOriginal: diaOriginal,
    };
  }

  // Función para formatear fecha como dd/mm/aaaa
  function formatearFecha(fecha) {
    let dia = fecha.getDate().toString().padStart(2, "0");
    let mes = (fecha.getMonth() + 1).toString().padStart(2, "0");
    let año = fecha.getFullYear();
    return `${dia}/${mes}/${año}`;
  }

  // Función para calcular descuentos
  function calcularDescuentos() {
    const montoTotal =
      parseFloat(document.getElementById("montoTotal").value) || 0;
    const descuentoInput = document.getElementById("descuento");
    const descuentoPorInput = document.getElementById("descuentopor");
    const valorOpInput = document.getElementById("valorop");

    // Determinar qué campo se está editando
    const activeElement = document.activeElement.id;

    if (activeElement === "descuentopor") {
      // Si se edita el porcentaje
      const porcentaje = parseFloat(descuentoPorInput.value) || 0;
      const descuento = montoTotal * (porcentaje / 100);

      descuentoInput.value = descuento.toFixed(2);
      valorOpInput.value = (montoTotal - descuento).toFixed(2);
    } else if (activeElement === "descuento") {
      // Si se edita el monto de descuento
      const descuento = parseFloat(descuentoInput.value) || 0;
      const porcentaje = (descuento / montoTotal) * 100;

      descuentoPorInput.value = porcentaje.toFixed(2);
      valorOpInput.value = (montoTotal - descuento).toFixed(2);
    }

    // Actualizar el cálculo de la corrida si ya existe
    if (typeof calcularCorrida === "function") {
      calcularCorrida();
    }
  }

  // Event listeners para los campos de descuento
  document
    .getElementById("descuento")
    .addEventListener("input", calcularDescuentos);
  document
    .getElementById("descuentopor")
    .addEventListener("input", calcularDescuentos);
    

  // Función para calcular la corrida financiera
 function calcularCorrida() {
    // Obtener y convertir valores del formulario
    let folio = document.getElementById("folio").value;
    let fechaInicio = new Date(document.getElementById("fechaInicio").value);
    let montoTotal = parseCurrency(document.getElementById("montoTotal").value);
    let valorOp = parseCurrency(document.getElementById("valorop").value) || montoTotal;
    let montoEnganche = parseCurrency(document.getElementById("montoEnganche").value);
    let plazosEnganche = parseInt(document.getElementById("plazosEnganche").value) || 0;
    let plazosSinInteres = parseInt(document.getElementById("plazosSinInteres").value) || 0;
    let plazosConInteres = parseInt(document.getElementById("plazosConInteres").value) || 0;
    let tasaInteresAnual = parseCurrency(document.getElementById("tasaInteresAnual").value) / 100;
    let tasaInteresMensual = tasaInteresAnual / 12;

    // Validar que el enganche no sea mayor al valor operación
    if (montoEnganche > valorOp) {
        document.getElementById("engancheError").textContent = 
            "El enganche no puede ser mayor al importe total";
        return;
    } else {
        document.getElementById("engancheError").textContent = "";
    }

    // Inicializar variables
    let saldoInsoluto = valorOp - montoEnganche;
    let saldoEnganche = montoEnganche;
    let tablaPagos = [];
    let totales = {
        capital: 0,
        intereses: 0,
        total: 0,
    };
    let capitalPendiente = valorOp - montoEnganche;

    // Función para agregar un pago a la tabla
    const agregarPago = (numero, fecha, capital, interes, total, tipo, saldo, esAjuste = false, ajuste = 0, infoFecha = null) => {
        const pago = {
            numero,
            fecha: formatearFecha(fecha.fecha),
            capital: capital,
            interes: interes,
            total: total,
            tipo,
            saldo: saldo,
            esAjuste,
            ajuste: ajuste,
            infoFecha,
        };

        tablaPagos.push(pago);

        totales.capital += capital;
        totales.intereses += interes;
        totales.total += total;

        return pago;
    };

    // 1. PROCESAR PAGOS DE ENGANCHE
    if (plazosEnganche > 0 && saldoEnganche > 0) {
        let montoBaseEnganche = saldoEnganche / plazosEnganche;

        for (let i = 0; i < plazosEnganche; i++) {
            let montoPago;
            let esUltimoEnganche = i === plazosEnganche - 1;

            if (esUltimoEnganche) {
                montoPago = saldoEnganche;
            } else {
                montoPago = Math.round(montoBaseEnganche);
                montoPago = Math.min(montoPago, saldoEnganche);
            }

            let fechaPago = obtenerFechaPago(fechaInicio, i + 1);
            agregarPago(
                i + 1,
                fechaPago,
                montoPago,
                0,
                montoPago,
                "ENGANCHE",
                saldoEnganche - montoPago + saldoInsoluto,
                false,
                0,
                fechaPago.ajustada ? { diaOriginal: fechaPago.diaOriginal } : null
            );
            saldoEnganche -= montoPago;
        }
    }

    // 2. PROCESAR PAGOS NORMALES
    if (saldoInsoluto > 0) {
        const totalPlazos = plazosConInteres + plazosSinInteres;

        if (totalPlazos > 0) {
            let montoBaseCapital = saldoInsoluto / totalPlazos;
            let montoCapitalEntero = Math.round(montoBaseCapital);
            const puntoCambioInteres = plazosSinInteres;

            for (let i = 0; i < totalPlazos; i++) {
                const esUltimoPago = i === totalPlazos - 1;
                const conInteres = i >= puntoCambioInteres;
                let montoCapital;

                if (esUltimoPago) {
                    montoCapital = saldoInsoluto;
                } else {
                    montoCapital = Math.min(montoCapitalEntero, saldoInsoluto);
                }

                let interes = conInteres ? saldoInsoluto * tasaInteresMensual : 0;
                let interesRedondeado = Math.round(interes);
                let totalPago = montoCapital + interesRedondeado;

                saldoInsoluto -= montoCapital;

                let mesesAdicionales = plazosEnganche + i + 1;
                let fechaPago = obtenerFechaPago(fechaInicio, mesesAdicionales);

                const tipoPago = conInteres ? "CON INTERES" : "SIN INTERES";
                agregarPago(
                    (plazosEnganche > 0 ? plazosEnganche : 0) + i + 1,
                    fechaPago,
                    montoCapital,
                    interesRedondeado,
                    totalPago,
                    tipoPago,
                    saldoInsoluto,
                    esUltimoPago && montoCapital !== montoCapitalEntero,
                    esUltimoPago ? saldoInsoluto + montoCapital - montoCapitalEntero : 0,
                    fechaPago.ajustada ? { diaOriginal: fechaPago.diaOriginal } : null
                );
            }
        }
    }

    // Verificar que el saldo final sea cero
    if (Math.abs(saldoInsoluto) > 0.01) {
        console.warn("Atención: Saldo residual detectado", saldoInsoluto);

        if (tablaPagos.length > 0) {
            let ultimoPago = tablaPagos[tablaPagos.length - 1];
            let ajuste = saldoInsoluto;

            ultimoPago.capital += ajuste;
            ultimoPago.total += ajuste;
            ultimoPago.saldo = 0;
            ultimoPago.esAjuste = true;
            ultimoPago.ajuste = ajuste;

            totales.capital += ajuste;
            totales.total += ajuste;

            saldoInsoluto = 0;
        }
    }

    // Generar tabla HTML
    let tablaHTML = '<table id="tablaPagos" class="table table-striped table-bordered table-hover table-sm table-condensed">';
    tablaHTML += `
        <thead class="bg-green">
            <tr>
                <th>No. Pago</th>
                <th>Fecha</th>
                <th>Capital</th>
                <th>Interés</th>
                <th>Total Pago</th>
                <th>Tipo</th>
                <th>Saldo Insoluto</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    `;

    tablaPagos.forEach((pago) => {
        let detalle = "";
        let rowClass = "";

        if (pago.esRedondeado) {
            detalle = `Redondeado a múltiplo de 5`;
            rowClass = "rounded-row";
        }
        if (Math.abs(pago.ajuste) > 0.01) {
            detalle += detalle ? "<br>" : "";
            detalle += `<span class="adjustment-detail">(+${formatCurrency(pago.ajuste)} al capital)</span>`;
        }
        if (pago.infoFecha) {
            detalle += detalle ? "<br>" : "";
            detalle += `<span class="date-adjustment">Ajuste de fecha: día ${pago.infoFecha.diaOriginal} no disponible</span>`;
        }

        tablaHTML += `
            <tr class="${rowClass}">
                <td>${pago.numero}</td>
                <td>${pago.fecha}</td>
                <td class='text-right'>${formatCurrency(pago.capital)}</td>
                <td class='text-right'>${formatCurrency(pago.interes)}</td>
                <td class='text-right'>${formatCurrency(pago.total)}</td>
                <td>${pago.tipo}</td>
                <td class='text-right'>${formatCurrency(pago.saldo)}</td>
                <td class="text-center">
                    <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' title='Editar'>
                        <i class='fas fa-edit'></i>
                    </button>
                </td>
            </tr>
        `;
    });

    tablaHTML += "</tbody></table>";

    // Mostrar resultados
    document.getElementById("paymentTable").innerHTML = tablaHTML;

    // Destruir DataTable si ya existe
    if ($.fn.DataTable.isDataTable("#tablaPagos")) {
        $("#tablaPagos").DataTable().destroy();
    }

    // Inicializar DataTables
    $("#tablaPagos").DataTable({
        scrollY: "400px",
        scrollCollapse: true,
        paging: false,
        autoWidth: false,
        ordering: true,
        info: false,
        searching: false,
        ordering: false,
        dom: "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-6'i>>",
        columnDefs: [
            {
                targets: [2, 3, 4, 6],
                className: "dt-body-right",
                render: function(data, type, row) {
                    if (type === 'display') {
                        return formatCurrency(parseFloat(data));
                    }
                    return data;
                }
            },
            {
                targets: -1,
                orderable: false,
                searchable: false,
            },
        ],
        language: {
            lengthMenu: "Mostrar _MENU_ registros",
            zeroRecords: "No se encontraron resultados",
            info: "Mostrando _TOTAL_ registros",
            infoEmpty: "Mostrando 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
            },
            sProcessing: "Procesando...",
        },
    });

    // Actualizar totales
    $("#totalCapital").val(formatCurrency(totales.capital));
    $("#totalIntereses").val(formatCurrency(totales.intereses));
    $("#totalPagar").val(formatCurrency(totales.total));

    document.getElementById("results").style.display = "block";
    document.getElementById("results").scrollIntoView({ behavior: "smooth" });
}
  // Modal de edición de pago
  $("body").append(`
  <div class="modal fade" id="modalEditarPago" tabindex="-1" role="dialog" aria-labelledby="modalEditarPagoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalEditarPagoLabel">Editar Pago</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formEditarPago">
            <input type="hidden" id="editarNumeroPago">
            <div class="form-group">
              <label for="editarMontoCapital">Monto a Capital</label>
              <input type="number" class="form-control" id="editarMontoCapital" step="0.01" min="0" required>
            </div>
            <div class="form-group">
              <label for="editarFechaPago">Nueva Fecha de Pago</label>
              <input type="date" class="form-control" id="editarFechaPago" required>
            </div>
            <div class="form-group form-check">
              <input type="checkbox" class="form-check-input" id="editarAjustarFechas">
              <label class="form-check-label" for="editarAjustarFechas">Ajustar todas las fechas posteriores</label>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" id="btnGuardarCambiosPago">Guardar Cambios</button>
        </div>
      </div>
    </div>
  </div>
  `);

  // Manejador para el botón editar
  $(document).on("click", ".btnEditar", function () {
    const row = $(this).closest("tr");
    const numeroPago = parseInt(row.find("td:eq(0)").text());
    const montoCapital = parseFloat(
      row
        .find("td:eq(2)")
        .text()
        .replace(/[^0-9.-]+/g, "")
    );
    const fechaPago = row.find("td:eq(1)").text();

    // Convertir fecha dd/mm/aaaa a aaaa-mm-dd para el input date
    const [dia, mes, anio] = fechaPago.split("/");
    const fechaFormatoInput = `${anio}-${mes.padStart(2, "0")}-${dia.padStart(
      2,
      "0"
    )}`;

    // Llenar el modal
    $("#editarNumeroPago").val(numeroPago);
    $("#editarMontoCapital").val(montoCapital.toFixed(2));
    $("#editarFechaPago").val(fechaFormatoInput);
    $("#editarAjustarFechas").prop("checked", false);

    // Mostrar modal
    $("#modalEditarPago").modal("show");
  });

  // Función para obtener los pagos actuales de la tabla
  function obtenerPagosActuales() {
    const pagos = [];
    $("#tablaPagos tbody tr").each(function () {
      const $row = $(this);
      pagos.push({
        numero: parseInt($row.find("td:eq(0)").text()),
        fecha: $row.find("td:eq(1)").text(),
        capital: parseFloat(
          $row
            .find("td:eq(2)")
            .text()
            .replace(/[^0-9.-]+/g, "")
        ),
        interes: parseFloat(
          $row
            .find("td:eq(3)")
            .text()
            .replace(/[^0-9.-]+/g, "")
        ),
        total: parseFloat(
          $row
            .find("td:eq(4)")
            .text()
            .replace(/[^0-9.-]+/g, "")
        ),
        tipo: $row.find("td:eq(5)").text(),
        saldo: parseFloat(
          $row
            .find("td:eq(6)")
            .text()
            .replace(/[^0-9.-]+/g, "")
        ),
      });
    });
    return pagos;
  }

  // Función para recalcular la corrida con los cambios (versión definitiva)
  // Función mejorada para recalcular la corrida con los cambios

  // Función corregida para recalcular la corrida con los cambios
function recalcularCorridaConCambios(numeroPago, nuevoCapital, nuevaFecha, ajustarFechas) {
    try {
        // 1. Obtener parámetros básicos
        const tasaInteresAnual = parseFloat($("#tasaInteresAnual").val()) / 100;
        const tasaInteresMensual = tasaInteresAnual / 12;
        const montoTotal = parseFloat($("#montoTotal").val());
        const enganche = parseFloat($("#montoEnganche").val()) || 0;
        
        // 2. Obtener pagos actuales
        const pagosActuales = obtenerPagosActuales();
        const pagoEditado = pagosActuales.find(p => p.numero === numeroPago);
        
        if (!pagoEditado) throw new Error("Pago a editar no encontrado");

        // 3. Inicializar variables
        const nuevosPagos = [];
        let fechaBase = new Date(nuevaFecha);
        let saldoInsoluto = montoTotal - enganche;
        let saldoCubierto = false;

        // 4. Procesar cada pago en orden
        for (let i = 0; i < pagosActuales.length; i++) {
            const pagoActual = pagosActuales[i];
            
            // Si el saldo ya está cubierto, marcar como completado
            if (saldoCubierto) {
                nuevosPagos.push({
                    ...pagoActual,
                    capital: 0,
                    interes: 0,
                    total: 0,
                    saldo: 0,
                    tipo: "COMPLETADO"
                });
                continue;
            }

            // Calcular saldo anterior (excepto para el primer pago)
            if (i > 0) {
                saldoInsoluto = nuevosPagos[i-1].saldo;
            }

            // Si es el pago editado, aplicar el nuevo capital
            if (pagoActual.numero === numeroPago) {
                const interes = (pagoActual.tipo === "CON INTERES") 
                    ? redondearA5(saldoInsoluto * tasaInteresMensual) 
                    : 0;
                
                // Asegurar que el capital no exceda el saldo
                const capital = Math.min(nuevoCapital, saldoInsoluto);
                const nuevoSaldo = saldoInsoluto - capital;
                
                nuevosPagos.push({
                    ...pagoActual,
                    capital: capital,
                    interes: interes,
                    total: capital + interes,
                    saldo: nuevoSaldo,
                    fecha: ajustarFechas ? formatearFecha(fechaBase) : pagoActual.fecha,
                    editado: true
                });

                saldoInsoluto = nuevoSaldo;
                if (nuevoSaldo <= 0) saldoCubierto = true;
            } 
            else {
                // Para pagos no editados
                let capital = pagoActual.capital;
                let interes = 0;
                
                // Calcular interés si corresponde
                if (pagoActual.tipo === "CON INTERES") {
                    interes = redondearA5(saldoInsoluto * tasaInteresMensual);
                }
                
                // Ajustar capital si el saldo es menor
                if (saldoInsoluto < capital) {
                    capital = saldoInsoluto;
                }
                
                const nuevoPago = {
                    ...pagoActual,
                    capital: capital,
                    interes: interes,
                    total: capital + interes,
                    saldo: saldoInsoluto - capital
                };
                
                nuevosPagos.push(nuevoPago);
                saldoInsoluto = nuevoPago.saldo;
                
                // Verificar si el saldo se cubrió
                if (nuevoPago.saldo <= 0) {
                    saldoCubierto = true;
                    // Ajustar por posibles redondeos
                    if (nuevoPago.saldo < 0) {
                        nuevoPago.capital += nuevoPago.saldo;
                        nuevoPago.total = nuevoPago.capital + nuevoPago.interes;
                        nuevoPago.saldo = 0;
                    }
                }
            }
        }

        // 5. Verificación final
        const ultimoPagoConSaldo = nuevosPagos.findLast(p => p.saldo > 0);
        if (ultimoPagoConSaldo && Math.abs(ultimoPagoConSaldo.saldo) > 0.01) {
            console.warn("Quedó saldo remanente:", ultimoPagoConSaldo.saldo);
            // Ajustar en el último pago con capital
            for (let j = nuevosPagos.length - 1; j >= 0; j--) {
                if (nuevosPagos[j].capital > 0) {
                    nuevosPagos[j].capital += nuevosPagos[j].saldo;
                    nuevosPagos[j].total = nuevosPagos[j].capital + nuevosPagos[j].interes;
                    nuevosPagos[j].saldo = 0;
                    break;
                }
            }
        }

        // 6. Actualizar la tabla
        actualizarTablaPagos(nuevosPagos);
        return true;
    } catch (error) {
        console.error("Error en recalcularCorridaConCambios:", error);
        Swal.fire("Error", `No se pudo aplicar los cambios: ${error.message}`, "error");
        return false;
    }
}

  // Función para actualizar la tabla de pagos
  function actualizarTablaPagos(pagos) {
    // Destruir DataTable existente
    if ($.fn.DataTable.isDataTable("#tablaPagos")) {
      $("#tablaPagos").DataTable().destroy();
    }

    // Generar nuevo HTML
    let tablaHTML =
      '<table id="tablaPagos" class="table table-striped table-bordered table-hover table-sm table-condensed">';
    tablaHTML += `
      <thead class="bg-green">
        <tr>
          <th>No. Pago</th>
          <th>Fecha</th>
          <th>Capital</th>
          <th>Interés</th>
          <th>Total Pago</th>
          <th>Tipo</th>
          <th>Saldo Insoluto</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
    `;

    const formatoMoneda = (valor) => {
      return (
        "$" +
        parseFloat(valor)
          .toFixed(2)
          .replace(/\d(?=(\d{3})+\.)/g, "$&,")
      );
    };

    // Determinar si el saldo ya se cubrió completamente
    let saldoCubierto = false;

    pagos.forEach((pago, index) => {
      // Verificar si el saldo ya está cubierto
      if (!saldoCubierto && parseFloat(pago.saldo) <= 0 && index > 0) {
        saldoCubierto = true;
      }

      // Clases CSS para estilos especiales
      const clasesFila = [];
      if (pago.editado) clasesFila.push("pago-editado");
      if (saldoCubierto) clasesFila.push("pago-completado");

      // Valores a mostrar (cero si el saldo ya está cubierto)
      const capital = saldoCubierto ? 0 : pago.capital;
      const interes = saldoCubierto ? 0 : pago.interes;
      const total = saldoCubierto ? 0 : pago.total;
      const saldo = saldoCubierto ? 0 : pago.saldo;
      const tipo = saldoCubierto ? "COMPLETADO" : pago.tipo;

      tablaHTML += `
        <tr class="${clasesFila.join(" ")}">
          <td>${pago.numero}</td>
          <td>${pago.fecha}</td>
          <td class='text-right'>${formatoMoneda(capital)}</td>
          <td class='text-right'>${formatoMoneda(interes)}</td>
          <td class='text-right'>${formatoMoneda(total)}</td>
          <td>${tipo}</td>
          <td class='text-right'>${formatoMoneda(saldo)}</td>
          <td class="text-center">
            <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' title='Editar'>
              <i class='fas fa-edit'></i>
            </button>
          </td>
        </tr>
      `;
    });

    tablaHTML += "</tbody></table>";

    // Reemplazar la tabla
    document.getElementById("paymentTable").innerHTML = tablaHTML;

    // Agregar estilos CSS dinámicamente
    $("head").append(`
      <style>
        .pago-editado {
          background-color: #e3f2fd !important;
          font-weight: bold;
        }
        .pago-completado {
          color: #888 !important;
          font-style: italic;
        }
        .pago-completado td {
          text-decoration: line-through;
        }
      </style>
    `);

    // Re-inicializar DataTable
    $("#tablaPagos").DataTable({
      scrollY: "400px",
      scrollCollapse: true,
      paging: false,
      autoWidth: false,
      ordering: true,
      info: false,
      searching: false,
      ordering: false,
      dom:
        "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-6'i>>",
      columnDefs: [
        {
          targets: [2, 3, 4, 6],
          className: "dt-body-right",
          render: $.fn.dataTable.render.number(",", ".", 2, "$"),
        },
        {
          targets: -1,
          orderable: false,
          searchable: false,
        },
      ],
      language: {
        lengthMenu: "Mostrar _MENU_ registros",
        zeroRecords: "No se encontraron resultados",
        info: "Mostrando _TOTAL_ registros",
        infoEmpty: "Mostrando 0 registros",
        infoFiltered: "(filtrado de un total de _MAX_ registros)",
        sSearch: "Buscar:",
        oPaginate: {
          sFirst: "Primero",
          sLast: "Último",
          sNext: "Siguiente",
          sPrevious: "Anterior",
        },
        sProcessing: "Procesando...",
      },
    });

    // Actualizar totales (solo pagos no completados)
    const pagosValidos = pagos.filter((p) => !p.completado);
    const totales = pagosValidos.reduce(
      (acc, pago) => {
        acc.capital += parseFloat(pago.capital);
        acc.intereses += parseFloat(pago.interes);
        acc.total += parseFloat(pago.total);
        return acc;
      },
      { capital: 0, intereses: 0, total: 0 }
    );

    $("#totalCapital").val(
      totales.capital.toLocaleString("es-MX", {
        style: "currency",
        currency: "MXN",
      })
    );

    $("#totalIntereses").val(
      totales.intereses.toLocaleString("es-MX", {
        style: "currency",
        currency: "MXN",
      })
    );

    $("#totalPagar").val(
      totales.total.toLocaleString("es-MX", {
        style: "currency",
        currency: "MXN",
      })
    );
  }

  // Manejador para guardar los cambios del pago
  $("#btnGuardarCambiosPago").click(function () {
    const numeroPago = parseInt($("#editarNumeroPago").val());
    const nuevoCapital = parseFloat($("#editarMontoCapital").val());
    const nuevaFecha = $("#editarFechaPago").val();
    const ajustarFechas = $("#editarAjustarFechas").is(":checked");

    // Validaciones
    if (isNaN(nuevoCapital)) {
      Swal.fire(
        "Error",
        "El monto a capital debe ser un número válido",
        "error"
      );
      return;
    }

    if (nuevoCapital <= 0) {
      Swal.fire("Error", "El monto a capital debe ser mayor a cero", "error");
      return;
    }

    if (!nuevaFecha) {
      Swal.fire("Error", "Debe seleccionar una fecha válida", "error");
      return;
    }

    // Recalcular la corrida con los cambios
    const exito = recalcularCorridaConCambios(
      numeroPago,
      nuevoCapital,
      nuevaFecha,
      ajustarFechas
    );

    if (exito) {
      // Cerrar modal
      $("#modalEditarPago").modal("hide");

      Swal.fire({
        icon: "success",
        title: "Cambios guardados",
        text: "Los cambios al pago se han aplicado correctamente",
        timer: 2000,
        showConfirmButton: false,
      });
    } else {
      Swal.fire("Error", "No se pudo aplicar los cambios al pago", "error");
    }
  });

  // Validar enganche en tiempo real
  document
    .getElementById("montoEnganche")
    .addEventListener("input", function () {
      const montoTotal =
        parseFloat(document.getElementById("montoTotal").value) || 0;
      const montoEnganche = parseFloat(this.value) || 0;

      if (montoEnganche > montoTotal) {
        document.getElementById("engancheError").textContent =
          "El enganche no puede ser mayor al monto total";
      } else {
        document.getElementById("engancheError").textContent = "";
      }
    });

  // Establecer fecha mínima como hoy
  document.getElementById("fechaInicio").min = new Date()
    .toISOString()
    .split("T")[0];

  // Calcular automáticamente cuando se cambian valores clave
  const inputsCalculo = [
    "montoTotal",
    "montoEnganche",
    "plazosEnganche",
    "plazosSinInteres",
    "plazosConInteres",
    "tasaInteresAnual",
  ];

  inputsCalculo.forEach((id) => {
    document.getElementById(id).addEventListener("change", function () {
      if (document.getElementById("results").style.display === "block") {
        calcularCorrida();
      }
    });
  });

  // Inicializar tooltips
  $('[data-toggle="tooltip"]').tooltip();
});
