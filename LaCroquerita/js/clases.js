// clases.js

// Suponiendo que tienes clases disponibles en tu base de datos (puede venir desde una API o simulado)
const clasesDisponibles = [
  { id: 1, dia: "Lunes", hora: "10:00 am" },
  { id: 2, dia: "Miércoles", hora: "02:00 pm" },
  { id: 3, dia: "Viernes", hora: "06:00 pm" },
];

// Simulación de usuario actual (en una aplicación real, esto vendría del login)
const usuarioActual = { id: 7, nombre: "María Fernanda" };

function cargarHorarios() {
  const select = document.getElementById("selectHorario");
  clasesDisponibles.forEach((clase) => {
    const option = document.createElement("option");
    option.value = clase.id;
    option.textContent = `${clase.dia} - ${clase.hora}`;
    select.appendChild(option);
  });
}

function reservarClase() {
  const horarioSeleccionado = document.getElementById("selectHorario").value;

  if (!horarioSeleccionado) {
    alert("Por favor seleccione un horario antes de reservar.");
    return;
  }

  // Aquí normalmente enviarías los datos al servidor/backend
  console.log(
    `Reserva hecha: Usuario ${usuarioActual.nombre} reservó clase ID ${horarioSeleccionado}`
  );

  const mensaje = document.getElementById("mensajeReserva");
  mensaje.textContent = "Clase reservada correctamente";
  mensaje.style.color = "green";
}

document.addEventListener("DOMContentLoaded", () => {
  cargarHorarios();

  document
    .getElementById("btnReservar")
    .addEventListener("click", reservarClase);
});
