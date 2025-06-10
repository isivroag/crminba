$(document).ready(function () {
  function limpiarFormulario() {
    // Limpiar campos principales
    $("#folio").val("");
    $("#id_prospecto").val("");
    $("#nombre_prospecto").val("");
    $("#fechacot").val(new Date().toISOString().split("T")[0]);
    $("#tasaInteresAnual").val("17.00");

    // Limpiar datos del lote
    $("#id_proyecto").val("");
    $("#proyecto").val("");
    $("#id_manzana").val("");
    $("#manzana").val("");
    $("#id_lote").val("");
    $("#lote").val("");
    $("#fechaInicio").val(new Date().toISOString().split("T")[0]);
    $("#montoTotal").val("0");
    $("#descuento").val("0");
    $("#descuentopor").val("0");
    $("#valorop").val("0");

    // Limpiar datos financieros
    $("#montoEnganche").val("");
    $("#plazosEnganche").val("0");
    $("#plazosSinInteres").val("0");
    $("#plazosConInteres").val("0");

    // Limpiar resultados
    $("#paymentTable").html("");
    $("#totalCapital").val("0");
    $("#totalIntereses").val("0");
    $("#totalPagar").val("0");

    // Ocultar sección de resultados
    $("#results").hide();
  }

  function deshabilitarInputsYCalculo() {
    // Deshabilita todos los inputs, selects y textareas dentro del formulario principal
    $("input, select, textarea").prop("disabled", true);
    // Oculta el botón de calcular
    $("#btnCalcular").hide();
    $("#btnGuardar").hide();
  }

  // Buscar presupuesto por folio al cambiar el input
  $("#folio").on("change", function () {
    const folio = $(this).val().trim();
    if (!folio) return;

    $.ajax({
      url: "bd/buscar_pres.php",
      method: "POST",
      dataType: "json",
      data: { folio: folio },
      success: function (response) {
        
        if (response.success == 0) {
          Swal.fire("No encontrado", response.error, "warning");
          limpiarFormulario();
          $("#folio").val(folio);
          return;
        }
        // Llenar los campos del formulario con los datos recibidos
        $("#folio").val(response.id_pres);
        $("#id_prospecto").val(response.id_pros);
        $("#nombre_prospecto").val(response.nombre_pros);
        $("#fechacot").val(response.fecha_pres);
        $("#tasaInteresAnual").val(response.tasa);
        $("#fechaInicio").val(response.inicial);
        $("#id_lote").val(response.id_lote);
        $("#lote").val(response.nlote);
        $("#id_proyecto").val(response.id_proy);
        $("#proyecto").val(response.nproyecto);
        $("#id_manzana").val(response.id_man);
        $("#manzana").val(response.nmanzana);  
        $("#frente").val(response.frente);
        $("#fondo").val(response.fondo);
        $("#preciom").val(formatCurrency(response.preciom));
        $("#superficie").val(response.superficie);
        $("#tipolote").val(response.tipo);
        $("#valortotal").val(formatCurrency(response.importe));
        $("#montoTotal").val(formatCurrency(response.importe));
        $("#descuento").val(formatCurrency(response.descuento));
        $("#descuentopor").val(response.pordescuento);
        $("#valorop").val(formatCurrency(response.valorop));
        $("#montoEnganche").val(formatCurrency(response.enganche));
        $("#enganchepor").val(response.enganchepor);
        $("#plazosEnganche").val(response.nenganche);
        $("#plazosSinInteres").val(response.nmsi);
        $("#plazosConInteres").val(response.nmci);
        $("#totalCapital").val(formatCurrency(response.totalcapital));
        $("#totalIntereses").val(formatCurrency(response.totalinteres));
        $("#totalPagar").val(formatCurrency(response.totalpagar));


        // Mostrar resultados
        $("#results").show();
        deshabilitarInputsYCalculo();

        // Buscar detalle de pagos
        buscarDetallePresupuesto(folio);
      },
      error: function () {
        console.error("Error al buscar presupuesto:", response);
        Swal.fire("Error", "No se pudo buscar el presupuesto", "error");
      },
    });
  });
   // Disparar el evento change si hay un valor en el input al cargar la página
    const initialFolio = $("#folio").val().trim();
    if (initialFolio) {
        $("#folio").trigger("change");
        
      
        // buscarPresupuesto(initialFolio);
    }

  // Función para buscar y mostrar detalle de pagos
  function buscarDetallePresupuesto(folio) {
    $.ajax({
      url: "bd/buscar_detallepres.php",
      method: "POST",
      dataType: "json",
      data: { folio: folio },
      success: function (response) {
        if (Array.isArray(response) && response.length > 0) {
          // Calcular totales
          
          const totales = calcularTotales(response);
          TablaPagos(response, totales);
          $("#results").show();
         
        } else {
          $("#paymentTable").html("");
          Swal.fire("Sin pagos", "No se encontraron pagos para este folio", "info");
        }
      },
      error: function () {
        console.error("Error al buscar detalle de pagos:", response);
        Swal.fire("Error", "No se pudo buscar el detalle de pagos", "error");
      },
    });
  }

  $("#btnImprimir").click(function () {
    $folio = $("#folio").val();
    window.open('formatos/generarcot.php?id=' + $folio, '_blank');  
  });
  // Botón Guardar
  $("#btnGuardar").click(function () {
    // Validar campos obligatorios

    if ($("#id_prospecto").val() === "") {
      Swal.fire("Error", "Debes seleccionar un prospecto", "error");
      return;
    }

    if ($("#id_lote").val() === "") {
      Swal.fire("Error", "Debes seleccionar un lote", "error");
      return;
    }

    // Validar que haya una corrida calculada
    if ($("#paymentTable").html().trim() === "") {
      Swal.fire(
        "Error",
        "Debes calcular la corrida financiera primero",
        "error"
      );
      return;
    }

    // Recolectar datos del formulario
    const encabezado = {
      folio: $("#folio").val(),
      id_pros: $("#id_prospecto").val(),
      nombre_pros: $("#nombre_prospecto").val(),
      fecha_pres: $("#fechacot").val(),
      tasa: $("#tasaInteresAnual").val(),
      inicial: $("#fechaInicio").val(),
      id_lote: $("#id_lote").val(),
      id_proy: $("#id_proyecto").val(),
      id_man: $("#id_manzana").val(),
       importe: parseFloat($("#montoTotal").val().replace(/,/g, "")),
      descuento: parseFloat($("#descuento").val().replace(/,/g, "")),
      pordescuento: parseFloat($("#descuentopor").val()),
      valorop: parseFloat($("#valorop").val().replace(/,/g, "")),
      enganche: parseFloat($("#montoEnganche").val().replace(/,/g, "")),
      nenganche: $("#plazosEnganche").val(),
      nmsi: $("#plazosSinInteres").val(),
      nmci: $("#plazosConInteres").val(),
      totalcapital: parseCurrency( $("#totalCapital").val().replace(/,/g, "")),
      totalinteres: parseCurrency($("#totalIntereses").val().replace(/,/g, "")),
      totalpagar: parseCurrency($("#totalPagar").val().replace(/,/g, "")),
      descuentopor: parseFloat($("#descuentopor").val()),
      enganchepor: parseFloat($("#enganchepor").val()),

      
    };

    // Mostrar loading
    Swal.fire({
      title: "Guardando cotización",
      html: "Por favor espera...",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

     $.ajax({
        url: 'bd/guardar_presupuesto.php',
        method: 'POST',
        dataType: 'json',
         data: JSON.stringify(encabezado),
        success: function(response) {
          
            if (response.success && response.id_pres) {
                // 2. Si se guardó el encabezado, guardar los detalles
                const detalles = {
                    id_pres: response.id_pres,
                    pagos: obtenerPagosActuales()
                };
                $('#folio').val(response.id_pres);
                
                
                $.ajax({
                    url: 'bd/guardar_detalle_pres.php',
                    method: 'POST',
                    dataType: 'json',
                    data: JSON.stringify(detalles),
                    success: function(detalleResponse) {
                        Swal.close();
                        if (detalleResponse.success) {
                            Swal.fire(
                                '¡Guardado!',
                                'El presupuesto se ha guardado completamente.',
                                'success'
                            );
                            // Opcional: Actualizar folio si es necesario
                            $('#folio').val(response.nuevo_folio || $('#folio').val());
                        } else {
                            Swal.fire('Error', 'Los detalles no se guardaron correctamente', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Error al guardar los detalles', 'error');
                    }
                });
            } else {
                Swal.fire('Error', response.message || 'Error al guardar encabezado', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Error de conexión al guardar', 'error');
            console.error('Error:', xhr.responseText);
        }
    });

    // Enviar datos al servidor
    /*
    $.ajax({
      url: "bd/guardar_cotizacion.php",
      method: "POST",
      dataType: "json",
      data: datosFormulario,
      success: function (response) {
        Swal.close();
        if (response.success) {
          Swal.fire(
            "¡Guardado!",
            "La cotización se ha guardado correctamente.",
            "success"
          );
          // Opcional: Actualizar folio automáticamente
          $("#folio").val(response.nuevo_folio || $("#folio").val());
        } else {
          Swal.fire("Error", response.message || "Error al guardar", "error");
        }
      },
      error: function (xhr, status, error) {
        Swal.close();
        console.error("Error:", error);
        Swal.fire(
          "Error",
          "Ocurrió un error al guardar la cotización",
          "error"
        );
      },
    });
    */
  });

  // Botón Nuevo
  $("#btnNuevo").click(function () {
    Swal.fire({
      title: "¿Nueva cotización?",
      text: "¿Estás seguro que deseas comenzar una nueva cotización? Se perderán los datos no guardados.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Sí, nueva cotización",
      cancelButtonText: "Cancelar",
    }).then((result) => {
      if (result.value) {
        window.location.href = "cot.php"; // Redirigir a la misma página para reiniciar
      }
    });
  });

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

  $("#btnBuscarProspecto").click(function () {
    // Mostrar el modal primero
    $("#modalProspecto").modal("show");
    id_col = $("#idcol").val(); // Obtener el id_col del campo oculto
    console.log("ID de Colaborador:", id_col);
    // Hacer la petición AJAX
    $.ajax({
      url: "bd/get_prospectos.php",
      method: "POST",
      dataType: "json",
      data: {
        id_col: id_col // Puedes enviar parámetros adicionales si es necesario
      },
      success: function (response) {
        if (response && response.length > 0) {
          tablePros.clear().rows.add(response).draw();
        } else {
          tablePros.clear().draw();
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar Prospectos:", error);
      },
    });
  });

  $("#tablaProspecto").on("click", ".seleccionar-prospecto", function () {
    var id = $(this).data("id");
    var nombre = $(this).data("nombre");

    // Asignar valores a los campos correspondientes
    $("#id_prospecto").val(id);
    $("#nombre_prospecto").val(nombre);

    // Cerrar el modal
    $("#modalProspecto").modal("hide");
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
          return parseFloat(data).toFixed(2) ;
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
        data: "frente",
        className: "text-right",
        render: function (data) {
          return parseFloat(data).toFixed(2);
        },
      },
      {
        data: "fondo",
        className: "text-right",
        render: function (data) {
          return parseFloat(data).toFixed(2);
        },
      },
      {
        data: "tipo",
      },
      
      {
        data: null,
        render: function (data, type, row) {
          return `<button class="btn btn-sm btn-primary seleccionar-lote" 
                            data-id="${row.id_lote}" 
                            data-clave="${row.clave_lote}"
                            data-superficie="${row.superficie}"
                            data-preciom="${row.preciom}"
                            data-valortotal="${row.valortotal}"
                            data-status="${row.status}"
                            data-frente="${row.frente}"
                            data-fondo="${row.fondo}"
                            data-tipo="${row.tipo}"><i class="fas fa-check mr-1"></i>
                        </button>`;
        },
        orderable: false,
      },
    ],
    searching:false,
    dom: '<"top"f>rt<"bottom"lip><"clear">',
    responsive: true,
    info: false,
    paging:false,
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
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

  var tablePros = $("#tablaProspecto").DataTable({
    columns: [
      { data: "id_pros", visible: true },
      { data: "nombre" },
      {
        data: null,
        render: function (data, type, row) {
          return `<button class="btn btn-sm btn-primary seleccionar-prospecto" 
                            data-id="${row.id_pros}" 
                            data-nombre="${row.nombre}">
                            <i class="fas fa-check mr-1"></i>
                        </button>`;
        },
        orderable: false,
        className: "text-center",
      },
    ],
    dom: '<"top"f>rt<"bottom"lip><"clear">',
    responsive: true,
    language: {
      lengthMenu: "Mostrar _MENU_ registros",
      zeroRecords: "No se encontraron resultados",
      info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
      infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
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
    deferRender: true,
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
          '<tr><td colspan="10" class="text-center">' +
            '<i class="fas fa-spinner fa-spin mr-2"></i>Cargando lotes...</td></tr>'
        );
      },
      success: function (response) {
        console.log("Lotes cargados:", response);
        if (response.length > 0) {
          table.clear().rows.add(response).draw();
        } else {
          table.clear().draw();
          $("#tablaLote tbody").html(
            '<tr><td colspan="10" class="text-center">No se encontraron lotes</td></tr>'
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar lotes:", error);
        $("#tablaLote tbody").html(
          '<tr><td colspan="10" class="text-center text-danger">' +
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
      status: $(this).data("status"),
      frente: $(this).data("frente"),
      fondo: $(this).data("fondo"),
      tipo: $(this).data("tipo"),

    };

    var proyectoTexto = $("#bproyecto option:selected").text();
    var manzanaTexto = $("#bmanzana option:selected").text();

    // Llenar campos
    $("#id_lote").val(loteData.id);
    $("#clave_lote").val(loteData.clave);
    $("#superficie").val(loteData.superficie);
    $("#preciom").val(formatCurrency(loteData.preciom));
    $("#montoTotal").val(formatCurrency(loteData.valortotal));
    $("#valortotal").val(formatCurrency(loteData.valortotal));

    $("#valorop").val(formatCurrency(loteData.valortotal));

    $("#proyecto").val(proyectoTexto);
    $("#manzana").val(manzanaTexto);
    $("#lote").val(loteData.clave);
    $("#id_proyecto").val($("#bproyecto").val());
    $("#id_manzana").val($("#bmanzana").val());
    
    $("#frente").val(loteData.frente);
    $("#fondo").val(loteData.fondo);
    $("#tipolote").val(loteData.tipo);


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

  // Función obtenerFechaPago con mejor manejo de errores
  function obtenerFechaPago(
    fechaBase,
    mesesAdicionales,
    diaPagoEspecifico = null
  ) {
    try {
      // Validar fecha base
      if (!(fechaBase instanceof Date)) {
        if (typeof fechaBase === "string") {
          // Intentar parsear formato dd/mm/yyyy
          if (fechaBase.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
            const [dia, mes, anio] = fechaBase.split("/");
            fechaBase = new Date(`${anio}-${mes}-${dia}`);
          } else {
            fechaBase = new Date(fechaBase);
          }
        } else {
          throw new Error("Tipo de fecha no soportado");
        }

        if (isNaN(fechaBase.getTime())) {
          throw new Error("Fecha base inválida");
        }
      }

      // Determinar día de pago
      const diaPago =
        diaPagoEspecifico !== null ? diaPagoEspecifico : fechaBase.getDate();

      // Calcular nueva fecha sumando meses
      const year = fechaBase.getFullYear();
      const month = fechaBase.getMonth() + mesesAdicionales;

      // Ajustar año y mes correctamente
      const newYear = year + Math.floor(month / 12);
      const newMonth = month % 12;

      // Obtener último día del mes destino
      const ultimoDiaMes = new Date(newYear, newMonth + 1, 0).getDate();

      // Ajustar día de pago según reglas:
      // 1. Si el día solicitado existe en el mes, usarlo
      // 2. Si no existe:
      //    - Para días 29-31: usar el último día del mes
      //    - Para otros días: usar el último día del mes
      let diaUsar = diaPago;
      let ajustada = false;

      if (diaPago > ultimoDiaMes) {
        diaUsar = ultimoDiaMes;
        ajustada = true;
      }

      // Crear la nueva fecha (a mediodía para evitar problemas horarios)
      const nuevaFecha = new Date(newYear, newMonth, diaUsar, 12, 0, 0);

      return {
        fecha: nuevaFecha,
        ajustada: ajustada,
        diaOriginal: diaPago,
      };
    } catch (error) {
      console.error("Error en obtenerFechaPago:", error);
      // Devolver una fecha por defecto en caso de error
      const fechaDefault = new Date();
      fechaDefault.setHours(12, 0, 0, 0); // Fijar a mediodía para consistencia
      return {
        fecha: fechaDefault,
        ajustada: false,
        diaOriginal: fechaDefault.getDate(),
      };
    }
  }

  // Función para calcular descuentos

  // Event listeners para los campos de descuento
  document
    .getElementById("descuento")
    .addEventListener("input", calcularDescuentos);
  document
    .getElementById("descuentopor")
    .addEventListener("input", calcularDescuentos);

  document
    .getElementById("descuento")
    .addEventListener("blur", handleCurrencyInput);
  document.getElementById("descuento").addEventListener("focus", function () {
    this.value = parseCurrency(this.value).toString().replace(".", ".");
  });

  // Mismos eventos para montoEnganche y enganchepor
  document
    .getElementById("montoEnganche")
    .addEventListener("input", calcularEnganche);
  document
    .getElementById("enganchepor")
    .addEventListener("input", calcularEnganche);
 

  document
    .getElementById("montoEnganche")
    .addEventListener("blur", handleCurrencyInput);
  document.getElementById("montoEnganche").addEventListener("focus", function () {
    this.value = parseCurrency(this.value).toString().replace(".", ",");
  });

  // Función para calcular enganche y porcentaje
 function calcularEnganche() {
    const valorOp = parseCurrency(document.getElementById("valorop").value);
    const montoEngancheInput = document.getElementById("montoEnganche");
    const enganchePorInput = document.getElementById("enganchepor");
    const activeElement = document.activeElement ? document.activeElement.id : null;

    // Si hay un porcentaje definido, mantener la proporción con el nuevo valorOp
    if (enganchePorInput.value && activeElement !== "montoEnganche") {
        const porcentaje = parseFloat(enganchePorInput.value) || 0;
        montoEngancheInput.value = formatCurrency(valorOp * (porcentaje / 100));
    }
    // Si hay un monto definido, actualizar el porcentaje
    else if (montoEngancheInput.value && activeElement !== "enganchepor") {
        const monto = parseCurrency(montoEngancheInput.value);
        const porcentaje = valorOp ? (monto / valorOp) * 100 : 0;
        enganchePorInput.value = porcentaje.toFixed(2);
    }

    // Actualizar el cálculo de la corrida si ya existe
    if (typeof calcularCorrida === "function") {
        calcularCorrida();
    }
}

 function calcularDescuentos() {
    const montoTotal = parseCurrency(document.getElementById("montoTotal").value);
    const descuentoInput = document.getElementById("descuento");
    const descuentoPorInput = document.getElementById("descuentopor");
    const valorOpInput = document.getElementById("valorop");

    const activeElement = document.activeElement.id;

    if (activeElement === "descuentopor") {
        const porcentaje = parseFloat(descuentoPorInput.value) || 0;
        const descuento = montoTotal * (porcentaje / 100);
        descuentoInput.value = formatCurrency(descuento);
        valorOpInput.value = formatCurrency(montoTotal - descuento);
    } else if (activeElement === "descuento") {
        const descuento = parseCurrency(descuentoInput.value);
        const porcentaje = (descuento / montoTotal) * 100;
        descuentoPorInput.value = porcentaje.toFixed(2);
        valorOpInput.value = formatCurrency(montoTotal - descuento);
    }

    // Siempre recalcular enganche después de cambiar valorOp
    calcularEnganche();

    if (typeof calcularCorrida === "function") {
        calcularCorrida();
    }
}


  document
    .getElementById("montoEnganche")
    .addEventListener("blur", handleCurrencyInput);
  document
    .getElementById("montoEnganche")
    .addEventListener("focus", function () {
      this.value = parseCurrency(this.value).toString().replace(".", ".");
    });

  document
    .getElementById("montoTotal")
    .addEventListener("blur", handleCurrencyInput);
  document
    .getElementById("valorop")
    .addEventListener("blur", handleCurrencyInput);

  // Para el input de porcentaje (manejo especial)
  document.getElementById("descuentopor").addEventListener("blur", function () {
    let value = parseFloat(this.value.replace(",", "."));
    if (!isNaN(value)) {
      this.value = value.toFixed(2);
    }
  });

  document.getElementById("enganchepor").addEventListener("blur", function () {
    let value = parseFloat(this.value.replace(",", "."));
    if (!isNaN(value)) {
      this.value = value.toFixed(2);
    }
  });

  // Modal de edición de pago
  $("body").append(`
<div class="modal fade" id="modalEditarPago" tabindex="-1" role="dialog" aria-labelledby="modalEditarPagoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalEditarPagoLabel">Editar Pago #<span id="numeroPagoDisplay"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="formEditarPago">
          <input type="hidden" id="editarNumeroPago">
          
          <div class="form-group">
            <label>Fecha actual: <span id="fechaActual"></span></label>
            <label for="editarFechaPago">Nueva Fecha de Pago</label>
            <input type="date" class="form-control" id="editarFechaPago" required>
          </div>
          
          <div class="form-group">
            <label for="editarMontoCapital">Nuevo Monto a Capital</label>
            <input type="number" class="form-control" id="editarMontoCapital" step="0.01" min="0" required>
          </div>
          
          <div class="form-group">
            <label>Interés calculado: <span id="interesActual"></span></label>
            <small class="form-text text-muted">El interés se recalculará automáticamente</small>
          </div>
          
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="editarAjustarFechas">
            <label class="form-check-label" for="editarAjustarFechas">Ajustar fechas posteriores</label>
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

  $(document).on("click", ".btnEditar", function (e) {
    e.preventDefault();
    const row = $(this).closest("tr");
    const numeroPago = parseInt(row.find("td:eq(0)").text());

    // Función segura para extraer valores numéricos
    const extraerValorNumerico = (texto) => {
      if (!texto || typeof texto !== "string") return 0;
      const valorLimpio = texto.toString().replace(/[^\d.-]/g, "");
      const valorNumerico = parseFloat(valorLimpio);
      return isNaN(valorNumerico) ? 0 : valorNumerico;
    };

    const montoCapital = extraerValorNumerico(row.find("td:eq(2)").text());
    const montoInteres = extraerValorNumerico(row.find("td:eq(3)").text());
    const fechaTexto = row.find("td:eq(1)").text() || "";
    const fechaPago = typeof fechaTexto === "string" ? fechaTexto.trim() : "";

    // Convertir fecha dd/mm/aaaa a aaaa-mm-dd
    let fechaFormatoInput = "";
    if (fechaPago && fechaPago.match(/^\d{2}\/\d{2}\/\d{4}$/)) {
      const [dia, mes, anio] = fechaPago.split("/");
      fechaFormatoInput = `${anio}-${mes.padStart(2, "0")}-${dia.padStart(
        2,
        "0"
      )}`;
    }

    // Llenar el modal (solo capital editable)
    $("#editarNumeroPago").val(isNaN(numeroPago) ? "" : numeroPago);
    $("#editarMontoCapital").val(
      isNaN(montoCapital) ? "0.00" : montoCapital.toFixed(2)
    );

    // Mostrar interés pero no editable
    $("#interesActual").text(montoInteres.toFixed(2));
    $("#fechaActual").text(fechaPago);

    $("#editarFechaPago").val(fechaFormatoInput);
    $("#editarAjustarFechas").prop("checked", false);

    $("#modalEditarPago").modal("show");
  });

  $("#btnGuardarCambiosPago").click(function () {
    // Obtener valores editados
    const numeroPago = parseInt($("#editarNumeroPago").val() || "0");
    const nuevoCapital =
      parseFloat(
        $("#editarMontoCapital")
          .val()
          .replace(/[^\d.-]/g, "")
      ) || 0;
    const nuevaFecha = ($("#editarFechaPago").val() || "").trim();
    const ajustarFechas = $("#editarAjustarFechas").is(":checked");

    // Validaciones básicas
    if (isNaN(numeroPago)) {
      Swal.fire("Error", "Número de pago inválido", "error");
      return;
    }

    if (isNaN(nuevoCapital)) {
      Swal.fire("Error", "Monto a capital inválido", "error");
      return;
    }

    if (nuevoCapital < 0) {
      Swal.fire("Error", "El capital no puede ser negativo", "error");
      return;
    }

    if (!nuevaFecha) {
      Swal.fire("Error", "Se requiere una fecha válida", "error");
      return;
    }

    // Obtener pagos actuales
    let pagosActuales;
    try {
      pagosActuales = obtenerPagosActuales();
    } catch (e) {
      console.error("Error al obtener pagos:", e);
      Swal.fire("Error", "No se pudieron obtener los pagos actuales", "error");
      return;
    }

    // Buscar pago a editar
    const indicePago = pagosActuales.findIndex((p) => p.numero === numeroPago);
    if (indicePago === -1) {
      Swal.fire("Error", "Pago no encontrado", "error");
      return;
    }

    // Convertir fecha
    let fechaFormateada;
    try {
      const [anio, mes, dia] = nuevaFecha.split("-");
      fechaFormateada = `${dia}/${mes}/${anio}`;
    } catch (e) {
      Swal.fire("Error", "Formato de fecha inválido", "error");
      return;
    }

    // Crear objeto con cambios (el interés se recalculará después)
    const cambios = {
      capital: nuevoCapital,
      fecha: fechaFormateada,
      corrimientoFechas: ajustarFechas,
    };

    // Recalcular la corrida (la función manejará el cálculo de intereses)
    try {
      const resultado = recalcularCorrida(cambios, indicePago, pagosActuales);
      actualizarTablaPagos(resultado.tablaPagos, resultado.totales);

      $("#modalEditarPago").modal("hide");
      Swal.fire({
        icon: "success",
        title: "Éxito",
        text: "Cambios guardados correctamente",
        timer: 2000,
        showConfirmButton: false,
      });
    } catch (e) {
      console.error("Error al recalcular:", e);
      Swal.fire("Error", "No se pudo recalcular la corrida", "error");
    }
  });

  // Función mejorada para obtener pagos actuales
  function obtenerPagosActuales() {
    const pagos = [];

    // Verificar si la tabla existe
    if (!$("#tablaPagos").length) {
      return pagos;
    }

    $("#tablaPagos tbody tr").each(function () {
      try {
        const $row = $(this);
        const parsearMoneda = (texto) => {
          if (!texto) return 0;
          const valor = texto.replace(/[^\d.-]/g, "");
          return valor ? parseFloat(valor) : 0;
        };

        const numero = parseInt($row.find("td:eq(0)").text()) || 0;
        const fecha = $row.find("td:eq(1)").text().trim() || "";
        const capital = parsearMoneda($row.find("td:eq(2)").text());
        const interes = parsearMoneda($row.find("td:eq(3)").text());
        const total = parsearMoneda($row.find("td:eq(4)").text());
        const tipo = $row.find("td:eq(5)").text().trim() || "";
        const saldo = parsearMoneda($row.find("td:eq(6)").text());

        pagos.push({
          numero: isNaN(numero) ? 0 : numero,
          fecha: fecha,
          capital: isNaN(capital) ? 0 : capital,
          interes: isNaN(interes) ? 0 : interes,
          total: isNaN(total) ? 0 : total,
          tipo: tipo,
          saldo: isNaN(saldo) ? 0 : saldo,
        });
      } catch (e) {
        console.error("Error al parsear fila:", e);
      }
    });

    return pagos;
  }

  // Función para calcular la corrida financiera
  function calcularCorrida() {
    // Obtener y validar fecha de inicio (con ajuste horario)
    const fechaInicioInput = document.getElementById("fechaInicio").value;
    const fechaInicio = new Date(`${fechaInicioInput}T12:00:00`);

    if (isNaN(fechaInicio.getTime())) {
      alert("La fecha de inicio no es válida");
      return;
    }

    // Obtener valores del formulario
    let folio = document.getElementById("folio").value;
    let montoTotal = parseCurrency(document.getElementById("montoTotal").value);
    let valorOp =
      parseCurrency(document.getElementById("valorop").value) || montoTotal;
    let montoEnganche = parseCurrency(
      document.getElementById("montoEnganche").value
    );
    let plazosEnganche =
      parseInt(document.getElementById("plazosEnganche").value) || 0;
    let plazosSinInteres =
      parseInt(document.getElementById("plazosSinInteres").value) || 0;
    let plazosConInteres =
      parseInt(document.getElementById("plazosConInteres").value) || 0;
    let tasaInteresAnual =
      parseCurrency(document.getElementById("tasaInteresAnual").value) / 100;
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
    let totales = { capital: 0, intereses: 0, total: 0 };
    const diaPagoOriginal = fechaInicio.getDate();

    // Función para agregar pagos
    const agregarPago = (
      numero,
      fecha,
      capital,
      interes,
      total,
      tipo,
      saldo,
      esAjuste = false,
      ajuste = 0,
      infoFecha = null
    ) => {
      const pago = {
        numero,
        fecha: formatearFecha(fecha.fecha || fecha),
        capital: parseFloat(capital),
        interes: parseFloat(interes),
        total: parseFloat(total),
        tipo,
        saldo: parseFloat(saldo),
        esAjuste,
        ajuste: parseFloat(ajuste),
        infoFecha,
      };

      tablaPagos.push(pago);
      totales.capital += pago.capital;
      totales.intereses += pago.interes;
      totales.total += pago.total;

      return pago;
    };

    // 1. Procesar pagos de enganche
    if (plazosEnganche > 0 && saldoEnganche > 0) {
      const montoBaseEnganche = saldoEnganche / plazosEnganche;

      for (let i = 0; i < plazosEnganche; i++) {
        const esUltimoEnganche = i === plazosEnganche - 1;
        const montoPago = esUltimoEnganche
          ? saldoEnganche
          : Math.round(montoBaseEnganche);

        const fechaPago =
          i === 0
            ? {
                fecha: new Date(fechaInicio),
                ajustada: false,
                diaOriginal: diaPagoOriginal,
              }
            : obtenerFechaPago(fechaInicio, i, diaPagoOriginal);

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

    // 2. Procesar pagos normales (capital + intereses)
    if (saldoInsoluto > 0) {
      const totalPlazos = plazosConInteres + plazosSinInteres;

      if (totalPlazos > 0) {
        const montoBaseCapital = saldoInsoluto / totalPlazos;
        const montoCapitalEntero = Math.round(montoBaseCapital);
        const puntoCambioInteres = plazosSinInteres;

        for (let i = 0; i < totalPlazos; i++) {
          const esUltimoPago = i === totalPlazos - 1;
          const conInteres = i >= puntoCambioInteres;
          const mesesAdicionales = plazosEnganche + i;

          const montoCapital = esUltimoPago
            ? saldoInsoluto
            : Math.min(montoCapitalEntero, saldoInsoluto);

          const interes = conInteres
            ? Math.round(saldoInsoluto * tasaInteresMensual)
            : 0;
          const totalPago = montoCapital + interes;

          const fechaPago = obtenerFechaPago(
            fechaInicio,
            mesesAdicionales,
            diaPagoOriginal
          );
          const tipoPago = conInteres ? "CON INTERES" : "SIN INTERES";

          agregarPago(
            (plazosEnganche > 0 ? plazosEnganche : 0) + i + 1,
            fechaPago,
            montoCapital,
            interes,
            totalPago,
            tipoPago,
            saldoInsoluto - montoCapital,
            esUltimoPago && montoCapital !== montoCapitalEntero,
            esUltimoPago
              ? saldoInsoluto + montoCapital - montoCapitalEntero
              : 0,
            fechaPago.ajustada ? { diaOriginal: fechaPago.diaOriginal } : null
          );

          saldoInsoluto -= montoCapital;
        }
      }
    }

    // 3. Ajustar saldo residual si existe
    if (Math.abs(saldoInsoluto) > 0.01 && tablaPagos.length > 0) {
      const ultimoPago = tablaPagos[tablaPagos.length - 1];
      const ajuste = saldoInsoluto;

      ultimoPago.capital = (parseFloat(ultimoPago.capital) + ajuste).toFixed(2);
      ultimoPago.total = (parseFloat(ultimoPago.total) + ajuste).toFixed(2);
      ultimoPago.saldo = "0.00";
      ultimoPago.esAjuste = true;
      ultimoPago.ajuste = ajuste.toFixed(2);

      totales.capital += ajuste;
      totales.total += ajuste;
    }

    // Mostrar resultados usando la función de actualización de tabla
    actualizarTablaPagos(tablaPagos, totales);

    document.getElementById("results").style.display = "block";
    document.getElementById("results").scrollIntoView({ behavior: "smooth" });
  }

  // Modificación en la función recalcularCorrida para manejar intereses automáticamente
  function recalcularCorrida(cambios, indicePago, tablaPagos) {
    // Copiar el array original para no modificarlo directamente
    let nuevaTabla = JSON.parse(JSON.stringify(tablaPagos));
    let tasaInteresAnual =
      parseCurrency(document.getElementById("tasaInteresAnual").value) / 100;
    let tasaInteresMensual = tasaInteresAnual / 12;

    // 1. Calcular el saldo insoluto previo al pago editado
    let saldoPrevio =
      indicePago > 0
        ? parseFloat(nuevaTabla[indicePago - 1].saldo)
        : parseFloat(nuevaTabla[0].capital) + parseFloat(nuevaTabla[0].saldo);

    // 2. Aplicar los cambios al pago editado
    const pagoActual = nuevaTabla[indicePago];
    const capitalOriginal = parseFloat(pagoActual.capital);
    const capitalNuevo = cambios.capital;

    // Actualizar capital
    pagoActual.capital = capitalNuevo.toFixed(2);

    // Manejo de fechas - convertir el string de fecha a objeto Date
    let fechaPagoEditado;
    if (typeof cambios.fecha === "string") {
      // Convertir formato "dd/mm/yyyy" a Date
      const [dia, mes, anio] = cambios.fecha.split("/");
      fechaPagoEditado = new Date(`${anio}-${mes}-${dia}T12:00:00`);
    } else {
      fechaPagoEditado = cambios.fecha;
    }

    // Validar fecha
    if (!fechaPagoEditado || isNaN(fechaPagoEditado.getTime())) {
      console.error("Fecha no válida:", cambios.fecha);
      fechaPagoEditado = new Date(); // Usar fecha actual como fallback
    }

    pagoActual.fecha = formatearFecha(fechaPagoEditado);

    // Recalcular interés (solo si es pago CON INTERES)
    if (pagoActual.tipo === "CON INTERES") {
      const nuevoInteres = saldoPrevio * tasaInteresMensual;
      pagoActual.interes = Math.round(nuevoInteres).toFixed(2);
    } else {
      pagoActual.interes = "0.00";
    }

    // Actualizar total y saldo
    pagoActual.total = (
      parseFloat(pagoActual.capital) + parseFloat(pagoActual.interes)
    ).toFixed(2);
    let saldoInsoluto = saldoPrevio - parseFloat(pagoActual.capital);
    pagoActual.saldo = saldoInsoluto.toFixed(2);

    // 3. Recalcular pagos posteriores
    if (cambios.corrimientoFechas) {
      // Obtener el día de pago original (del primer pago)
      let diaPagoOriginal = null;
      if (nuevaTabla.length > 0 && nuevaTabla[0].infoFecha) {
        diaPagoOriginal = nuevaTabla[0].infoFecha.diaOriginal;
      }

      // Recalcular fechas para pagos posteriores
      for (let i = indicePago + 1; i < nuevaTabla.length; i++) {
        const pago = nuevaTabla[i];

        // Si el saldo ya se liquidó, marcar como completado
        if (saldoInsoluto <= 0.01) {
          pago.capital = "0.00";
          pago.interes = "0.00";
          pago.total = "0.00";
          pago.tipo = "COMPLETADO";
          pago.saldo = "0.00";
          continue;
        }

        // Calcular capital para este pago
        let capitalPago = parseFloat(pago.capital);
        const esUltimoPago = i === nuevaTabla.length - 1;

        if (esUltimoPago || capitalPago > saldoInsoluto) {
          capitalPago = saldoInsoluto;
        }

        // Calcular intereses
        let interesPago = 0;
        if (pago.tipo === "CON INTERES") {
          interesPago = saldoInsoluto * tasaInteresMensual;
          interesPago = Math.round(interesPago);
        }

        // Actualizar el pago
        pago.capital = capitalPago.toFixed(2);
        pago.interes = interesPago.toFixed(2);
        pago.total = (capitalPago + interesPago).toFixed(2);
        pago.saldo = (saldoInsoluto - capitalPago).toFixed(2);

        // Actualizar saldo para el siguiente pago
        saldoInsoluto -= capitalPago;

        // Calcular nueva fecha usando obtenerFechaPago
        const mesesDesdeInicio = i; // Asumiendo pagos mensuales
        const fechaAnterior = new Date(
          nuevaTabla[i - 1].fecha.split("/").reverse().join("-") + "T12:00:00"
        );
        const resultadoFecha = obtenerFechaPago(
          fechaAnterior,
          1, // 1 mes después
          diaPagoOriginal
        );

        pago.fecha = formatearFecha(resultadoFecha.fecha);
        pago.infoFecha = resultadoFecha.ajustada
          ? {
              diaOriginal: resultadoFecha.diaOriginal,
            }
          : null;
      }
    } else {
      // Solo recalcular montos sin cambiar fechas
      for (let i = indicePago + 1; i < nuevaTabla.length; i++) {
        const pago = nuevaTabla[i];

        if (saldoInsoluto <= 0.01) {
          pago.capital = "0.00";
          pago.interes = "0.00";
          pago.total = "0.00";
          pago.tipo = "COMPLETADO";
          pago.saldo = "0.00";
          continue;
        }

        let capitalPago = parseFloat(pago.capital);
        const esUltimoPago = i === nuevaTabla.length - 1;

        if (esUltimoPago || capitalPago > saldoInsoluto) {
          capitalPago = saldoInsoluto;
        }

        let interesPago = 0;
        if (pago.tipo === "CON INTERES") {
          interesPago = saldoInsoluto * tasaInteresMensual;
          interesPago = Math.round(interesPago);
        }

        pago.capital = capitalPago.toFixed(2);
        pago.interes = interesPago.toFixed(2);
        pago.total = (capitalPago + interesPago).toFixed(2);
        pago.saldo = (saldoInsoluto - capitalPago).toFixed(2);

        saldoInsoluto -= capitalPago;
      }
    }

    // 4. Eliminar pagos completamente liquidados al final
    nuevaTabla = nuevaTabla.filter(
      (pago) =>
        parseFloat(pago.capital) > 0.01 || parseFloat(pago.interes) > 0.01
    );

    // 5. Recalcular totales
    const totales = {
      capital: 0,
      intereses: 0,
      total: 0,
    };

    nuevaTabla.forEach((pago) => {
      totales.capital += parseFloat(pago.capital);
      totales.intereses += parseFloat(pago.interes);
      totales.total += parseFloat(pago.total);
    });

    return {
      tablaPagos: nuevaTabla,
      totales: totales,
    };
  }

  function calcularTotales(tablaPagos) {
    return tablaPagos.reduce(
      (acum, pago) => {
        acum.capital += parseFloat(pago.capital);
        acum.intereses += parseFloat(pago.interes);
        acum.total += parseFloat(pago.total);
        return acum;
      },
      { capital: 0, intereses: 0, total: 0 }
    );
  }

  // Función para manejar la edición de un pago (se llamaría desde el modal de edición)
  function manejarEdicionPago(indicePago) {
    // Obtener los datos editados del modal
    const pagoEditado = {
      capital: parseFloat(document.getElementById("modalCapital").value),
      interes: parseFloat(document.getElementById("modalInteres").value),
      fecha: document.getElementById("modalFecha").value,
      corrimientoFechas: document.getElementById("modalCorrimiento").checked,
    };

    // Obtener la tabla de pagos actual
    const tablaPagosActual = obtenerTablaPagosActual(); // Necesitarías implementar esta función

    // Recalcular
    const resultado = recalcularCorrida(
      pagoEditado,
      indicePago,
      tablaPagosActual
    );

    // Actualizar la UI con la nueva tabla
    actualizarTablaPagos(resultado.tablaPagos, resultado.totales);
  }

  // Función auxiliar para formatear fechas (debe ser consistente con la original)
  function formatearFecha(fecha) {
    if (typeof fecha === "string") return fecha;

    const dia = String(fecha.getDate()).padStart(2, "0");
    const mes = String(fecha.getMonth() + 1).padStart(2, "0");
    const año = fecha.getFullYear();

    return `${dia}/${mes}/${año}`;
  }

  // Manejador para guardar los cambios del pago

  function actualizarTablaPagos(tablaPagos, totales) {
    let tablaHTML = `
        <table id="tablaPagos" class="table table-striped table-bordered table-hover table-sm table-condensed">
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
      let rowClass = "";
      let detalle = "";

      if (pago.esAjuste) {
        detalle = `<span class="adjustment-detail">(+${formatoMoneda(
          pago.ajuste
        )} al capital)</span>`;
        rowClass = "adjusted-row";
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
                </td>
            </tr>
        `;
    });

    tablaHTML += "</tbody></table>";

    // Reemplazar la tabla existente
    document.getElementById("paymentTable").innerHTML = tablaHTML;

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

    // Reinicializar DataTables si es necesario
    if ($.fn.DataTable.isDataTable("#tablaPagos")) {
      $("#tablaPagos").DataTable().destroy();
    }

    $("#tablaPagos").DataTable({
      scrollY: "400px",
      scrollCollapse: true,
      paging: false,
      autoWidth: false,
      ordering: false,
      info: false,
      searching: false,
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
  }

   function TablaPagos(tablaPagos, totales) {
    let tablaHTML = `
        <table id="tablaPagos" class="table table-striped table-bordered table-hover table-sm table-condensed">
            <thead class="bg-green">
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
      let rowClass = "";
      let detalle = "";

      if (pago.esAjuste) {
        detalle = `<span class="adjustment-detail">(+${formatoMoneda(
          pago.ajuste
        )} al capital)</span>`;
        rowClass = "adjusted-row";
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
            </tr>
        `;
    });

    tablaHTML += "</tbody></table>";

    // Reemplazar la tabla existente
    document.getElementById("paymentTable").innerHTML = tablaHTML;

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

    // Reinicializar DataTables si es necesario
    if ($.fn.DataTable.isDataTable("#tablaPagos")) {
      $("#tablaPagos").DataTable().destroy();
    }

    $("#tablaPagos").DataTable({
      scrollY: "400px",
      scrollCollapse: true,
      paging: false,
      autoWidth: false,
      ordering: false,
      info: false,
      searching: false,
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
  }

  // Validar enganche en tiempo real
  document
    .getElementById("montoEnganche")
    .addEventListener("input", function () {
      const montoTotal =
        parseCurrency(document.getElementById("montoTotal").value) || 0;
      const montoEnganche = parseCurrency(this.value) || 0;

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
        //calcularCorrida();
      }
    });
  });

  // Inicializar tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // Convierte un string formateado (1.000,50) a número (1000.50)
  // Convierte un string formateado (1,000.50 o 1.000,50) a número (1000.50)
  function parseCurrency(value) {
    if (typeof value === "number") return value;
    if (!value) return 0;
    // Elimina cualquier símbolo de moneda y espacios
    value = value.replace(/[^0-9.,-]/g, "").trim();

    // Si tiene coma y punto, asume formato "1,234.56" (inglés)
    if (value.indexOf(",") > -1 && value.indexOf(".") > -1) {
      if (value.lastIndexOf(".") > value.lastIndexOf(",")) {
        // "1,234.56" => "1234.56"
        value = value.replace(/,/g, "");
      } else {
        // "1.234,56" => "1234.56"
        value = value.replace(/\./g, "").replace(",", ".");
      }
    } else if (value.indexOf(",") > -1) {
      // Solo coma: "1234,56" => "1234.56"
      value = value.replace(",", ".");
    } else {
      // Solo punto o solo números
      // No cambio necesario
    }
    const num = parseFloat(value);
    return isNaN(num) ? 0 : num;
  }

  // Formatea un número a string monetario mexicano (1000.5 → "1,000.50")
  function formatCurrency(value) {
    const num = typeof value === "string" ? parseCurrency(value) : value;
    return num.toLocaleString("es-MX", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  // Valida y formatea el input mientras el usuario escribe (formato mexicano)
  function handleCurrencyInput(event) {
    let input = event.target;
    let value = input.value.replace(/[^0-9.,-]/g, "");

    // Permite solo una coma o punto decimal
    const parts = value.split(/[.,]/);
    let formatted = parts[0];
    if (parts.length > 1) {
      formatted += "." + parts.slice(1).join("");
    }

    let num = parseCurrency(formatted);
    input.value = formatCurrency(num);
  }
});
