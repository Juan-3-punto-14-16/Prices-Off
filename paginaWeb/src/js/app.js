document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

function iniciarApp() {
    iniciarFormularioProductos();
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