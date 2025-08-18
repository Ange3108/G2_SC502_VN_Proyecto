document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const mesActual = document.getElementById("mesActual");
  const btnPrev = document.getElementById("prevMonth");
  const btnNext = document.getElementById("nextMonth");
<<<<<<< HEAD

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
=======
  const listaEventos = document.getElementById("listaEventos");

  const meses = [
    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
    "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
>>>>>>> main
  ];

  const diasSemana = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];

  const eventos = [
<<<<<<< HEAD
    { dia: 3, mes: 7, titulo: "Clase especial de bordado" }, // Agosto (mes 7)
    { dia: 10, mes: 7, titulo: "Sesión abierta: Técnicas mixtas" },
    { dia: 22, mes: 7, titulo: "Cafecito del Día de las Madres" },
    { dia: 5, mes: 8, titulo: "Bienvenida a septiembre" }, // Septiembre
=======
    { dia: 3, mes: 7, titulo: "Clase especial de bordado", cupo: true }, // Agosto
    { dia: 10, mes: 7, titulo: "Sesión abierta: Técnicas mixtas", cupo: false },
    { dia: 22, mes: 7, titulo: "Cafecito del Día de las Madres", cupo: true },
    { dia: 5, mes: 8, titulo: "Bienvenida a septiembre", cupo: true }, // Septiembre
>>>>>>> main
  ];

  let currentMonth = new Date().getMonth(); // mes actual
  let currentYear = new Date().getFullYear();

  function getDiasEnMes(mes, año) {
    return new Date(año, mes + 1, 0).getDate();
  }

  function renderCalendar(mes) {
    calendar.innerHTML = "";
    mesActual.textContent = `${meses[mes]} ${currentYear}`;
<<<<<<< HEAD

    // Día de la semana en que comienza el mes
    const firstDay = new Date(currentYear, mes, 1).getDay();
    const diasEnMes = getDiasEnMes(mes, currentYear);

    // Agregar encabezado de días
=======
    listaEventos.innerHTML = "";

    // Día de inicio del mes
    const firstDay = new Date(currentYear, mes, 1).getDay();
    const diasEnMes = getDiasEnMes(mes, currentYear);

    // Encabezado de días
>>>>>>> main
    diasSemana.forEach((dia) => {
      const diaSemana = document.createElement("div");
      diaSemana.className = "calendar-day dia-semana";
      diaSemana.textContent = dia;
      calendar.appendChild(diaSemana);
    });

<<<<<<< HEAD
    //Espacios vacíos antes del día 1
=======
    // Espacios vacíos antes del día 1
>>>>>>> main
    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day invisible";
      calendar.appendChild(emptyDiv);
    }

<<<<<<< HEAD
    //Días del mes
=======
    // Días del mes
>>>>>>> main
    for (let i = 1; i <= diasEnMes; i++) {
      const diaDiv = document.createElement("div");
      diaDiv.className = "calendar-day";

      const evento = eventos.find((ev) => ev.dia === i && ev.mes === mes);
      if (evento) {
        diaDiv.classList.add("evento");
<<<<<<< HEAD
        diaDiv.innerHTML = `<strong>${i}</strong><br>${evento.titulo}`;
=======
        diaDiv.innerHTML = `
          <strong>${i}</strong><br>
          ${evento.titulo}<br>
          ${evento.cupo
            ? `<button class="btn btn-sm btn-success mt-2">Inscribirse</button>`
            : `<span class="text-danger fw-bold d-block mt-2">Cupo lleno</span>`}
        `;

        // Agregar a la lista del modal
        const li = document.createElement("li");
        li.className = "list-group-item";
        li.textContent = `Día ${evento.dia}: ${evento.titulo}${evento.cupo ? '' : ' (Cupo lleno)'}`;
        listaEventos.appendChild(li);
>>>>>>> main
      } else {
        diaDiv.innerHTML = `<strong>${i}</strong>`;
      }

      calendar.appendChild(diaDiv);
    }
<<<<<<< HEAD
=======

    // Mostrar modal con eventos si hay para el mes
    if (listaEventos.children.length > 0) {
      const modal = new bootstrap.Modal(document.getElementById("modalEventos"));
      modal.show();
    }
>>>>>>> main
  }

  btnPrev.addEventListener("click", () => {
    currentMonth = (currentMonth - 1 + 12) % 12;
    renderCalendar(currentMonth);
  });

  btnNext.addEventListener("click", () => {
    currentMonth = (currentMonth + 1) % 12;
    renderCalendar(currentMonth);
  });

  renderCalendar(currentMonth);
});
