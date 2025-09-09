$(document).ready(function () {
  var id, opcion;
  opcion = 4;

  // TOOLTIP DATATABLE
  $('[data-toggle="tooltip"]').tooltip();

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
    dom:
      "<'row justify-content-center'<'col-sm-12 col-md-4 form-group'l><'col-sm-12 col-md-4 form-group'B><'col-sm-12 col-md-4 form-group'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",

    buttons: [
      {
        extend: "excelHtml5",
        text: "<i class='fas fa-file-excel'> Excel</i>",
        titleAttr: "Exportar a Excel",
        title: "Listado de Colaboradores",
        className: "btn bg-success ",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
      {
        extend: "pdfHtml5",
        text: "<i class='far fa-file-pdf'> PDF</i>",
        titleAttr: "Exportar a PDF",
        title: "Listado de Colaboradores",
        className: "btn bg-danger",
        exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] },
      },
    ],

    columnDefs: [
      {
        targets: -1,
        data: null,
        defaultContent:
          "<div class='text-center'><button class='btn btn-sm btn-primary btnEditar' data-toggle='tooltip' data-placement='top' title='Editar'><i class='fas fa-edit'></i></button>\
                <button class='btn btn-sm btn-danger btnBorrar' data-toggle='tooltip' data-placement='top' title='Eliminar'><i class='fas fa-trash-alt'></i></button></div>",
      },
    ],

    //Para cambiar el lenguaje a español
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

  //BONTON NUEVO
  $("#btnNuevo").click(function () {
    $("#formDatos").trigger("reset");
    $(".modal-header").css("background-color", "#28a745");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("NUEVO CLIENTE");
    $("#modalCRUD").modal("show");
    id = null;
    opcion = 1;
  });

  var fila;

  //BOTON EDITAR
  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");

    id_clie = parseInt(fila.find("td:eq(0)").text());
    id = id_clie;
    $("#formDatos").trigger("reset");

    $.ajax({
      url: "bd/crud_cliente.php",
      type: "POST",
      dataType: "json",
      data: { id_clie: id_clie, opcion: 4 },
      success: function (data) {
        console.log(data);
        $("#nombre_clie").val(data[0].nombre);
        $("#dir_calle").val(data[0].dir_calle);
        $("#dir_ciudad").val(data[0].dir_ciudad);
        $("#dir_colonia").val(data[0].dir_colonia);
        $("#dir_edo").val(data[0].dir_edo);
        $("#dir_cp").val(data[0].dir_cp);
        $("#tel_clie").val(data[0].tel_cel);
        $("#correo_clie").val(data[0].email);
        $("#rfc").val(data[0].rfc);
        $("#folio_ide").val(data[0].folio);


        col_asignado=data[0].col_asignado;
        origen=data[0].origen;


        $("#origen").selectpicker("val", origen);
        $("#col_asignado").selectpicker("val", col_asignado);

 
  

        var especial = data[0].especial 
        if (especial == 1) {
          $("#especial").prop("checked", true);
        }
        else {
          $("#especial").prop("checked", false);
        }
        $("#nacionalidad").val(data[0].nacionalidad);
        
        $("#tipo_ide").val(data[0].tipo_ide);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error en AJAX:", textStatus, errorThrown);
        Swal.fire({
          title: "Error",
          text: "No se pudo obtener la información del colaborador.",
          icon: "error",
        });
      },
    });

    opcion = 2; //editar

    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white");
    $(".modal-title").text("EDITAR COLABORADOR");
    $("#modalCRUD").modal("show");
  });

  //BOTON BORRAR
  $(document).on("click", ".btnBorrar", function () {
    fila = $(this);

    id = parseInt($(this).closest("tr").find("td:eq(0)").text());
    console.log(id);
    opcion = 3;
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
            url: "bd/crudcliente.php",
            type: "POST",
            dataType: "json",
            data: { id: id, opcion: opcion },
            success: function (data) {
              tablaVis.row(fila.parents("tr")).remove().draw();
            },
          });
        } else if (isConfirm.dismiss === swal.DismissReason.cancel) {
        }
      });
  });

  //GUARDAR PROVEEDOR

  $(document).on("click", "#btnGuardar", function () {
    var nombre = $("#nombre_clie").val();
    var telefono = $("#tel_clie").val();
    var correo = $("#correo_clie").val();
    var rfc = $("#rfc").val();
    var folio_ide = $("#folio_ide").val();
    var especial = $("#especial").is(":checked") ? 1 : 0;
    var nacionalidad = $("#nacionalidad").val();
    var dir_calle = $.trim($("#dir_calle").val());
    var dir_ciudad = $.trim($("#dir_ciudad").val());
    var dir_colonia = $.trim($("#dir_colonia").val());
    var dir_edo = $.trim($("#dir_edo").val());
    var dir_cp = $.trim($("#dir_cp").val());
    var tipo_ide = $("#tipo_ide").val();
    var col_asignado = $("#col_asignado").val();
    var origen = $("#origen").val();

    var telValido = /^(\+[0-9]{1,3})?[0-9]{10}$/.test(telefono);
    if (!telValido) {
      Swal.fire(
        "Error",
        "El teléfono debe tener 10 dígitos para nacional o formato internacional con +código (ej: +521234567890)",
        "error"
      );
      return;
    } else {
      let telefonoNormalizado = telefono;
      if (!telefono.startsWith("+")) {
        telefonoNormalizado = "+52" + telefono;
        telefono = telefonoNormalizado; // Actualizar el teléfono con el normalizado
      }
    }

    console.log("Datos a enviar:", {
      nombre,
      telefono,
      correo,
      rfc,
      folio_ide,
      especial,
      tipo_ide,
      dir_calle,
      dir_ciudad,
      dir_colonia,
      dir_edo,
      dir_cp,
      id,
      opcion,
    });

    $.ajax({
      url: "bd/crud_cliente.php",
      type: "POST",
      dataType: "json",
      data: {
        nombre: nombre,
        telefono: telefono,
        correo: correo,
        rfc: rfc,
        folio_ide: folio_ide,
        especial: especial,
        tipo_ide: tipo_ide,
        nacionalidad: nacionalidad,
        dir_calle: dir_calle,
        dir_ciudad: dir_ciudad,
        dir_colonia: dir_colonia,
        dir_edo: dir_edo,
        dir_cp: dir_cp,
        id_clie: id,
        opcion: opcion,
        col_asignado: col_asignado,
        origen: origen,
      },
      success: function (data) {
        console.log(data);
       ;

        id_clie= data[0].id_clie;
        nombre = data[0].nombre;
        telefono = data[0].tel_cel;
        correo = data[0].email;
        // Actualizar DataTable
        if (opcion == 1) {
          // Nuevo registro
          tablaVis.row
            .add([
              id_clie,
              nombre,
              telefono,
              correo,
              // Columna de acciones se agrega automáticamente
            ])
            .draw();
        } else {
          // Edición
          tablaVis
            .row(fila)
            .data([
              id_clie,
              nombre,
              telefono,
              correo,
           
            ])
            .draw();
        }

        // Cerrar modal y limpiar
        $("#modalCRUD").modal("hide");
        $("#formDatos").trigger("reset");

        Swal.fire({
          title: "¡Éxito!",
          text: "Los datos se han guardado correctamente.",
          icon: "success",
        });
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // Error de conexión
        console.error("Error en AJAX:", textStatus, errorThrown);
        Swal.fire({
          title: "Error de Conexión",
          text: "No se pudo conectar al servidor. Por favor intente nuevamente.",
          icon: "error",
        });
      },
    });
  });

  // Función para validar email
  function validarEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Función para generar botones de acción (debe coincidir con tu implementación)
});
