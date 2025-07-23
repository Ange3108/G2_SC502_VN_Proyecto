// LaCrocherita/Login/js/Login.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  const loginMessageDiv = document.getElementById("login-message");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); 

    // Oculta el mensaje anterior
    loginMessageDiv.style.display = "none";
    loginMessageDiv.className = "mt-3 text-center"; // Resetea clases

    const formData = new FormData(form); // Obtiene los datos del formulario

    fetch("../php/auth_process.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json()) // Espera una respuesta JSON
      .then((data) => {
        // Muestra el mensaje de la respuesta PHP
        loginMessageDiv.textContent = data.message;
        loginMessageDiv.style.display = "block";
        if (data.type === "success") {
          loginMessageDiv.classList.add("text-success");
          // si PHP no redirigiera, se haria aquí: window.location.href = "../../Home/Home.php";
        } else {
          loginMessageDiv.classList.add("text-danger");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        loginMessageDiv.textContent = "Error al procesar la solicitud. Inténtalo de nuevo.";
        loginMessageDiv.classList.add("text-danger");
        loginMessageDiv.style.display = "block";
      });
  });
});