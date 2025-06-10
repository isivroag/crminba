$(document).ready(function () {
  jQuery.ajaxSetup({
    beforeSend: function () {
      $("#div_carga").show();
    },
    complete: function () {
      $("#div_carga").hide();
    },
    success: function () {},
  });

  document.getElementById('btnAcotaciones').addEventListener('click', function () {
    const acot = document.getElementById('acotaciones');
    acot.classList.toggle('d-none');
    acot.classList.toggle('d-block');
});

  $("#cotizador").click(function () {
 
    idlote = $("#idlote").val();
    idproy = $("#idproy").val();
    idman = $("#idman").val();
    window.location.href =
      "cot.php?id_lote=" +
      idlote +
      "&id_proy=" +
      idproy +
      "&id_man=" +
      idman;
  });

  const svgOverlay = document.getElementById("svg-overlay");

  // Variables para el zoom
  let scale = 1;
  const zoomIntensity = 0.1;

  function handleWheel(event) {
    event.preventDefault();
    const rect = svgOverlay.getBoundingClientRect();
    const mouseX = event.clientX - rect.left;
    const mouseY = event.clientY - rect.top;

    // Convertir coordenadas de pantalla a coordenadas SVG
    const viewBox = svgOverlay
      .getAttribute("viewBox")
      .split(" ")
      .map(parseFloat);
    const [x, y, width, height] = viewBox;
    const svgX = x + mouseX * (width / rect.width);
    const svgY = y + mouseY * (height / rect.height);

    const wheelDelta = event.deltaY < 0 ? 1 : -1;
    const zoomFactor = Math.exp(wheelDelta * zoomIntensity);
    scale *= zoomFactor;

    const newWidth = 800 / scale;
    const newHeight = 720 / scale;

    // Calcular nuevo viewBox centrado en el punto del mouse
    const newX = svgX - (mouseX / rect.width) * newWidth;
    const newY = svgY - (mouseY / rect.height) * newHeight;

    svgOverlay.setAttribute(
      "viewBox",
      `${newX} ${newY} ${newWidth} ${newHeight}`
    );
  }

  let isDragging = false;
  let startX, startY;
  let viewBoxX, viewBoxY;

  function handleMouseDown(event) {
    isDragging = true;
    startX = event.clientX;
    startY = event.clientY;

    const viewBox = svgOverlay.getAttribute("viewBox").split(" ");
    viewBoxX = parseFloat(viewBox[0]);
    viewBoxY = parseFloat(viewBox[1]);

    svgOverlay.style.cursor = "grabbing";
  }

  function handleMouseMove(event) {
    if (isDragging) {
      const dx = event.clientX - startX;
      const dy = event.clientY - startY;
      const newViewBoxX = viewBoxX - dx / scale;
      const newViewBoxY = viewBoxY - dy / scale;
      svgOverlay.setAttribute(
        "viewBox",
        `${newViewBoxX} ${newViewBoxY} ${800 / scale} ${720 / scale}`
      );
    }
  }

  function handleMouseUp() {
    isDragging = false;
    svgOverlay.style.cursor = "grab";
  }

  svgOverlay.addEventListener("mousedown", handleMouseDown);
  svgOverlay.addEventListener("mousemove", handleMouseMove);
  svgOverlay.addEventListener("mouseup", handleMouseUp);
  svgOverlay.addEventListener("mouseleave", handleMouseUp);
  svgOverlay.addEventListener("wheel", handleWheel);

  // Variables para touch
  let touchStartX, touchStartY;
  let lastTouchDistance = null;

  // Detectar touchstart
  svgOverlay.addEventListener(
    "touchstart",
    function (event) {
      if (event.touches.length === 1) {
        // Un solo dedo (arrastrar)
        isDragging = true;
        touchStartX = event.touches[0].clientX;
        touchStartY = event.touches[0].clientY;

        const viewBox = svgOverlay.getAttribute("viewBox").split(" ");
        viewBoxX = parseFloat(viewBox[0]);
        viewBoxY = parseFloat(viewBox[1]);

        svgOverlay.style.cursor = "grabbing";
      } else if (event.touches.length === 2) {
        // Pinch zoom
        lastTouchDistance = getTouchDistance(event.touches);
      }
    },
    {
      passive: false,
    }
  );

  // Detectar touchmove
  svgOverlay.addEventListener(
    "touchmove",
    function (event) {
      event.preventDefault();

      if (event.touches.length === 1 && isDragging) {
        const dx = event.touches[0].clientX - touchStartX;
        const dy = event.touches[0].clientY - touchStartY;
        const newViewBoxX = viewBoxX - dx / scale;
        const newViewBoxY = viewBoxY - dy / scale;
        svgOverlay.setAttribute(
          "viewBox",
          `${newViewBoxX} ${newViewBoxY} ${980 / scale} ${750 / scale}`
        );
      } else if (event.touches.length === 2) {
        // Zoom (pinch)
        const newDistance = getTouchDistance(event.touches);
        if (lastTouchDistance !== null) {
          const zoomDelta = newDistance / lastTouchDistance;
          scale *= zoomDelta;

          const viewBox = svgOverlay
            .getAttribute("viewBox")
            .split(" ")
            .map(parseFloat);
          const [x, y] = viewBox;
          const newWidth = 980 / scale;
          const newHeight = 750 / scale;

          svgOverlay.setAttribute(
            "viewBox",
            `${x} ${y} ${newWidth} ${newHeight}`
          );
        }
        lastTouchDistance = newDistance;
      }
    },
    {
      passive: false,
    }
  );

  // Finalizar touch
  svgOverlay.addEventListener(
    "touchend",
    function (event) {
      isDragging = false;
      lastTouchDistance = null;
      svgOverlay.style.cursor = "grab";
    },
    {
      passive: false,
    }
  );

  // Función para medir distancia entre dos dedos
  function getTouchDistance(touches) {
    const dx = touches[0].clientX - touches[1].clientX;
    const dy = touches[0].clientY - touches[1].clientY;
    return Math.sqrt(dx * dx + dy * dy);
  }

  function getColorByEstado(estado) {
    switch (estado.toLowerCase()) {
      case "disponible":
        return "#4bae00";
      case "apartado":
        return "#f37a1b";
      case "vendido":
        return "#c20000";
      default:
        return "none";
    }
  }

  fetch("img/mapareserva.svg")
    .then((response) => response.text())
    .then((data) => {
      svgOverlay.innerHTML = data;

      const group = svgOverlay.querySelector("#layer2");
      if (!group) {
        console.error("No se encontró el grupo #layer2");
        return;
      }

      // 🔹 CENTRAR EL SVG AUTOMÁTICAMENTE AL CARGAR
      const bbox = group.getBBox();
      const centerX = bbox.x + bbox.width / 2;
      const centerY = bbox.y + bbox.height / 2;
      const containerWidth = 980;
      const containerHeight = 750;
      const startX = centerX - containerWidth / 2;
      const startY = centerY - containerHeight / 2;

      svgOverlay.setAttribute(
        "viewBox",
        `${startX} ${startY} ${containerWidth} ${containerHeight}`
      );
      scale = 1;

      const paths = svgOverlay.querySelectorAll("#layer2 path");

      $.ajax({
        url: "bd/buscarlot.php",
        type: "POST",
        data: {
          action: "getAllLotes",
        },
        success: function (response) {
          //console.log('Respuesta de la base de datos:', response);
          const lotes = JSON.parse(response);

          paths.forEach((path) => {
            const pathId = path.getAttribute("id");
            path.classList.add("manzana");
            console.log(path);

            const lote = lotes.find((l) => l.id === pathId);
            if (lote) {
              const color = getColorByEstado(lote.status);
              path.style.fill = color;
              //path.style.opacity = 0.75;
            } else {
              path.style.fill = "none";
              // path.style.opacity = 0.5;
            }

            path.addEventListener("click", () => {
              $.ajax({
                url: "bd/buscarlot.php",
                type: "POST",
                data: {
                  id: pathId,
                },
                success: function (response) {
                  const lote = JSON.parse(response);
                  if (lote.error) {
                    alert(lote.error);
                  } else {
                    //document.getElementById('modalManzana').textContent = lote.manzana;
                    $("#modalManzana").val(lote.manzana);
                    //document.getElementById('modalLote').textContent = lote.clave_lote;
                    $("#modalLote").val(lote.clave_lote);
                    $("#idlote").val(lote.clave_lote);
                    $("#idproy").val(lote.id_proy);
                    $("#idman").val(lote.id_man);
                    $("#modalSuperficie").val(
                      Number(lote.superficie).toLocaleString("es-MX", {
                        maximumFractionDigits: 2,
                      })
                    );

                    $("#modalPreciom").val(
                      Number(lote.preciom).toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN",
                      })
                    );

                    $("#modalValor").val(
                      Number(lote.valortotal).toLocaleString("es-MX", {
                        style: "currency",
                        currency: "MXN",
                      })
                    );
                    document.getElementById("modalEstado").textContent =
                      lote.status;

                    const modal = new bootstrap.Modal(
                      document.getElementById("loteModal")
                    );
                    modal.show();
                  }
                },
                error: function (error) {
                  console.error("Error al buscar el lote:", error);
                  alert("No se pudo obtener la información del lote.");
                },
              });
            });
          });
        },
        error: function (error) {
          console.error("Error al obtener los lotes:", error);
        },
      });
    })
    .catch((error) => {
      console.error("Error al cargar el SVG:", error);
      $("#div_carga").hide();
      alert("No se pudo cargar el mapa.");
    });
});
