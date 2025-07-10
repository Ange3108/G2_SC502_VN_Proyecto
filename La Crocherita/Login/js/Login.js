document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("loginForm");
  const loginError = document.getElementById("login-error");

  const usuariosRegistrados = [
    {
      email: "mafeh@crocherita.com",
      password: "miCrochet2025",
    },
    {
      email: "admin@crocherita.com",
      password: "admin123",
    },
  ];

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    const usuarioValido = usuariosRegistrados.find(
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
