$(document).ready(function () {
    // Configurar colaborador automáticamente si es rol colaborador
    configurarColaborador();

    // Botón Nuevo
    $("#btnNuevo").click(function () {
        limpiarFormulario();
        $("#fecha_apartado").val(new Date().toISOString().split('T')[0]);
        configurarColaborador();
    });

    // Botón Guardar
    $("#btnGuardar").click(function () {
        guardarApartado();
    });

    // Botón Buscar Prospecto
    $("#btnBuscarProspecto").click(function () {
        cargarProspectos();
        $("#modalProspecto").modal("show");
    });

    // Botón Buscar Lote
    $("#btnBuscarLote").click(function () {
        $("#bproyecto").val("").trigger("change");
        $("#tablaLote").DataTable().clear().draw();
        $("#modalLote").modal("show");
        if ($("#bproyecto option").length > 1) {
            $("#bproyecto").prop("selectedIndex", 1).trigger("change");
        }
    });

    // Función para configurar colaborador automáticamente
    function configurarColaborador() {
        const tipoUsuario = $("#tipousuario").val();
        const idCol = $("#idcol").val();
        
        if (tipoUsuario == "4") { // Rol colaborador
            $("#col_asignado").val(idCol);
            $("#col_asignado").selectpicker('refresh');
            $("#col_asignado").prop('disabled', true);
        } else {
            $("#col_asignado").prop('disabled', false);
        }
    }

    // Función para limpiar formulario
    function limpiarFormulario() {
        $("#folio").val("");
        $("#id_prospecto").val("");
        $("#nombre_prospecto").val("");
        $("#id_proyecto").val("");
        $("#proyecto").val("");
        $("#id_manzana").val("");
        $("#manzana").val("");
        $("#id_lote").val("");
        $("#lote").val("");
        $("#superficie").val("");
        $("#preciom").val("");
        $("#valortotal").val("");
        $("#importe_apartado").val("");
        $("#observaciones").val("");
    }

    // Función para cargar prospectos
    function cargarProspectos() {
        if ($.fn.DataTable.isDataTable("#tablaProspecto")) {
            $("#tablaProspecto").DataTable().destroy();
        }

        $("#tablaProspecto").DataTable({
            ajax: {
                url: "bd/get_cliente.php",
                type: "POST",
                dataSrc: ""
            },
            columns: [
                { data: "id_clie" },
                { data: "nombre" },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button class="btn btn-sm btn-primary seleccionar-prospecto" 
                                data-id="${row.id_clie}" 
                                data-nombre="${row.nombre}">
                                <i class="fas fa-check mr-1"></i>
                            </button>`;
                    },
                    orderable: false
                }
            ],
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            columnDefs: [
                {
                    targets: 2,
                    className: "text-center",
                }   
            ]

        });
    }

    // Seleccionar prospecto
    $("#tablaProspecto").on("click", ".seleccionar-prospecto", function () {
        const id = $(this).data("id");
        const nombre = $(this).data("nombre");
        
        $("#id_prospecto").val(id);
        $("#nombre_prospecto").val(nombre);
        $("#modalProspecto").modal("hide");
    });

    // Inicializar DataTable para lotes
    var tableLote = $("#tablaLote").DataTable({
        columns: [
            { data: "id_lote", visible: false },
            { data: "clave_lote" },
            {
                data: "superficie",
                className: "text-right",
                render: function (data) {
                    return parseFloat(data).toFixed(2) + " m²";
                }
            },
            {
                data: "preciom",
                className: "text-right",
                render: function (data) {
                    return parseFloat(data).toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN"
                    });
                }
            },
            {
                data: "valortotal",
                className: "text-right",
                render: function (data) {
                    return parseFloat(data).toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN"
                    });
                }
            },
            {
                data: "status",
                 className: "text-center",
                render: function (data) {
                    const statusMap = {
                        "DISPONIBLE": "badge-success",
                        "APARTADO": "badge-warning",
                        "VENDIDO": "badge-danger"
                    };
                    const badgeClass = statusMap[data] || "badge-secondary";
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            {
                data: null,
                 className: "text-center",
                render: function (data, type, row) {
                    return `<button class="btn btn-sm btn-primary seleccionar-lote" 
                            data-id="${row.id_lote}" 
                            data-clave="${row.clave_lote}"
                            data-superficie="${row.superficie}"
                            data-preciom="${row.preciom}"
                            data-valortotal="${row.valortotal}">
                            <i class="fas fa-check mr-1"></i>
                        </button>`;
                },
                orderable: false
            }
        ],
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
           
    });

    // Cargar manzanas cuando cambia el proyecto
    $("#bproyecto").change(function () {
        const idProyecto = $(this).val();
        const selectManzana = $("#bmanzana");

        if (!idProyecto) {
            selectManzana.empty().append('<option value="">-- Seleccione Manzana --</option>').prop("disabled", true);
            tableLote.clear().draw();
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
                        selectManzana.append(`<option value="${manzana.id_man}">${manzana.descripcion}</option>`);
                    });
                    selectManzana.prop("selectedIndex", 0).trigger("change");
                }
            }
        });
    });

    // Cargar lotes cuando cambia la manzana
    $("#bmanzana").change(function () {
        const idProyecto = $("#bproyecto").val();
        const idManzana = $(this).val();

        if (!idProyecto) return;

        $.ajax({
            url: "bd/get_lotes.php",
            method: "POST",
            data: { id_proy: idProyecto, id_man: idManzana },
            dataType: "json",
            success: function (response) {
                tableLote.clear().rows.add(response).draw();
            }
        });
    });

    // Seleccionar lote
    $("#tablaLote").on("click", ".seleccionar-lote", function () {
        const row = $("#tablaLote").DataTable().row($(this).closest("tr")).data();

        if (row.status.toUpperCase() !== "DISPONIBLE") {
            Swal.fire({
                icon: "warning",
                title: "Lote no disponible",
                text: `El lote seleccionado está ${row.status.toLowerCase()}.`
            });
            return;
        }

        const proyectoTexto = $("#bproyecto option:selected").text();
        const manzanaTexto = $("#bmanzana option:selected").text();

        $("#id_lote").val($(this).data("id"));
        $("#lote").val($(this).data("clave"));
        $("#superficie").val($(this).data("superficie"));
        $("#preciom").val($(this).data("preciom"));
        $("#valortotal").val($(this).data("valortotal"));
        $("#proyecto").val(proyectoTexto);
        $("#manzana").val(manzanaTexto);
        $("#id_proyecto").val($("#bproyecto").val());
        $("#id_manzana").val($("#bmanzana").val());

        $("#modalLote").modal("hide");
    });

    // Función para guardar apartado
    function guardarApartado() {
        // Validaciones
        if (!$("#id_prospecto").val()) {
            Swal.fire("Error", "Debe seleccionar un prospecto", "error");
            return;
        }

        if (!$("#id_lote").val()) {
            Swal.fire("Error", "Debe seleccionar un lote", "error");
            return;
        }

        if (!$("#importe_apartado").val()) {
            Swal.fire("Error", "Debe ingresar el importe del apartado", "error");
            return;
        }

        if (!$("#col_asignado").val()) {
            Swal.fire("Error", "Debe seleccionar un colaborador", "error");
            return;
        }

        const datos = {
            folio: $("#folio").val(),
            id_prospecto: $("#id_prospecto").val(),
            fecha_apartado: $("#fecha_apartado").val(),
            id_lote: $("#id_lote").val(),
            id_proyecto: $("#id_proyecto").val(),
            id_manzana: $("#id_manzana").val(),
            importe_apartado: $("#importe_apartado").val().replace(/[^0-9.-]+/g, ""),
            observaciones: $("#observaciones").val(),
            col_asignado: $("#col_asignado").val(),
            id_usuario: $("#iduser").val()
        };

        $.ajax({
            url: "bd/guardar_apartado.php",
            method: "POST",
            dataType: "json",
            data: JSON.stringify(datos),
            contentType: "application/json",
            success: function (response) {
                if (response.success) {
                    Swal.fire("¡Éxito!", "El apartado se guardó correctamente", "success");
                    $("#folio").val(response.folio);
                } else {
                    Swal.fire("Error", response.message || "Error al guardar", "error");
                }
            },
            error: function () {
                Swal.fire("Error", "Error de conexión", "error");
            }
        });
    }

    // Formatear campo de importe
    $("#importe_apartado").on("blur", function () {
        let value = parseFloat($(this).val().replace(/[^0-9.-]+/g, ""));
        if (!isNaN(value)) {
            $(this).val(value.toLocaleString("es-MX", {
                style: "currency",
                currency: "MXN"
            }));
        }
    });

    // Configurar colaborador al cargar la página
    configurarColaborador();
});