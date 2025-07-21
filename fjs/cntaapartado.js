$(document).ready(function () {
    var tablaVis;
    var opcion;
    var fila;
    var id_apartado;

    // Inicializar DataTable
    tablaVis = $("#tablaV").DataTable({
        responsive: true,
        columnDefs: [
            { targets: [0, 1, 3, 6, 11, 12, 14], className: "text-center" },
            { targets: [7, 8, 9], className: "text-right" },
            { targets: 14, orderable: false, searchable: false }
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
        order: [[1, "desc"]]
    });

    // Función para formatear moneda
    function formatCurrency(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    }

    // Función para parsear moneda
    function parseCurrency(value) {
        return parseFloat(value.replace(/[^\d.-]/g, ''));
    }

    // Botón Editar
    $(document).on("click", ".btnEditar", function () {
        fila = $(this).closest("tr");
        id_apartado = $(this).data("id");

        // Obtener datos de la fila
        var fecha = fila.find("td:eq(1)").text();
        var importe = fila.find("td:eq(9)").text();
        var colaborador = fila.find("td:eq(10)").text();
        var observaciones = fila.find("td:eq(13)").text();

        // Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
        var fechaParts = fecha.split('/');
        var fechaFormatted = fechaParts[2] + '-' + fechaParts[1] + '-' + fechaParts[0];

        // Rellenar el formulario
        $("#id_apartado").val(id_apartado);
        $("#fecha_apartado").val(fechaFormatted);
        $("#importe_apartado").val(importe);
        $("#observaciones").val(observaciones);

        // Buscar y seleccionar colaborador
        $("#col_asignado option").each(function () {
            if ($(this).text() === colaborador) {
                $(this).prop('selected', true);
                return false;
            }
        });
        $("#col_asignado").selectpicker('refresh');

        opcion = 2; // Editar
        $(".modal-header").css("background-color", "#17a2b8");
        $(".modal-title").text("EDITAR APARTADO");
        $("#modalCRUD").modal("show");
    });

    // Botón Cancelar
    $(document).on("click", ".btnCancelar", function () {
        id_apartado = $(this).data("id");
        fila = $(this).closest("tr");

        Swal.fire({
            title: "¿Cancelar apartado?",
            text: "Esta acción no se puede deshacer",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, cancelar",
            cancelButtonText: "No"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "bd/cancelar_apartado.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        id_apartado: id_apartado
                    },
                    success: function (data) {
                        if (data.success) {
                            Swal.fire("¡Cancelado!", "El apartado ha sido cancelado", "success");
                            
                            // Actualizar la fila
                            fila.find("td:eq(12)").html('<span class="badge badge-danger">CANCELADO</span>');
                            fila.find("td:eq(14)").html('<button class="btn btn-sm btn-info btnVer" data-id="' + id_apartado + '" title="Ver Detalles"><i class="fas fa-eye"></i></button>');
                            fila.removeClass('apartado-warning apartado-danger');
                        } else {
                            Swal.fire("Error", data.message || "Error al cancelar", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });

    // Botón Convertir
    $(document).on("click", ".btnConvertir", function () {
        id_apartado = $(this).data("id");
        fila = $(this).closest("tr");

        Swal.fire({
            title: "¿Convertir a venta?",
            text: "El apartado se convertirá en una venta",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, convertir",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "bd/convertir_apartado.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        id_apartado: id_apartado
                    },
                    success: function (data) {
                        if (data.success) {
                            Swal.fire("¡Convertido!", "El apartado ha sido convertido a venta", "success");
                            
                            // Actualizar la fila
                            fila.find("td:eq(12)").html('<span class="badge badge-primary">CONVERTIDO</span>');
                            fila.find("td:eq(14)").html('<button class="btn btn-sm btn-info btnVer" data-id="' + id_apartado + '" title="Ver Detalles"><i class="fas fa-eye"></i></button>');
                            fila.removeClass('apartado-warning apartado-danger');
                        } else {
                            Swal.fire("Error", data.message || "Error al convertir", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error", "Error de conexión", "error");
                    }
                });
            }
        });
    });

    // Botón Guardar (Editar)
    $("#btnGuardar").click(function () {
        var fecha_apartado = $("#fecha_apartado").val();
        var importe_apartado = $("#importe_apartado").val();
        var col_asignado = $("#col_asignado").val();
        var observaciones = $("#observaciones").val();

        if (!fecha_apartado || !importe_apartado || !col_asignado) {
            Swal.fire("Error", "Todos los campos obligatorios deben ser completados", "error");
            return;
        }

        $.ajax({
            url: "bd/editar_apartado.php",
            type: "POST",
            dataType: "json",
            data: {
                id_apartado: id_apartado,
                fecha_apartado: fecha_apartado,
                importe_apartado: parseCurrency(importe_apartado),
                col_asignado: col_asignado,
                observaciones: observaciones
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire("¡Éxito!", "Apartado actualizado correctamente", "success");
                    
                    // Actualizar la fila
                    var fechaFormatted = fecha_apartado.split('-').reverse().join('/');
                    fila.find("td:eq(1)").text(fechaFormatted);
                    fila.find("td:eq(9)").text(formatCurrency(parseCurrency(importe_apartado)));
                    fila.find("td:eq(10)").text($("#col_asignado option:selected").text());
                    fila.find("td:eq(13)").text(observaciones);
                    
                    $("#modalCRUD").modal("hide");
                } else {
                    Swal.fire("Error", data.message || "Error al actualizar", "error");
                }
            },
            error: function () {
                Swal.fire("Error", "Error de conexión", "error");
            }
        });
    });

    // Formatear campo de importe
    $("#importe_apartado").on("blur", function () {
        let value = parseCurrency($(this).val());
        if (!isNaN(value)) {
            $(this).val(formatCurrency(value));
        }
    });

    // Limpiar formato al enfocar
    $("#importe_apartado").on("focus", function () {
        let value = parseCurrency($(this).val());
        if (!isNaN(value)) {
            $(this).val(value);
        }
    });

    // Actualizar automáticamente cada 30 segundos
    setInterval(function() {
        location.reload();
    }, 30000);
});