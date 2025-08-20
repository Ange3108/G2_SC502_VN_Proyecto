document.addEventListener('DOMContentLoaded', function () {

$(document).ready(function () {
  // Abrir modal de editar y rellenar datos
  $(document).on('click', '.btnEditar', function () {
    const id = $(this).data('id');
    const estado = $(this).data('estado');
    // Rellenar los campos del modal
    $('#id_proyecto_modal').val(id);    
    $('#estado_modal').val(estado);
    // Abrir el modal
    const modalEl = document.getElementById('formCambiarEstado');
    if (modalEl) {
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    }
  });

  // Enviar formulario de cambio de estado por AJAX
  $(document).on('submit', 'form#formCambiarEstado', function (e) {
    e.preventDefault();
    const estado = $('#estado_modal').val();
    if (!estado) {
      mostrarMensaje('El estado es requerido', 'danger');
      return;
    }
    const formData = new FormData(this);
    fetch('misProyectos.php', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.text())
      .then(msg => {
        mostrarMensaje(msg.trim(), 'success');
        const modalEl = document.getElementById('formCambiarEstado');
        if (modalEl) {
          const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          modal.hide();
        }
        setTimeout(() => location.reload(), 900);
      })
      .catch(err => mostrarMensaje('Error: ' + err, 'danger'));
  });

  // Eliminar proyecto
  $(document).on('click', '.btn-eliminar', function () {
    const btn = $(this);
    const id = btn.data('id');
    if (!confirm('¿Seguro que quieres eliminar este proyecto?')) return;
    fetch('misProyectos.php?eliminar=' + id, {
      method: 'GET',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
      .then(res => res.text())
      .then(msg => {
        mostrarMensaje(msg.trim(), 'info');
        btn.closest('tr').remove();
      })
      .catch(err => mostrarMensaje('Error: ' + err, 'danger'));
  });

  // Función para mostrar mensajes
  function mostrarMensaje(texto, tipo) {
    const mensaje = document.getElementById('mensaje');
    if (!mensaje) return;
    mensaje.className = `alert alert-${tipo} alert-dismissible fade show`;
    mensaje.innerHTML = `
      ${texto}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    `;
    mensaje.style.display = 'block';
    if (tipo === 'success' || tipo === 'info') {
      setTimeout(() => {
        mensaje.style.display = 'none';
      }, 3500);
    }
  }
  
});
});



