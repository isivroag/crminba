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
    $(".modal-title").text("NUEVO COLABORADOR");
    $("#modalCRUD").modal("show");
    id = null;
    opcion = 1;
  });

  var fila;

  //BOTON EDITAR
  $(document).on("click", ".btnEditar", function () {
    fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    nombre = fila.find("td:eq(1)").text();
    telefono = fila.find("td:eq(2)").text();

    correo = fila.find("td:eq(3)").text();

    $("#nombre").val(nombre);
    $("#telefono").val(telefono);

    $("#correo").val(correo);

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
            url: "bd/crudcolaborador.php",
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
    var nombre = $("#nombre").val();
    var telefono = $("#telefono").val();
    var correo = $("#correo").val();

    // Validación de campos
    if (!nombre || !telefono || !correo) {
      Swal.fire({
        title: "Datos Faltantes",
        text: "Debe ingresar todos los datos marcados con *",
        icon: "warning",
      });
      return false;
    }

    // Validación de email
    if (!validarEmail(correo)) {
      Swal.fire({
        title: "Correo Inválido",
        text: "Por favor ingrese un correo electrónico válido",
        icon: "warning",
      });
      return false;
    }

    $.ajax({
      url: "bd/crudcolaborador.php",
      type: "POST",
      dataType: "json",
      data: {
        nombre: nombre,
        telefono: telefono,
        correo: correo,
        id: id,
        opcion: opcion,
      },
      success: function (data) {
        Swal.fire({
          title: "Éxito",
          text: "Datos guardados correctamente",
          icon: "success",
          confirmButtonText: "Aceptar",
          confirmButtonColor: "#28B463",
        });
          id_col = data[0].id_col;
              nombre = data[0].nombre;
              telefono = data[0].telefono;  
              correo = data[0].correo;
          
          // Actualizar DataTable
          if (opcion == 1) {
           
            
             
           
            // Nuevo registro
            tablaVis.row
              .add([
                id_col,
                nombre,
                telefono,
                correo,

              ])
              .draw();
          } else {
            // Edición
            // Actualizar la fila existente 
            tablaVis
              .row(fila)
              .data([
                id_col,
                nombre,
                telefono,
                correo,
              ])
              .draw();
          }

          // Cerrar modal y limpiar
          $("#modalCRUD").modal("hide");
          $("#formDatos").trigger("reset");
       
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

 
});
