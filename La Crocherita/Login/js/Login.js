document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  const loginError = document.getElementById("login-error");

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    // Obtener usuarios de localStorage
    const usuariosRegistrados =
      JSON.parse(localStorage.getItem("usuariosRegistrados")) || [];

    // Incluir los usuarios predefinidos si aún no existen en localStorage
    const defaultUsers = [
      {
        email: "mafeh@crocherita.com",
        password: "miCrochet2025",
      },
      {
        email: "admin@crocherita.com",
        password: "admin123",
      },
    ];

    // Combinar los usuarios predefinidos con los registrados
    // Evitar duplicados basados en el email
    // Si un usuario ya existe en localStorage, no lo agregamos de nuevo
    const allUsers = [...defaultUsers];
    usuariosRegistrados.forEach((user) => {
      if (!allUsers.some((defaultUser) => defaultUser.email === user.email)) {
        allUsers.push(user);
      }
    });

    const usuarioValido = allUsers.find(
      (user) => user.email === email && user.password === password
    );

    if (usuarioValido) {
      window.location.href = "../Home/Home.html";
    } else {
      loginError.style.display = "block";
      loginError.textContent =
        "Correo o contraseña incorrectos. Intentá de nuevo.";
    }
  });
});