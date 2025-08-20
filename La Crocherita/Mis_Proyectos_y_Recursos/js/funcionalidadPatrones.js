document.addEventListener('DOMContentLoaded', function () {
    
    const form = document.getElementById('formEditarPatron');
    const mensaje = document.getElementById('mensaje');


    // Validar formulario y actualizar patrón
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Validaciones del lado del cliente
        if (!validarFormulario()) {
            return;
        }

        const datos = new FormData(form);

        // Enviar datos con AJAX (incluyendo archivos)
        $.ajax({
            url: 'patrones.php',
            type: 'POST',
            data: datos,
            processData: false,
            contentType: false,
            success: function (respuesta) {
                mostrarMensaje(respuesta, 'success');
                form.reset();
                // Cerrar el modal automáticamente
                setTimeout(function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarPatron'));
                    if(modal) modal.hide();
                    // Recargar solo la lista de patrones por AJAX
                    cargarPatrones();
                }, 1000);
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error al editar el patrón: ' + error, 'danger');
            }
        });
    });

    // Eliminar reservación
    $(document).on('click', '.btnEliminar', function (e) {
        e.preventDefault();
        if (!confirm('¿Está seguro de eliminar este patrón?')) return;

        const id = $(this).data('id_patron');

        $.get(`patrones.php?eliminar=${id}`, function(respuesta) {
            mostrarMensaje(respuesta, 'info');
    
        }).fail(function(xhr, status, error) {
            mostrarMensaje('Error al eliminar el patrón: ' + error, 'danger');
        });
    });

    // Editar reservación
    $(document).on('click', '.btnEditar', function (e) {
        e.preventDefault();
        
        const id = $(this).data('id_patron');
        const nombre = $(this).data('nombre_patron');
        const descripcion = $(this).data('descripcion');
        const nivel = $(this).data('nivel_dificultad');
        const puntos = $(this).data('puntos_utilizados');
        const materiales = $(this).data('materiales');


        // Llenar el formulario con los datos existentes
        $('#nombre_patron_editar').val(nombre);
        $('#descripcion_editar').val(descripcion);
        $('#nivel_dificultad_editar').val(nivel);
        $('#puntos_utilizados_editar').val(puntos);
        $('#materiales_editar').val(materiales);
        $('#id_patron_editar').val(id);

        // Cambiar el texto del botón
        $('#btnEditar').text('Actualizar Patrón');

        // Scroll al formulario
        $('html, body').animate({
            scrollTop: $('#formEditarPatron').offset().top
        }, 500);
    });

    // Función para validar el formulario de edición
    function validarFormulario() {
        const nombre = document.getElementById('nombre_patron_editar').value.trim();
        const descripcion = document.getElementById('descripcion_editar').value.trim();
        const puntos = document.getElementById('puntos_utilizados_editar').value.trim();
        const nivel = document.getElementById('nivel_dificultad_editar').value;
        const materiales = document.getElementById('materiales_editar').value.trim();

        // Validar nombre
        if (nombre === '') {
            mostrarMensaje('El nombre del patrón es requerido', 'danger');
            return false;
        }
        if (nombre.length < 4) {
            mostrarMensaje('El nombre debe tener al menos 4 caracteres', 'danger');
            return false;
        }
        //validar descripcion
        if (descripcion === '') {
            mostrarMensaje('La descripción del patrón es requerida', 'danger');
            return false;
        }
        //validar puntos utilizados
        if (puntos === '') {
            mostrarMensaje('Los puntos utilizados son requeridos', 'danger');
            return false;
        }
        //validar nivel de dificultad
        if (nivel === '') {
            mostrarMensaje('El nivel de dificultad es requerido', 'danger');
            return false;
        }
        //validar materiales
        if (materiales === '') {
            mostrarMensaje('Los materiales son requeridos', 'danger');
            return false;
        }
        return true;
    }
    // Función para recargar la lista de patrones por AJAX
    function cargarPatrones() {
        // Ruta absoluta relativa al proyecto para evitar errores de ruta
        var rutaAjax = '/G2_SC502_VN_Proyecto/La Crocherita/Mis_Proyectos_y_Recursos/partes/patrones.php?ajax=1';
        $.get(rutaAjax, function(data) {
            // Reemplaza el contenido del contenedor de tarjetas de patrones
            $('.container.py-5').html($(data).find('.container.py-5').html());
        });
    }

    // Función para mostrar mensajes
    function mostrarMensaje(texto, tipo) {
        mensaje.className = `alert alert-${tipo} alert-dismissible fade show`;
        mensaje.innerHTML = `
            ${texto}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        `;
        mensaje.style.display = 'block';

        // Auto-ocultar después de 5 segundos si es un mensaje de éxito
        if (tipo === 'success') {
            setTimeout(() => {
                mensaje.style.display = 'none';
            }, 5000);
        }
    }

    $(document).on('click', '.btnFavoritos', function () {
        var $icon = $(this).find('i.fa-heart');
        var esFavorito = $icon.hasClass('text-danger');
        var id_patron = $(this).data('id');
        if (!esFavorito) {
            $icon.removeClass('text-secondary').addClass('text-danger');
        }
        // (Opcional: Si quieres permitir quitar de favoritos, aquí puedes alternar el color y hacer otra petición AJAX)
    });
    // Funcionalidad para el botón de favoritos
    $(document).on('click', '.btnFavoritos', function (e) {
        e.preventDefault();
        const id_patron = $(this).data('id');
        // Puedes agregar animación o cambio de color aquí si quieres
        $.ajax({
            url: 'patrones.php',
            type: 'POST',
            data: { id_patron: id_patron },
            success: function(respuesta) {
                mostrarMensaje(respuesta, 'success');
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error al agregar a favoritos: ' + error, 'danger');
            }
        });
    });



});
   