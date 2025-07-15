document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");
  const registerError = document.getElementById("register-error");
  const registerSuccess = document.getElementById("register-success");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("emailRegister").value.trim();
    const password = document.getElementById("passwordRegister").value.trim();
    const confirmPassword = document
      .getElementById("confirmPassword")
      .value.trim();

    registerError.style.display = "none";
    registerSuccess.style.display = "none";

    if (password !== confirmPassword) {
      registerError.textContent = "Las contraseñas no coinciden.";
      registerError.style.display = "block";
      return;
    }

    // Obtener usuarios existentes o inicializar un array vacío
    let usuariosRegistrados =
      JSON.parse(localStorage.getItem("usuariosRegistrados")) || [];

    // Verificar si el correo ya está registrado
    const emailExists = usuariosRegistrados.some((user) => user.email === email);
    if (emailExists) {
      registerError.textContent = "Este correo electrónico ya está registrado.";
      registerError.style.display = "block";
      return;
    }

    // Agregar el nuevo usuario
    usuariosRegistrados.push({ name, email, password });
    localStorage.setItem("usuariosRegistrados", JSON.stringify(usuariosRegistrados));

    registerSuccess.textContent = "¡Registro exitoso! Redirigiendo al inicio de sesión...";
    registerSuccess.style.display = "block";

    // Opcional: redirigir al login después de un breve tiempo
    setTimeout(() => {
      window.location.href = "Login.html";
    }, 2000); // Redirige después de 2 segundos
  });
});