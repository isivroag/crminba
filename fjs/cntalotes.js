$(document).ready(function () {
  var id, opcion, fila;
  opcion = 4;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();
  $("#manzana").on("change", cambiarManzana);

  $(document).off("show.bs.modal");
  $(document).on("show.bs.modal", function (e) {
    var $modal = $(e.target);
    $modal
      .removeAttr("aria-hidden")
      .attr("aria-modal", "true")
      .css("display", "block")
      .css("padding-right", "17px");

    $("body").addClass("modal-open");
    $(".modal-backdrop").attr("aria-hidden", "true");
  });

  tablaVis = $("#tablaV").DataTable({
    scrollY: "400px", // Scroll vertical
    scrollCollapse: true,
    paging: false, // Sin paginación
    autoWidth: false, // Evita cambios automáticos en el ancho
    ordering: true,
    info: true, // Muestra el resumen de registros
    searching: true, // Habilita la búsqueda

    dom:
      "<'row justify-content-between'<'col-sm-6'l><'col-sm-6'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-6'i>>",

    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' title='Editar'><i class='fas fa-edit'></i></button>\
                 <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
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

  tablaProyecto = $("#tablaProyecto").DataTable({
    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><div class='btn-group'><button class='btn btn-sm btn-success btnSelProy' data-toggle='tooltip' data-placement='top' title='Seleccionar Obra'><i class='fas fa-hand-pointer'></i></button></div></div>",
      },
    ],
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

  $(document).on("click", ".btnSelProy", function () {
    fila = $(this);
    id_proy = parseInt($(this).closest("tr").find("td:eq(0)").text());

    window.location.href = "cntaestructura.php?id_proy=" + id_proy;
  });

  //BONTON NUEVO
  $("#bproyecto").click(function () {
    $("#modalProyecto").modal("show");
  });

  $(document).on("click", "#btnNuevo", function () {
    $("#formDatos").trigger("reset");
    opcion = 1; // Opción 1: Crear nuevo
    $("#modalCRUD").modal("show");
  });

  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    clave = fila.find("td:eq(1)").text();
    descripcion = fila.find("td:eq(2)").text();
    $("#clave").val(clave);
    $("#descripcion").val(descripcion);
    opcion = 2; // editar
    $(".modal-title").text("EDITAR MANZANA");
    $("#modalCRUD").modal("show");
  });

  $(document).on("click", "#btnGuardarmn", function () {
    // Obtén los valores de los campos
    var datos = {
      id_lote: $("#id_lote").val() || "", // si tienes campo oculto para editar
      id_proy: $("#id_proy").val(),
      id_man: $("#id_man").val(),
      clave_lote: $("#clave_lote").val(),
      id_mapa: $("#id_mapa").val(),
      manzana: $("#manzana").val(),
      noroeste: $("#noroeste").val(),
      norte: $("#norte").val(),
      noreste: $("#noreste").val(),
      oeste: $("#oeste").val(),
      este: $("#este").val(),
      suroeste: $("#suroeste").val(),
      sur: $("#sur").val(),
      sureste: $("#sureste").val(),
      id_tipo: $("#id_tipo").val(),
      superficie: $("#superficie").val(),
      precio: $("#precio")
        .val()
        .replace(/[^0-9.]+/g, ""), // quita formato moneda
      valortotal: $("#valortotal")
        .val()
        .replace(/[^0-9.]+/g, ""),
      frente: $("#frente").val(),
      fondo: $("#fondo").val(),
      construido: $("#construido").val(),
      indiviso: $("#indiviso").val(),
      renta: $("#renta").val(),
      opcion: opcion, // 1 = alta, 2 = editar, etc.
    };

    // Validación básica
    if (
      datos.clave_lote.length == 0 ||
      datos.id_mapa.length == 0 ||
      datos.manzana.length == 0 ||
      datos.id_tipo.length == 0 ||
      datos.superficie.length == 0 ||
      datos.precio.length == 0 ||
      datos.valortotal.length == 0
    ) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos requeridos",
        icon: "warning",
      });
      return false;
    } else {
      $.ajax({
        url: "bd/crudlote.php",
        type: "POST",
        dataType: "json",
        data: datos,
        success: function (data) {
          if (data[0].respuesta == "ok") {
            registroguardado();
            if (opcion == 1) {
              tablaVis.row
                .add([
                  data[0].id_lote,
                  data[0].clave_lote,
                  data[0].manzana,
                  data[0].superficie,
                  parseFloat(data[0].precio).toLocaleString("es-MX", {
                    style: "currency",
                    currency: "MXN",
                  }),
                  parseFloat(data[0].valortotal).toLocaleString("es-MX", {
                    style: "currency",
                    currency: "MXN",
                  }),
                ])
                .draw();
            } else if (opcion == 2) {
              tablaVis
                .row(fila)
                .data([
                  data[0].id_lote,
                  data[0].clave_lote,
                  data[0].manzana,
                  data[0].superficie,
                  parseFloat(data[0].precio).toLocaleString("es-MX", {
                    style: "currency",
                    currency: "MXN",
                  }),
                  parseFloat(data[0].valortotal).toLocaleString("es-MX", {
                    style: "currency",
                    currency: "MXN",
                  }),
                ])
                .draw();
            }
          } else {
            Swal.fire({
              title: "Error",
              text: "No se pudo guardar el lote",
              icon: "error",
            });
          }
        },
      });
      $("#modalCRUD").modal("hide");
    }
  });

  $(document).on("click", ".btnBorrar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());
    opcion = 3; //borrar
    //agregar codigo de sweatalert2
    swal
      .fire({
        title: "ELIMINAR",
        text: "¿Desea eliminar el registro seleccionado?",
        showCancelButton: true,
        icon: "question",
        focusConfirm: true,
        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
        confirmButtonColor: "#28B463",
        cancelButtonColor: "#d33",
      })
      .then(function (isConfirm) {
        if (isConfirm.value) {
          $.ajax({
            url: "bd/crudmanzana.php",
            type: "POST",
            dataType: "json",
            data: { id: id, opcion: opcion },
            success: function (data) {
              if (data[0].respuesta == "ok") {
                swal.fire({
                  title: "Registro Eliminado",
                  icon: "success",
                  focusConfirm: true,
                  confirmButtonText: "Aceptar",
                });
                tablaVis.row(fila.parents("tr")).remove().draw();
              } else {
                swal.fire({
                  title: "Error",
                  text: "No se puede eliminar el registro, ya que tiene datos relacionados.",
                  icon: "error",
                  focusConfirm: true,
                  confirmButtonText: "Aceptar",
                });
              }
            },
          });
        } else if (isConfirm.dismiss === swal.DismissReason.cancel) {
        }
      });
  });

  function registroguardado() {
    swal.fire({
      title: "Registro Guardado con Exito",
      icon: "success",
      focusConfirm: true,
      confirmButtonText: "Aceptar",
    });
  }

  $("#id_tipo").on("change", function () {
    var selected = $(this).find("option:selected");
    var precio = 0;
    // El texto del option es: tipo ($1,000.00)
    var texto = selected.text();
    var match = texto.match(/\(([^)]+)\)/); // Toma todo lo que está entre paréntesis
    if (match) {
      // Limpia el texto para dejar solo números y punto decimal
      var precioStr = match[1].replace(/[^0-9.]+/g, "");
      precio = parseFloat(precioStr) || 0;
    }
    // Formato moneda para precio
    $("#precio").val(
      precio.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
    );
    calcularValorTotal();
  });

  // Cuando cambia la superficie o precio, recalcula el valor total
  $("#superficie, #precio").on("input", function () {
    calcularValorTotal();
  });

  function calcularValorTotal() {
    // Quita formato moneda antes de calcular
    var superficie = parseFloat($("#superficie").val()) || 0;
    var precioStr = $("#precio")
      .val()
      .replace(/[^0-9.-]+/g, "");
    var precio = parseFloat(precioStr) || 0;
    var valortotal = superficie * precio;
    // Formato moneda para valor total
    $("#valortotal").val(
      valortotal.toLocaleString("es-MX", { style: "currency", currency: "MXN" })
    );
  }

  // Formatea el texto de los options del select al cargar la página
  $("#id_tipo option").each(function () {
    var texto = $(this).text();
    var match = texto.match(/\(([\d.,]+)\)/);
    if (match) {
      var precio = parseFloat(match[1].replace(",", ""));
      var tipo = texto.split("(")[0].trim();
      $(this).text(
        tipo +
          " (" +
          precio.toLocaleString("es-MX", {
            style: "currency",
            currency: "MXN",
          }) +
          ")"
      );
    }
  });

  function cambiarManzana() {
    var id_proy = $("#id_proy").val();
    var id_man = $(this).val();

    // Mostrar loader
    $("#tablaV").append(
      '<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i></div>'
    );

    $.ajax({
      url: "bd/obtener_lotes.php",
      method: "POST",
      dataType: "json", // Asegúrate de especificar el tipo de dato esperado
      data: {
        id_proy: id_proy,
        id_man: id_man,
      },
      success: function (response) {
        // Limpiar la tabla DataTable correctamente
        tablaVis.clear().draw();

        // Agregar los nuevos datos formateados
        response.forEach(function (lote) {
          // Formatea los datos según sea necesario
          tablaVis.row
            .add([
              lote.id_lote,
              lote.clave_lote,
              lote.id_mapa,
              lote.status,
              "", // Columna vacía para los botones
            ])
            .draw(false); // El false evita redibujar después de cada fila
        });

        // Redibujar la tabla una sola vez al final
        tablaVis.draw();

        // Reactivar tooltips (opcional)
        $('[data-toggle="tooltip"]').tooltip();
      },
      error: function (xhr, status, error) {
        console.error("Error al cargar lotes:", error);
        Swal.fire("Error", "No se pudieron cargar los lotes", "error");
      },
      complete: function () {
        // Ocultar loader
        $("#tablaV .overlay").remove();
      },
    });
  }
});
