const calendarEl = document.getElementById('calendar');
const mesSelector = document.getElementById('mes-selector');

let mesActual = parseInt(mesSelector.value); // Mes inicial
let yearActual = new Date().getFullYear();

// Función para generar el calendario
function generarCalendario(mes, year) {
    calendarEl.innerHTML = ''; // Limpiar calendario

    // Primer día del mes
    const primerDia = new Date(year, mes - 1, 1);
    const ultimoDia = new Date(year, mes, 0);
    const totalDias = ultimoDia.getDate();

    // Cuántos días de la semana tiene que dejar vacíos antes del 1
    let startDay = primerDia.getDay(); // 0=Domingo, 1=Lunes,...
    startDay = startDay === 0 ? 6 : startDay - 1; // Ajustamos para que lunes=0

    // Crear celdas vacías
    for (let i = 0; i < startDay; i++) {
        const celda = document.createElement('div');
        celda.classList.add('calendar-cell', 'empty');
        calendarEl.appendChild(celda);
    }

    // Contador de días asistidos
    let diasAsistidos = 0;

    // Crear celdas de los días
    for (let dia = 1; dia <= totalDias; dia++) {
        const celda = document.createElement('div');
        celda.classList.add('calendar-cell');
        celda.textContent = dia;

        // Formatear fecha YYYY-MM-DD para comparar
        const fechaStr = `${year}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;

        // Buscar asistencia para ese día
        const asistenciaDia = asistencias.find(a => a.fecha === fechaStr);

        if (asistenciaDia) {
            if (asistenciaDia.estado === 'asistió') {
                celda.classList.add('asistio'); // Clase CSS verde
                diasAsistidos++;
            } else if (asistenciaDia.estado === 'faltó') {
                celda.classList.add('falto'); // Clase CSS rojo
            }
            // Tooltip con nombre de clase
            celda.title = asistenciaDia.nombre_clase + ' - ' + asistenciaDia.estado;
        }

        calendarEl.appendChild(celda);
    }

    // Actualizar resumen
    document.getElementById('total-dias').textContent = diasAsistidos;
    const precioPlan = parseInt(document.getElementById('precio-plan').textContent) || 0;
    document.getElementById('total-pago').textContent = diasAsistidos * precioPlan;
}

// Evento de cambio de mes
mesSelector.addEventListener('change', () => {
    mesActual = parseInt(mesSelector.value);
    generarCalendario(mesActual, yearActual);
});

// Inicializar calendario
generarCalendario(mesActual, yearActual);
