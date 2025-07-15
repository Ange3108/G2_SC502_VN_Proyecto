document.addEventListener("DOMContentLoaded", function () {
  const infoUsuario = document.getElementById("info-usuario");

  // Asegurate de declarar usuarioActual primero
  const usuarioActual = JSON.parse(localStorage.getItem("usuarioActual"));

  if (usuarioActual) {
    infoUsuario.innerHTML = `
      <p><strong>Nombre:</strong> ${usuarioActual.name || "Usuario sin nombre"}</p>
      <p><strong>Email:</strong> ${usuarioActual.email}</p>
    `;
  } else {
    infoUsuario.textContent = "No has iniciado sesión.";
  }
});

