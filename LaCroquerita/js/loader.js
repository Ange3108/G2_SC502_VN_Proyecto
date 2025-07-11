// Carga inicial (por defecto carga inicio.html)
document.addEventListener("DOMContentLoaded", () => {
  loadView("views/inicio.html");
});

// Detectar clics en los links del sidebar
document.querySelectorAll(".nav_link[data-view]").forEach(link => {
  link.addEventListener("click", (e) => {
    e.preventDefault();
    const view = link.getAttribute("data-view");
    loadView(view);
  });
});

// Función que carga una vista HTML en el contenedor principal
function loadView(viewPath) {
  fetch(viewPath)
    .then(res => {
      if (!res.ok) throw new Error("No se pudo cargar la vista.");
      return res.text();
    })
    .then(html => {
      document.querySelector(".main-content").innerHTML = html;
    })
    .catch(err => {
      document.querySelector(".main-content").innerHTML = `<p class="text-danger">Error al cargar contenido: ${err.message}</p>`;
    });
}
