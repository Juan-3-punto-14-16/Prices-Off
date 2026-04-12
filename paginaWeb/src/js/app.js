document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

function iniciarApp() {
    iniciarFormularioProductos();
    iniciarBuscadorInicio();
    enviarProductosFetch();
}

function iniciarFormularioProductos() {
    const btnAgregar = document.querySelector('#agregar_fila');
    const listaProductos = document.querySelector('#lista_productos');

    if (btnAgregar && listaProductos) {
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
            if (select) select.selectedIndex = 0;
            // Finalmente, inyectamos la nueva fila al final de la lista
            listaProductos.appendChild(nuevaFila);
        });
        listaProductos.addEventListener('click', (e) => {
            // Verificamos si el usuario hizo clic en un elemento que tenga la clase .btn_eliminar (o adentro de él)
            const btnEliminar = e.target.closest('.btn_eliminar');
            if (btnEliminar) {
                const filas = listaProductos.querySelectorAll('.producto_fila');
                // Solo permitimos borrar si hay más de 1 fila (para que no se queden sin formulario)
                if (filas.length > 1) {
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
    if (contenedorPrincipal && formularios.length > 0) {
        formularios.forEach(form => {
            form.addEventListener('submit', function (evento) {
                evento.preventDefault(); // Evita recargar

                const textoBuscado = this.querySelector('input[name="query"]').value;
                if (textoBuscado.trim() === '') return; // No hace nada si está vacío

                // 1. Cambiamos a la vista de resultados (Aparece barra lateral y mapa)
                contenedorPrincipal.classList.add('resultados_activos');
                if (inputFlotante) inputFlotante.value = textoBuscado;

                // 2. Dibujamos las tarjetas (Próximamente conectadas a BD)
                buscarYMostrarResultados(textoBuscado);
            });
        });
    }
}

async function enviarProductosFetch() {
    const formulario = document.querySelector('#formulario_productos');
    if (!formulario) return; // Si no existe el formulario, no hacemos nada

    formulario.addEventListener('submit', async function (evento) {
        evento.preventDefault(); // Evita recargar

        // CAMBIAR: latitud y longitud de prueba 20.655262648774382, -103.32549261971924
        const latitud = 20.655262;
        const longitud = -103.325492;

        let direccionObtenida = "Dirección no encontrada";

        try {
            const urlDireccion = `/api/direccion?latitud=${latitud}&longitud=${longitud}`;
            const respuestaDireccion = await fetch(urlDireccion);
            const datosDireccion = await respuestaDireccion.json();

            if (datosDireccion.direccion) {
                direccionObtenida = datosDireccion.direccion;
            }

            const filas = document.querySelectorAll('.producto_fila');
            const productos = [];

            // Recorre las filas para armar el arreglo de productos
            filas.forEach(fila => {
                productos.push({
                    nombre: fila.querySelector('[name="nombre[]"]').value,
                    precio: fila.querySelector('[name="precio[]"]').value,
                    cantidad: fila.querySelector('[name="cantidad[]"]').value,
                    unidadmedida: fila.querySelector('[name="unidad[]"]').value
                });
            });

            // Datos para APIController::guardar()
            const datos = new FormData();
            datos.append('tienda', document.querySelector('[name="establecimiento"]').value); // Agrega el nombre del establecimiento
            datos.append('productos', JSON.stringify(productos)); // Agrega el arreglo de productos como JSON
            datos.append('latitud', latitud);
            datos.append('longitud', longitud);
            datos.append('direccion', direccionObtenida); // Agrega la dirección obtenida de la API

            const url = '/api/guardar';
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            if (resultado.mensaje) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Registrado!',
                    text: 'Se ha registrado correctamente',
                    confirmButtonText: 'OK'
                });
                formulario.reset(); // Limpia el formulario después de enviar
            }
        }
        catch (error) {
            console.error("Error en el proceso:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al enviar los datos'
            });
        }
    })
}

function mostrarTarjetas(productos, latU, lonU) {
    const contenedor = document.querySelector('#contenedor_tarjetas');

    contenedor.innerHTML = ''; // Limpiamos el contenedor antes de mostrar nuevos resultados

    if (productos.length === 0) {
        contenedor.innerHTML = '<p class="alerta info">No se encontraron productos que coincidan con la búsqueda.</p>';
        return;
    }

    productos.forEach(producto => {
        // Calcula la distancia real entre el usuario y el producto usando sus coordenadas
        const distanciaReal = calcularDistancia(latU, lonU, producto.latitud, producto.longitud);

        const card = document.createElement('DIV');
        card.classList.add('card_producto');
        card.innerHTML = `
            <div class="card_header">
                <h3>${producto.tienda}</h3>
                <span class="distancia">A ${distanciaReal} km de ti</span>
            </div>

            <p class="direccion">${producto.direccion}</p>
            <p class="fecha">Publicado ${producto.publicado}</p>

            <div class="card_footer">
                <div class="reacciones">
                    <div class="icon_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 10v12" /><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z" />
                        </svg>
                        <span>${producto.votospositivos}</span>
                    </div>
                    <div class="icon_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 14V2" /><path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H20a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2.76a2 2 0 0 0-1.79 1.11L12 22h0a3.13 3.13 0 0 1-3-3.88Z" />
                        </svg>
                        <span>${producto.votosnegativos}</span>
                    </div>
                </div>

                <div class="precio_destacado">
                    $${producto.preciounitario}/${producto.unidadmedida}
                </div>
            </div>
        `;

        contenedor.appendChild(card);
    });
}

async function buscarYMostrarResultados(textoBuscado) {
    try {
        const url = `/api/buscar?nombre=${textoBuscado}`;
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();

        if (!resultado.datos) return;

        // Intenta obtener la ubicación del usuario, si el navegador lo permite y el usuario lo autoriza
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    // Si acepta, calculamos con su posición real
                    mostrarTarjetas(resultado.datos, pos.coords.latitude, pos.coords.longitude);
                },
                (error) => {
                    // Si rechaza, usamos coordenadas por defecto
                    mostrarTarjetas(resultado.datos, 20.6552, -103.3254);
                }
            );
        }
        else {
            // Si el navegador no soporta geolocalización, usamos coordenadas por defecto
            mostrarTarjetas(resultado.datos, 20.6552, -103.3254);
        }
    }
    catch (error) {
        console.error("Error al obtener los productos:", error);
    }
}

function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLon / 2) * Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return (R * c).toFixed(1); // Retorna la distancia con un decimal
}
