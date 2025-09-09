$(document).ready(function () {

      var textcolumnas = permisos();

  function permisos() {
    var tipousuario = parseInt($("#tipousuario").val());
    var columnas = "";
    console.log("Tipo de usuario:", tipousuario);

    switch (tipousuario) {
      case 1: // usuario normal
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnVer' data-toggle='tooltip' data-placement='top' title='Consultar'><i class='fas fa-search'></i></button>\
           <button class='btn btn-sm btn-success btnPDF' data-toggle='tooltip' data-placement='top' title='Imprimir'><i class='fas fa-print'></i></button>\
           </div>";
        break;
      case 2: // usuario administrador
      case 3: // usuario supervisor
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnVer' data-toggle='tooltip' data-placement='top' title='Consultar'><i class='fas fa-search'></i></button>\
           <button class='btn btn-sm btn-success btnPDF' data-toggle='tooltip' data-placement='top' title='Imprimir'><i class='fas fa-print'></i></button>\
           </div>";
        break;
      case 4: // usuario colaborador
        columnas =
          "<div class='text-center btn-group'>\
          <button class='btn btn-sm btn-primary btnVer' data-toggle='tooltip' data-placement='top' title='Consultar'><i class='fas fa-search'></i></button>\
           <button class='btn btn-sm btn-success btnPDF' data-toggle='tooltip' data-placement='top' title='Imprimir'><i class='fas fa-print'></i></button>\
           </div>";

        break;
      case 5: // usuario capturista
        columnas = "";
        break;
      default:
        columnas = "";

        break;
    }
    return columnas;
  }

  $("#tablaV").DataTable({
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
    order: [[0, "desc"]],
    columnDefs: [
      { targets: [0, 6, 7, 8, 9, 10], className: "text-center" },
      { targets: [5, 6], className: "text-right" },
      {
        targets: -1,
        data: null,
        defaultContent: textcolumnas,
      },
    ],
  });
  $(document).on("click", ".btnVer", function() {
     fila = $(this).closest("tr");
        id = parseInt(fila.find('td:eq(0)').text());
    window.location.href = "pcot.php?folio=" + id;
  });
  $(document).on("click", ".btnPDF", function () {
    var data = $("#tablaV").DataTable().row($(this).parents("tr")).data();
    var id = data[0];
    window.open("formatos/generarcot2.php?id=" + id, "_blank");
  });

});
