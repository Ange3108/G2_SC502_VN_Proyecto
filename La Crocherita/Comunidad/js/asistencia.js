document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    const mesSelector = document.getElementById('mes-selector');

    /**
     * Carga la información del plan en el panel de resumen.
     */
    function cargarInfoPlan() {
        if (plan_info && plan_info.nombre_plan) {
            document.getElementById('plan-nombre').textContent = plan_info.nombre_plan;
            const precio = parseFloat(plan_info.precio_base);
            document.getElementById('precio-plan').textContent = precio.toLocaleString('es-CR');
        }
    }

    /**
     * Genera y muestra el calendario para un mes y año específicos.
     */
    function generarCalendario(mes, year) {
        calendarEl.innerHTML = ''; // Limpiar calendario

        const primerDia = new Date(year, mes - 1, 1);
        const ultimoDia = new Date(year, mes, 0);
        const totalDias = ultimoDia.getDate();

        let startDay = primerDia.getDay();
        startDay = startDay === 0 ? 6 : startDay - 1;

        for (let i = 0; i < startDay; i++) {
            const celda = document.createElement('div');
            celda.classList.add('calendar-cell', 'empty');
            calendarEl.appendChild(celda);
        }

        let diasAsistidos = 0;

        // Bucle para crear y AÑADIR cada día del mes
        for (let dia = 1; dia <= totalDias; dia++) {
            const celda = document.createElement('div');
            celda.classList.add('calendar-cell');
            celda.textContent = dia;

            const fechaStr = `${year}-${String(mes).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
            const asistenciaDia = asistencias.find(a => a.fecha === fechaStr);

            if (asistenciaDia) {
                if (asistenciaDia.estado === 'asistió') {
                    celda.classList.add('asistio');
                    diasAsistidos++;
                } else if (asistenciaDia.estado === 'faltó') {
                    celda.classList.add('falto');
                }
                celda.title = `${asistenciaDia.nombre_clase} - ${asistenciaDia.estado}`;
            }
            
            // ¡ESTA ES LA LÍNEA QUE FALTABA!
            calendarEl.appendChild(celda);
        }

        // --- SECCIÓN DE CÁLCULO ---
        document.getElementById('total-dias').textContent = diasAsistidos;
        
        const precioPorClase = parseFloat(plan_info.precio_base) || 0;
        const totalPagar = diasAsistidos * precioPorClase;
        
        document.getElementById('total-pago').textContent = totalPagar.toLocaleString('es-CR');
    }

    // --- INICIALIZACIÓN DE LA PÁGINA ---
    cargarInfoPlan();

    mesSelector.addEventListener('change', () => {
        const mesActual = parseInt(mesSelector.value);
        const yearActual = new Date().getFullYear();
        generarCalendario(mesActual, yearActual);
    });

    mesSelector.dispatchEvent(new Event('change'));
});
