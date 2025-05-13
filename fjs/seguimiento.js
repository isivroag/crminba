$(document).ready(function () {
  // Validación al guardar seguimiento
  $("#formSeguimiento").on("submit", function (e) {
    e.preventDefault();

    let tipo_seg = $("#tipo_seg").val();
    let fecha_seg = $("#fecha_seg").val();
    let realizado = $("#realizado").val();
    let id_col = $("#id_col").val();
    let id_pros = $("#id_pros").val();
    let id_seg = $("#id_seg").val();
    let comentarios = $("#observaciones").val();
    console.log("ID Seg:", id_seg);
    console.log(opcion = id_seg ? 2 : 1);
    if (id_seg && parseInt(id_seg) > 0) {
      opcion = 2;
    } else {
      opcion = 1;
    }

    console.log("ID Pros:", id_pros);
    console.log("Tipo Seg:", tipo_seg);
    console.log("Fecha Seg:", fecha_seg);
    console.log("Realizado:", realizado);
    console.log("ID Col:", id_col);
    console.log("Comentarios:", comentarios);

    // Validaciones básicas
    if (!tipo_seg || !fecha_seg || !realizado || !id_col) {
      Swal.fire({
        title: "Campos Incompletos",
        text: "Por favor completa todos los campos obligatorios.",
        icon: "warning",
      });
      return;
    }

    // Validar fecha si ya se realizó
    const hoy = new Date().toISOString().split("T")[0];
    if (realizado === "1" && fecha_seg > hoy) {
      Swal.fire({
        title: "Fecha Inválida",
        text: "No puedes indicar que ya se realizó con una fecha futura.",
        icon: "error",
      });
      return;
    }

    if (realizado === "0" && fecha_seg < hoy) {
      Swal.fire({
        title: "Fecha Inválida",
        text: "No puedes agendar una acción con una fecha anterior a hoy.",
        icon: "warning",
      });
      return;
    }

    var formData = $(this).serializeArray(); // convierte el formulario en array
    formData.push({ name: "opcion", value: opcion });
    // Enviar vía AJAX
    $.ajax({
      url: "bd/guardar_seguimiento.php",
      type: "POST",
      data: {
      tipo_seg: tipo_seg,
      fecha_seg: fecha_seg,
      realizado: realizado,
      comentarios: comentarios,
      id_col: id_col,
      id_pros: id_pros,
      id_seg: id_seg,
      opcion: opcion
    },
      success: function (response) {
        try {
          const res = JSON.parse(response);
          if (res.success) {
            Swal.fire({
              title: "Seguimiento Guardado",
              text: res.message || "La acción ha sido registrada exitosamente.",
              icon: "success",
              timer: 2000,
              showConfirmButton: false,
            });
            $("#formSeguimiento")[0].reset();
          } else {
            Swal.fire({
              title: "Error",
              text: res.message || "Ocurrió un error al guardar.",
              icon: "error",
            });
          }
        } catch (e) {
          console.error("Error al interpretar la respuesta:", e);
          Swal.fire({
            title: "Error",
            text: "Error inesperado en la respuesta del servidor.",
            icon: "error",
          });
        }
      },
      error: function (xhr, status, error) {
        console.error("Error en AJAX:", error);
        Swal.fire({
          title: "Error de Conexión",
          text: "No se pudo guardar el seguimiento. Intenta más tarde.",
          icon: "error",
        });
      },
    });
  });
});
