//En este script se hacen las redirecciones de comunidad.html cuando se haga click en cada sección
document.addEventListener("DOMContentLoaded", () => {
  inicializarRedirecciones();
});

function inicializarRedirecciones() {
  asignarRedireccion(".clases", "secciones/clases.html");
  asignarRedireccion(".eventos", "secciones/eventos.html");
  asignarRedireccion(".recordatorio", "secciones/recordatorio.html");
  asignarRedireccion(".asistencia", "secciones/asistencia.html");
  asignarRedireccion(".Chat", "#");
}

function asignarRedireccion(selector, rutaDestino) {
  const elemento = document.querySelector(selector);
  if (elemento) {
    elemento.addEventListener("click", () => {
      window.location.href = rutaDestino;
    });
  }
}
