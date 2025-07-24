// LaCrocherita/Login/js/register.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");
  const registerMessageDiv = document.getElementById("register-message");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); 

    // Oculta el mensaje anterior
    registerMessageDiv.style.display = "none";
    registerMessageDiv.className = "mt-3 text-center"; // Resetea clases

    const formData = new FormData(form); // Obtiene los datos del formulario

    fetch("../php/proceso_autenticacion.php", { //ruta al PHP que procesa el registro
      method: "POST",
      body: formData,
    })
      .then((response) => response.json()) // Espera una respuesta JSON
      .then((data) => {
        // Muestra el mensaje de la respuesta PHP
        registerMessageDiv.textContent = data.message;
        registerMessageDiv.style.display = "block";
        if (data.type === "success") {
          registerMessageDiv.classList.add("text-success");
          // Si PHP no redirigiera, se haria aquí: setTimeout(() => { window.location.href = "Login.html"; }, 2000);
        } else if (data.type === "warning") {
            registerMessageDiv.classList.add("text-warning");
        }
        else {
          registerMessageDiv.classList.add("text-danger");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        registerMessageDiv.textContent = "Error al procesar la solicitud. Inténtalo de nuevo.";
        registerMessageDiv.classList.add("text-danger");
        registerMessageDiv.style.display = "block";
      });
  });
});