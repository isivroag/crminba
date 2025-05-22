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
      return; // Salir de la función si el lote no está disponible
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

  // Función para obtener la fecha de pago ajustada CORREGIDA
  function obtenerFechaPago(fechaBase, mesesAdicionales) {
    let fecha = new Date(fechaBase);
    let diaOriginal = fecha.getDate();

    let year = fecha.getFullYear();
    let month = fecha.getMonth();

    month += mesesAdicionales;
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

  function calcularCorrida() {
    // Obtener valores del formulario
    let folio = document.getElementById("folio").value;
    let fechaInicio = new Date(document.getElementById("fechaInicio").value);
    let montoTotal = parseFloat(document.getElementById("montoTotal").value);
    let montoEnganche = parseFloat(
      document.getElementById("montoEnganche").value
    );
    let plazosEnganche =
      parseInt(document.getElementById("plazosEnganche").value) || 0;
    let plazosSinInteres =
      parseInt(document.getElementById("plazosSinInteres").value) || 0;
    let plazosConInteres =
      parseInt(document.getElementById("plazosConInteres").value) || 0;
    let tasaInteresAnual =
      parseFloat(document.getElementById("tasaInteresAnual").value) / 100;
    let tasaInteresMensual = tasaInteresAnual / 12;

    // Validar que el enganche no sea mayor al monto total
    if (montoEnganche > montoTotal) {
      document.getElementById("engancheError").textContent =
        "El enganche no puede ser mayor al monto total";
      return;
    } else {
      document.getElementById("engancheError").textContent = "";
    }

    // Inicializar variables
    let saldoInsoluto = montoTotal - montoEnganche;
    let saldoEnganche = montoEnganche;
    let tablaPagos = [];
    let totales = {
      capital: 0,
      intereses: 0,
      total: 0,
    };
    let capitalPendiente = montoTotal - montoEnganche;
    let montoBasePago = 0;

    // Función para agregar un pago a la tabla
    const agregarPago = (
      numero,
      fecha,
      capital,
      interes,
      total,
      tipo,
      saldo,
      esRedondeado = false,
      ajuste = 0,
      infoFecha = null
    ) => {
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
        infoFecha,
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
        const esUltimoPago = i === plazosEnganche - 1;
        let montoPago;

        if (esUltimoPago) {
          montoPago = saldoEnganche;
        } else {
          montoPago = Math.min(montoBaseEnganche, saldoEnganche);
        }

        let montoRedondeado = redondearA5(montoPago);
        let diferencia = montoRedondeado - montoPago;

        if (montoRedondeado > saldoEnganche) {
          montoRedondeado = saldoEnganche;
          diferencia = 0;
        }

        if (diferencia > 0) {
          capitalPendiente += diferencia;
        }

        saldoEnganche -= montoRedondeado;
        let saldoCapital =
          saldoEnganche + saldoInsoluto + (diferencia > 0 ? diferencia : 0);

        let fechaPago = obtenerFechaPago(fechaInicio, i + 1);

        agregarPago(
          i + 1,
          fechaPago,
          montoRedondeado,
          0,
          montoRedondeado,
          "ENGANCHE",
          saldoCapital,
          montoRedondeado !== montoPago,
          diferencia,
          fechaPago.ajustada
            ? {
                diaOriginal: fechaPago.diaOriginal,
              }
            : null
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
          const esUltimoPago = i === totalPlazos - 1;
          const conInteres = i >= puntoCambioInteres;
          let montoPago;

          if (esUltimoPago) {
            montoPago = capitalPendiente;
          } else {
            montoPago = Math.min(montoBasePago, capitalPendiente);
          }

          let montoRedondeado = redondearA5(montoPago);
          let diferencia = montoRedondeado - montoPago;

          if (montoRedondeado > capitalPendiente) {
            montoRedondeado = capitalPendiente;
            diferencia = 0;
          }

          if (!esUltimoPago && diferencia > 0) {
            const pagosRestantes = totalPlazos - i - 1;
            if (pagosRestantes > 0) {
              montoBasePago += diferencia / pagosRestantes;
            }
          }

          let interes = conInteres ? saldoInsoluto * tasaInteresMensual : 0;
          let interesRedondeado = redondearA5(interes);
          let totalPago = montoRedondeado + interesRedondeado;

          capitalPendiente -= montoRedondeado;
          saldoInsoluto -= montoRedondeado;

          let mesesAdicionales = plazosEnganche + i + 1;
          let fechaPago = obtenerFechaPago(fechaInicio, mesesAdicionales);

          const tipoPago = conInteres ? "CON INTERES" : "SIN INTERES";
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
            fechaPago.ajustada
              ? {
                  diaOriginal: fechaPago.diaOriginal,
                }
              : null
          );
        }
      }
    }

    // Calcular CAT aproximado (fórmula simplificada)
    const calcularCAT = () => {
      if (totales.intereses <= 0 || montoTotal <= 0) return 0;

      const plazoTotalMeses =
        plazosEnganche + plazosConInteres + plazosSinInteres;
      if (plazoTotalMeses === 0) return 0;

      const plazoTotalAnios = plazoTotalMeses / 12;
      const catAproximado =
        (Math.pow(1 + totales.intereses / montoTotal, 1 / plazoTotalAnios) -
          1) *
        100;

      return catAproximado.toFixed(2);
    };

    // Generar tabla HTML
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

    // Función para formatear como moneda
    const formatoMoneda = (valor) => {
      return (
        "$" +
        parseFloat(valor)
          .toFixed(2)
          .replace(/\d(?=(\d{3})+\.)/g, "$&,")
      );
    };

    tablaPagos.forEach((pago) => {
      let detalle = "";
      let rowClass = "";

      if (pago.esRedondeado) {
        detalle = `Redondeado a múltiplo de 5`;
        rowClass = "rounded-row";
      }
      if (Math.abs(pago.ajuste) > 0.01) {
        detalle += detalle ? "<br>" : "";
        detalle += `<span class="adjustment-detail">(+${formatoMoneda(
          pago.ajuste
        )} al capital)</span>`;
      }
      if (pago.infoFecha) {
        detalle += detalle ? "<br>" : "";
        detalle += `<span class="date-adjustment">Ajuste de fecha: día ${pago.infoFecha.diaOriginal} no disponible</span>`;
      }

      tablaHTML += `
        <tr class="${rowClass}">
          <td>${pago.numero}</td>
          <td>${pago.fecha}</td>
          <td class='text-right'>${formatoMoneda(pago.capital)}</td>
          <td class='text-right'>${formatoMoneda(pago.interes)}</td>
          <td class='text-right'>${formatoMoneda(pago.total)}</td>
          <td>${pago.tipo}</td>
          <td class='text-right'>${formatoMoneda(pago.saldo)}</td>
          <td class="text-center">
            <button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' title='Editar'>
              <i class='fas fa-edit'></i>
            </button>
            <button class='btn btn-sm btn-success btnLote' data-toggle='tooltip' title='Lotes'>
              <i class='fa-duotone fa-solid fa-layer-group'></i>
            </button>
            <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' title='Eliminar'>
              <i class='fas fa-trash-alt'></i>
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
      dom:
        "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-6'i>>",
      columnDefs: [
        {
          targets: [2, 3, 4, 6], // Columnas numéricas
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

    // Actualizar totales

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

    //$("#cat").val(calcularCAT());

    document.getElementById("results").style.display = "block";

    // Scroll a los resultados
    document.getElementById("results").scrollIntoView({
      behavior: "smooth",
    });
  }

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
