const tutoriales = [
  {
    titulo: "Crochet para principiantes | 8 Puntos básicos para hacer Amiguramis 🧸🍯",
    descripcion: "Aprendé técnica básicas de crochet, puntos más usados y consejos.",
    enlace: "https://youtu.be/GaYaVjM71xg"
  },
  {
    titulo: "Conejito Base a Crochet | Tutorial Paso a Paso | Fácil y Rápido",
    descripcion: "Una guía fácil para empezar con una bonita figura.",
    enlace: "https://youtu.be/PdX2Ex1ciJk"
  }
];

function mostrarTutoriales() {
  const contenedor = document.getElementById("lista-tutoriales");
  
  tutoriales.forEach(tutorial => {
    const div = document.createElement("div");
    div.classList.add("tarjeta-tutorial");

    div.innerHTML = `
      <h3>${tutorial.titulo}</h3>
      <p>${tutorial.descripcion}</p>
      <a href="${tutorial.enlace}" target="_blank">Ver tutorial</a>
    `;

    contenedor.appendChild(div); //meter una cajita dentro de otra cajita
  });
}

document.addEventListener("DOMContentLoaded", mostrarTutoriales);
