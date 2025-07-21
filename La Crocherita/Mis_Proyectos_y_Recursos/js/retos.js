const retos = [
  {
    titulo:
      "Crochet para principiantes | 8 Puntos básicos para hacer Amiguramis 🧸🍯",
    descripcion:
      "Aprendé técnica básicas de crochet, puntos más usados y consejos.",
    enlace: "https://youtu.be/GaYaVjM71xg",
  },
  {
    titulo: "Conejito Base a Crochet | Tutorial Paso a Paso | Fácil y Rápido",
    descripcion: "Una guía fácil para empezar con una bonita figura.",
    enlace: "https://youtu.be/PdX2Ex1ciJk",
  },
  {
    titulo:
      "CÓMO TEJER GRANNY SQUARE BÁSICO FÁCIL | Ganchillo - Crochet | Lanas y Ovillos",
    descripcion:
      "Aprende a tejer el granny square más básico de una forma sencill y rápida.",
    enlace: "https://youtu.be/h0UXeUn7PDI",
  },
  {
    titulo:
      "Cómo Hacer un MINI CORAZÓN a Crochet Fácil y Rápido ❤️ Paso a Paso",
    descripcion:
      "Un tutorial super fácil y rápido de hacer, ideal para principiantes",
    enlace: "https://youtu.be/FRPKcZpsxk4",
  },
];

function mostrarRetos() {
  const contenedor = document.getElementById("lista-retos");

  retos.forEach((reto) => {
    const div = document.createElement("div");
    div.classList.add("tarjeta-reto");

    div.innerHTML = `
      <h3>${reto.titulo}</h3>
      <p>${reto.descripcion}</p>
      <a href="${reto.enlace}" target="_blank">Ver reto</a>
    `;

    contenedor.appendChild(div); //meter una cajita dentro de otra cajita
  });
}

document.addEventListener("DOMContentLoaded", mostrarRetos);
