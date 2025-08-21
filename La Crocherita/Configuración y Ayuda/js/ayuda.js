/*function enviarConsulta() {
            const texto = document.getElementById('consultaTexto').value.trim();
            
            if (texto === '') {
                alert('Por favor, escribe tu consulta antes de enviar.');
                return;
            }
            
            // Limpiar el textarea
            document.getElementById('consultaTexto').value = '';
            
            // Mostrar modal
            const modal = document.getElementById('modalConfirmacion');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
        
        function cerrarModal() {
            const modal = document.getElementById('modalConfirmacion');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
        
        // Cerrar modal al hacer clic en el fondo
        document.getElementById('modalConfirmacion').addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
        */