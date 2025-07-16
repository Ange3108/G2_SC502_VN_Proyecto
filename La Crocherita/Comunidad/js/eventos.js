document.addEventListener("DOMContentLoaded", function () {
  const diasEnMes = 31; // Agosto

  const calendar = document.getElementById("calendar");
  const mesActual = document.getElementById("mesActual");
  const btnPrev = document.getElementById("prevMonth");
  const btnNext = document.getElementById("nextMonth");

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

  // Eventos con cupo
  const eventos = [

    { dia: 3, mes: 7, titulo: "Clase especial de bordado" }, // Agosto (mes 7)
    { dia: 10, mes: 7, titulo: "Sesión abierta: Técnicas mixtas" },
    { dia: 22, mes: 7, titulo: "Cafecito del Día de las Madres" },
    { dia: 5, mes: 8, titulo: "Bienvenida a septiembre" }, // Septiembre
  ];

  let currentMonth = new Date().getMonth(); // mes actual
  let currentYear = new Date().getFullYear();

  function getDiasEnMes(mes, año) {
    return new Date(año, mes + 1, 0).getDate();
  }

  function renderCalendar(mes) {
    calendar.innerHTML = "";
    mesActual.textContent = `${meses[mes]} ${currentYear}`;

    // Día de la semana en que comienza el mes
    const firstDay = new Date(currentYear, mes, 1).getDay();
    const diasEnMes = getDiasEnMes(mes, currentYear);

    const evento = eventos.find((ev) => ev.dia === i);
    if (evento) {
      diaDiv.classList.add("evento");
      diaDiv.innerHTML = `
        <strong>${i}</strong><br>
        ${evento.titulo}<br>
        ${evento.cupoDisponible
          ? `<button class="btn btn-sm btn-success mt-2">Inscribirse</button>`
          : `<span class="text-danger fw-bold mt-2 d-block">Cupo lleno</span>`}
      `;
    } else {
      diaDiv.innerHTML = `<strong>${i}</strong>`;

    // Agregar encabezado de días
    diasSemana.forEach((dia) => {
      const diaSemana = document.createElement("div");
      diaSemana.className = "calendar-day dia-semana";
      diaSemana.textContent = dia;
      calendar.appendChild(diaSemana);
    });

    //Espacios vacíos antes del día 1
    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day invisible";
      calendar.appendChild(emptyDiv);
    }

    //Días del mes
    for (let i = 1; i <= diasEnMes; i++) {
      const diaDiv = document.createElement("div");
      diaDiv.className = "calendar-day";

      const evento = eventos.find((ev) => ev.dia === i && ev.mes === mes);
      if (evento) {
        diaDiv.classList.add("evento");
        diaDiv.innerHTML = `<strong>${i}</strong><br>${evento.titulo}`;
      } else {
        diaDiv.innerHTML = `<strong>${i}</strong>`;
      }

      calendar.appendChild(diaDiv);
    }
  }

  // Llenar el contenido del modal
  const listaEventos = document.getElementById("listaEventos");
  eventos.forEach(ev => {
    const li = document.createElement("li");
    li.className = "list-group-item";
    li.textContent = `Día ${ev.dia}: ${ev.titulo}${ev.cupoDisponible ? '' : ' (Cupo lleno)'}`;
    listaEventos.appendChild(li);
  });

  // Mostrar el modal automáticamente al cargar
  const modal = new bootstrap.Modal(document.getElementById("modalEventos"));
  modal.show();
=======
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
