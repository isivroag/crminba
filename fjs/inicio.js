$(document).ready(function () {
  var id, opcion;
  opcion = 4;

  var tablaNuevo = $("#tablaNuevo").DataTable({
    dom: "<'row'<'col-sm-12'tr>>", // Solo la tabla (sin l, B, f, i, p)
    paging: false, // Sin paginación
    info: false, // Sin leyenda de "Mostrando registros del..."
    searching: false, // Sin buscador
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

  var tablaRealizado = $("#tablaRealizado").DataTable({
    dom: "<'row'<'col-sm-12'tr>>", // Solo la tabla (sin l, B, f, i, p)
    paging: false, // Sin paginación
    info: false, // Sin leyenda de "Mostrando registros del..."
    searching: false, // Sin buscador
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
      columnDefs: [
     
      {
        targets: [7] ,
       
        render: function(data, type, row, meta) {
          return '<div class="multi-line ">' + data + '</div>';
        }
      }
       ],
  });

  var tablaAgenda = $("#tablaAgenda").DataTable({
    dom: "<'row'<'col-sm-12'tr>>", // Solo la tabla (sin l, B, f, i, p)
    paging: false, // Sin paginación
    info: false, // Sin leyenda de "Mostrando registros del..."
    searching: false, // Sin buscador

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
    columnDefs: [
      { className: "hide_column", targets: [0] },

      {
        targets: [8],

        render: function (data, type, row, meta) {
          return '<div class="multi-line ">' + data + "</div>";
        },
      },
    ],
  });

  $(document).on("click", ".btnSeguimiento", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "seguimiento.php?id_pros=" + id;
  });

  $(document).on("click", ".btnSeguir", function () {
    var fila = $(this).closest("tr");
    id = parseInt(fila.find("td:eq(0)").text());

    window.location.href = "seguimiento.php?id_seg=" + id;
  });
});
