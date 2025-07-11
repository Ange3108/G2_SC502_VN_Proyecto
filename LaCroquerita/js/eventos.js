document.addEventListener("DOMContentLoaded", function () {
  const diasEnMes = 31; // Propuesta: Agosto
  const calendar = document.getElementById("calendar");

  const eventos = [
    { dia: 3, titulo: "Clase especial de bordado" },
    { dia: 10, titulo: "Sesión abierta: Técnicas mixtas" },
    { dia: 22, titulo: "Cafecito del Día de las Madres" },
  ];

  for (let i = 1; i <= diasEnMes; i++) {
    const diaDiv = document.createElement("div");
    diaDiv.className = "calendar-day";

    const evento = eventos.find((ev) => ev.dia === i);
    if (evento) {
      diaDiv.classList.add("evento");
      diaDiv.innerHTML = `<strong>${i}</strong><br>${evento.titulo}`;
    } else {
      diaDiv.innerHTML = `<strong>${i}</strong>`;
    }

    calendar.appendChild(diaDiv);
  }
});
