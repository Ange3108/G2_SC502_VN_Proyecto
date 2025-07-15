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
    recoveryModal.style.display = "flex"; // Muestra el modal
  });

  // Cuando el usuario hace clic en <span> (x), cierra el modal
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