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
    
    if (id_seg && parseInt(id_seg) > 0) {
      opcion = 2;
    } else {
      opcion = 1;
    }


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
        opcion: opcion,
      },
      success: function (response) {
        try {
          const res = JSON.parse(response);
          if (res.success) {
            console.log("Respuesta del servidor:", res);
            Swal.fire({
              title: "Seguimiento Guardado",
              text: res.message || "La acción ha sido registrada exitosamente.",
              icon: "success",
              showCancelButton: true,
              confirmButtonText: "Sí, enviar correo",
              cancelButtonText: "No, gracias",
            }).then((result) => {
              if (result.value) {
                // Enviar correo por AJAXç
                console.log("Enviando correo...");
                $.ajax({
                  url: "bd/usarapicorreoseg.php",
                  type: "POST",
                  contentType: "application/json",
                  data: JSON.stringify({ id_seg: res.id_seg || id_seg }), // usa el id_seg que devuelva PHP si lo actualizaste ahí
                  success: function (correoRes) {
                    try {
                      const correoJson = JSON.parse(correoRes);
                      Swal.fire({
                        title: correoJson.success ? "Correo Enviado" : "Error",
                        text: correoJson.message,
                        icon: correoJson.success ? "success" : "error",
                      });
                    } catch (e) {
                      console.error(
                        "Error al interpretar respuesta del correo:",
                        e
                      );
                      Swal.fire({
                        title: "Error",
                        text: "No se pudo interpretar la respuesta del correo.",
                        icon: "error",
                      });
                    }
                  },
                  error: function (xhr, status, error) {
                    console.error("Error al enviar correo:", error);
                    Swal.fire({
                      title: "Error de Conexión",
                      text: "No se pudo enviar el correo.",
                      icon: "error",
                    });
                  },
                });
              }
              $("#formSeguimiento")[0].reset();
            });
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
