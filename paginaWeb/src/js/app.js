document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

function iniciarApp() {
    iniciarFormularioProductos();
    iniciarBuscadorInicio();
}

function iniciarFormularioProductos() {
    const btnAgregar = document.querySelector('#agregar_fila');
    const listaProductos = document.querySelector('#lista_productos');

    if(btnAgregar && listaProductos) {
        btnAgregar.addEventListener('click', () => {
            // Tomamos la primera fila como "molde"
            const filaPlantilla = listaProductos.querySelector('.producto_fila');
            // Clonamos ese molde (el 'true' indica que copie todo el HTML de adentro)
            const nuevaFila = filaPlantilla.cloneNode(true);
            // Limpiamos los campos de texto y números de la nueva copia
            const inputs = nuevaFila.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            // Regresamos el select a la opción de "Unidad" (la opción deshabilitada)
            const select = nuevaFila.querySelector('select');
            if(select) select.selectedIndex = 0;
            // Finalmente, inyectamos la nueva fila al final de la lista
            listaProductos.appendChild(nuevaFila);
        });
        listaProductos.addEventListener('click', (e) => {
            // Verificamos si el usuario hizo clic en un elemento que tenga la clase .btn_eliminar (o adentro de él)
            const btnEliminar = e.target.closest('.btn_eliminar');
            if(btnEliminar) {
                const filas = listaProductos.querySelectorAll('.producto_fila');
                // Solo permitimos borrar si hay más de 1 fila (para que no se queden sin formulario)
                if(filas.length > 1) {
                    const filaAEliminar = btnEliminar.closest('.producto_fila');
                    filaAEliminar.remove();
                } else {
                    //SweetAler, lo aprovechamos para una alerta bonita
                    Swal.fire({
                        icon: 'warning',
                        title: '¡Espera!',
                        text: 'Debes agregar al menos un producto a la lista.',
                        confirmButtonColor: '#333'
                    });
                }
            }
        });
    }
}
function iniciarBuscadorInicio() {
    const contenedorPrincipal = document.getElementById('contenedor_principal');
    const formularios = document.querySelectorAll('.formulario_inicio');
    const inputFlotante = document.getElementById('input_busqueda_flotante');

    // Comprobamos que estemos en la página correcta antes de ejecutar
    if(contenedorPrincipal && formularios.length > 0) {
        formularios.forEach(form => {
            form.addEventListener('submit', function(evento) {
                evento.preventDefault(); // Evita recargar
                
                const textoBuscado = this.querySelector('input[name="query"]').value;
                if(textoBuscado.trim() === '') return; // No hace nada si está vacío

                // 1. Cambiamos a la vista de resultados (Aparece barra lateral y mapa)
                contenedorPrincipal.classList.add('resultados_activos');
                if(inputFlotante) inputFlotante.value = textoBuscado;

                // 2. Dibujamos las tarjetas (Próximamente conectadas a BD)
                buscarYMostrarResultados(textoBuscado);
            });
        });
    }
}
