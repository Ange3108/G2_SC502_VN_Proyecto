// LaCrocherita/Login/js/contra_olvidada.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("forgotPasswordForm");
  const recoveryModal = document.getElementById("recoveryModal");
  const closeButton = document.querySelector(".close-button");
  const closeModalBtn = document.getElementById("closeModalBtn");
  const recoveryEmailDisplay = document.getElementById("recoveryEmailDisplay");
  const emailInput = document.getElementById("emailRecovery");

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const email = emailInput.value.trim();
    recoveryEmailDisplay.textContent = email; // Muestra el correo en el popup

    const formData = new FormData(form); // Obtiene los datos del formulario

    fetch("../php/auth_process.php", {
        method: "POST",
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        // Aunque el PHP solo devuelve un mensaje informativo, se puede mostrar aquí
        // o usar el modal para la confirmación visual.
        // Aquí se puede decidir si seguir usando el modal para la confirmación
        recoveryModal.style.display = "flex"; // Muestra el modal
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Ocurrió un error al intentar recuperar la contraseña. Por favor, inténtalo de nuevo.");
    });
  });

  // Cuando el usuario hace clic en(x), cierra el modal
  closeButton.addEventListener("click", function () {
    recoveryModal.style.display = "none";
  });

  // Cuando el usuario hace clic en el botón "Entendido", cierra el modal
  closeModalBtn.addEventListener("click", function () {
    recoveryModal.style.display = "none";
  });

  // Cuando el usuario hace clic en cualquier lugar fuera del modal, lo cierra
  window.addEventListener("click", function (event) {
    if (event.target == recoveryModal) {
      recoveryModal.style.display = "none";
    }
  });
});