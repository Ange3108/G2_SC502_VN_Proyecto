document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const mesActual = document.getElementById("mesActual");
  const btnPrev = document.getElementById("prevMonth");
  const btnNext = document.getElementById("nextMonth");
  const listaEventos = document.getElementById("listaEventos"); // del modal existente

  //Acceso al archivo puente
  var API = "eventos_api.php";

  const meses = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];
  const diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

  let currentMonth = new Date().getMonth(); // 0..11
  let currentYear = new Date().getFullYear();

  var eventosMes = []; // [{id_evento, nombre_evento, descripcion, fecha_evento:'YYYY-MM-DD'}]

  function getDiasEnMes(mes, año) {
    return new Date(año, mes + 1, 0).getDate();
  }
  function pad2(n) {
    return String(n).padStart(2, "0");
  }
  function ymd(y, m0, d) {
    return y + "-" + pad2(m0 + 1) + "-" + pad2(d);
  }

  function buscarEventosPorDia(y, m0, d) {
    const dStr = ymd(y, m0, d);
    return eventosMes.filter((e) => e.fecha_evento === dStr);
  }

  // Cargar datos (AJAX) y refrescar
  function cargarEventos(anio, mes0, cb) {
    $.getJSON(
      API,
      { accion: "listar", anio: anio, mes: mes0 + 1 },
      function (data) {
        eventosMes = data && data.ok ? data.eventos || [] : [];
        cb();
      }
    ).fail(function () {
      eventosMes = [];
      cb();
    });
  }

  function renderCalendar(mes) {
    calendar.innerHTML = "";
    mesActual.textContent = `${meses[mes]} ${currentYear}`;

    // Encabezado de días
    diasSemana.forEach(function (dia) {
      const d = document.createElement("div");
      d.className = "calendar-day dia-semana";
      d.textContent = dia;
      calendar.appendChild(d);
    });

    const firstDay = new Date(currentYear, mes, 1).getDay();
    const diasEnMes = getDiasEnMes(mes, currentYear);

    // Espacios vacíos antes del día 1
    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day invisible";
      calendar.appendChild(emptyDiv);
    }

    if (listaEventos) listaEventos.innerHTML = "";

    // Días del mes
    for (let i = 1; i <= diasEnMes; i++) {
      const diaDiv = document.createElement("div");
      diaDiv.className = "calendar-day";

      const delDia = buscarEventosPorDia(currentYear, mes, i);

      if (delDia.length > 0) {
        diaDiv.classList.add("evento");
        const titulo = delDia[0].nombre_evento;
        const extra = delDia.length > 1 ? ` (+${delDia.length - 1})` : "";
        diaDiv.innerHTML = `<strong>${i}</strong><br>${titulo}${extra}`;

        // Click: editar o eliminar el primero
        diaDiv.addEventListener("click", function () {
          const ev = delDia[0];
          const elegirEditar = confirm("OK = Editar\nCancelar = Eliminar");
          if (elegirEditar) {
            const nuevoNombre = prompt(
              "Nuevo nombre del evento:",
              ev.nombre_evento
            );
            if (!nuevoNombre) return;
            const nuevaDesc = prompt(
              "Descripción (opcional):",
              ev.descripcion || ""
            );
            $.post(
              API,
              {
                accion: "actualizar",
                id_evento: ev.id_evento,
                nombre_evento: nuevoNombre,
                descripcion: nuevaDesc || "",
                fecha_evento: ev.fecha_evento,
              },
              function (res) {
                try {
                  res = typeof res === "string" ? JSON.parse(res) : res;
                } catch (e) {
                  res = { ok: false };
                }
                if (res && res.ok) recargar();
                else alert("No se pudo actualizar");
              }
            ).fail(function () {
              alert("Error de red al actualizar");
            });
          } else {
            if (!confirm("¿Eliminar este evento?")) return;
            $.post(
              API,
              { accion: "eliminar", id_evento: ev.id_evento },
              function (res) {
                try {
                  res = typeof res === "string" ? JSON.parse(res) : res;
                } catch (e) {
                  res = { ok: false };
                }
                if (res && res.ok) recargar();
                else alert("No se pudo eliminar");
              }
            ).fail(function () {
              alert("Error de red al eliminar");
            });
          }
        });
      } else {
        diaDiv.innerHTML = `<strong>${i}</strong>`;
      }

      // Doble click: crear
      diaDiv.addEventListener("dblclick", function () {
        const nombre = prompt("Nombre del evento:");
        if (!nombre) return;
        const desc = prompt("Descripción (opcional):", "") || "";
        const fecha = ymd(currentYear, mes, i);
        $.post(
          API,
          {
            accion: "crear",
            nombre_evento: nombre,
            descripcion: desc,
            fecha_evento: fecha,
          },
          function (res) {
            try {
              res = typeof res === "string" ? JSON.parse(res) : res;
            } catch (e) {
              res = { ok: false };
            }
            if (res && res.ok) recargar();
            else alert("No se pudo crear");
          }
        ).fail(function () {
          alert("Error de red al crear");
        });
      });

      calendar.appendChild(diaDiv);

      // Llenar lista del modal
      if (listaEventos && delDia.length > 0) {
        delDia.forEach(function (ev) {
          const li = document.createElement("li");
          li.className = "list-group-item";
          const fecha = ev.fecha_evento.split("-");
          li.textContent = `Día ${parseInt(fecha[2], 10)}: ${ev.nombre_evento}`;
          listaEventos.appendChild(li);
        });
      }
    }

    if (listaEventos && listaEventos.children.length > 0) {
      const modal = new bootstrap.Modal(
        document.getElementById("modalEventos")
      );
      modal.show();
    }
  }

  function recargar() {
    cargarEventos(currentYear, currentMonth, function () {
      renderCalendar(currentMonth);
    });
  }

  btnPrev.addEventListener("click", function () {
    currentMonth = (currentMonth - 1 + 12) % 12;
    recargar();
  });
  btnNext.addEventListener("click", function () {
    currentMonth = (currentMonth + 1) % 12;
    recargar();
  });

  recargar();
});
