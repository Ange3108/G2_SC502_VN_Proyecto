document.addEventListener("DOMContentLoaded", function () {
  const diasEnMes = 31; // Agosto
  const calendar = document.getElementById("calendar");

  // Eventos con cupo
  const eventos = [
    { dia: 3, titulo: "Clase especial de bordado", cupoDisponible: true },
    { dia: 10, titulo: "Sesión abierta: Técnicas mixtas", cupoDisponible: false },
    { dia: 22, titulo: "Cafecito del Día de las Madres", cupoDisponible: true },
  ];

  for (let i = 1; i <= diasEnMes; i++) {
    const diaDiv = document.createElement("div");
    diaDiv.className = "calendar-day";

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
    }

    calendar.appendChild(diaDiv);
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
});
