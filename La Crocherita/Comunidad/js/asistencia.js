document.addEventListener("DOMContentLoaded", function () {
  const calendar = document.getElementById("calendar");
  const totalDias = document.getElementById("total-dias");
  const totalPago = document.getElementById("total-pago");
  const selectorMes = document.getElementById("mes-selector");
  const planNombre = document.getElementById("plan-nombre");
  const precioPlan = document.getElementById("precio-plan");

  const asistenciaPorMes = {
    6: [2, 5, 8, 10, 15, 22, 23, 30], // Julio
    7: [1, 8, 9, 15, 22, 29],         // Agosto
    8: [3, 6, 10, 11, 17, 24]         // Septiembre
  };

  const precios = {
    plan1: 15000,
    plan2: 25000,
    extra: 7000 // Por cada día adicional fuera del plan
  };

  function obtenerSemanaDelMes(dia, mesIndex, anio = 2025) {
    const fecha = new Date(anio, mesIndex, dia);
    const primerDia = new Date(anio, mesIndex, 1);
    const diaSemanaPrimero = (primerDia.getDay() + 6) % 7; // Lunes como inicio
    return Math.floor((dia + diaSemanaPrimero - 1) / 7) + 1;
  }

  function calcularPago(asistencias, mesIndex) {
    const semanas = {};

    asistencias.forEach((dia) => {
      const semana = obtenerSemanaDelMes(dia, mesIndex);
      semanas[semana] = semanas[semana] ? semanas[semana] + 1 : 1;
    });

    let sumaSemanas = 0;
    let totalSemanas = 0;

    for (const cantidad of Object.values(semanas)) {
      sumaSemanas += cantidad >= 2 ? 2 : 1;
      totalSemanas++;
    }

    const promedio = totalSemanas > 0 ? sumaSemanas / totalSemanas : 0;
    const diasTotales = asistencias.length;

    let plan, base, limite;

    if (promedio >= 2) {
      plan = "2 veces a la semana";
      base = precios.plan2;
      limite = 8;
    } else {
      plan = "1 vez a la semana";
      base = precios.plan1;
      limite = 4;
    }

    const diasExtra = diasTotales > limite ? diasTotales - limite : 0;
    const total = base + (diasExtra * precios.extra);

    // Mostrar info
    planNombre.textContent = plan;
    precioPlan.textContent = base;
    totalDias.textContent = diasTotales;
    totalPago.textContent = total;
  }

  function crearCalendario(mesIndex) {
    calendar.innerHTML = "";

    const anio = 2025;
    const diasEnMes = new Date(anio, mesIndex + 1, 0).getDate();
    const primerDiaSemana = (new Date(anio, mesIndex, 1).getDay() + 6) % 7; // Lunes como inicio
    const asistencia = asistenciaPorMes[mesIndex] || [];

    for (let i = 0; i < primerDiaSemana; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.className = "calendar-day";
      emptyDiv.style.visibility = "hidden";
      calendar.appendChild(emptyDiv);
    }

    for (let i = 1; i <= diasEnMes; i++) {
      const diaDiv = document.createElement("div");
      diaDiv.className = "calendar-day";
      diaDiv.innerHTML = `<strong>${i}</strong>`;

      if (asistencia.includes(i)) {
        diaDiv.classList.add("asistido");
      }

      calendar.appendChild(diaDiv);
    }

    calcularPago(asistencia, mesIndex);
  }

  crearCalendario(parseInt(selectorMes.value));

  selectorMes.addEventListener("change", function () {
    crearCalendario(parseInt(this.value));
  });
});
