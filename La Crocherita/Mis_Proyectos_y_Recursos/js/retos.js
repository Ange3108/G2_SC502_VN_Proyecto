const retos = [
  {
    titulo:
      "Tutorial de dijes de mar a Crochet",
    descripcion:
      "Teje diferentes dijes de mar a crochet con este tutorial paso a paso.",
    enlace: "https://youtu.be/yawoBn6bSFo?si=-fTmt-dtuSryyH_W",
  },
  {
    titulo: "🙊MONITO a crochet 🩷 - Changuito tierno para llavero- TUTORIAL paso a paso, SUB ENG",
    descripcion: " tutorial de como tejer este hermoso monito para llavero. Espero que lo disfrutes  y a tejer !  🧡",
    enlace: "https://youtu.be/ki0vKcn5Q3o?si=u7yOrUDcrSJpK0ys",
  },
  {
    titulo:
      "♡ Tutorial de llavero de un mini ramo a crochet | No cosido necesario ♡",
    descripcion:
      "Aprende a tejer el llavero de un mini ramo a crochet de una forma sencilla y rápida.",
    enlace: "https://youtu.be/d8O58f5UeYc?si=aBYCAPCJj-c5DOx-",
  },
  {
    titulo:
      "🍓🍫CHOCOFRESA coquette🍓🎀❤️ crochet ideas, Fácil para principiantes de ganchillo ❤️",
    descripcion:
      "Un tutorial fácil y rápido de una fresa con chocolate a crochet, ideal para principiantes.",
    enlace: "https://youtu.be/bkHYnnGOtYE?si=OX9HH0UhdHNqd4AM",
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
