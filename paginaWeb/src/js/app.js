document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

let productosActuales = [];
// CAMBIAR: latitud y longitud de prueba 20.655262648774382, -103.32549261971924
let latitud = 20.655262;
let longitud = -103.325492;

function iniciarApp() {
    iniciarFormularioProductos();
    iniciarBuscadorInicio();
    enviarProductosFetch();
    iniciarMecanismoVotos();
    iniciarAutocompletado();
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

    // Ordenar resultados según el criterio seleccionado en el select
    const seleccionarOrden = document.querySelector('#orden');
    if (seleccionarOrden) {
        seleccionarOrden.addEventListener('change', function () {
            const metodo = seleccionarOrden.value;
            ordenarResultados(metodo);
        });
    }
}

async function enviarProductosFetch() {
    const formulario = document.querySelector('#formulario_productos');
    if (!formulario) return; // Si no existe el formulario, no hacemos nada

    formulario.addEventListener('submit', async function (evento) {
        evento.preventDefault(); // Evita recargar

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
        // Verifica si el usuario ya votó por este producto consultando el localStorage
        const votosRealizados = JSON.parse(localStorage.getItem('votos_pricesoff')) || [];
        const yaVoto = votosRealizados.includes(producto.id.toString());

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
                    <button class="icon_btn btn_votar" data-id="${producto.id}" data-voto="true" ${yaVoto ? 'disabled style="color: #2ecc71"' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 10v12" />
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z" />
                        </svg>
                        <span class="conteo_votos">${producto.votospositivos}</span>
                    </button>
                    <button class="icon_btn btn_votar" data-id="${producto.id}" data-voto="false" ${yaVoto ? 'disabled style="color: #2ecc71"' : ''}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 14V2" />
                            <path d="M9 18.12 10 14H4.17a2 2 0 0 1-1.92-2.56l2.33-8A2 2 0 0 1 6.5 2H20a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-2.76a2 2 0 0 0-1.79 1.11L12 22h0a3.13 3.13 0 0 1-3-3.88Z" />
                        </svg>
                        <span class="conteo_votos">${producto.votosnegativos}</span>
                    </button>
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
        // Guarda los productos actuales para ordenarlos despues sin tener que hacer otra consulta
        productosActuales = resultado.datos;
        const metodoActual = document.querySelector('#orden').value; // Método de ordenamiento actual

        // Intenta obtener la ubicación del usuario, si el navegador lo permite y el usuario lo autoriza
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    latitud = pos.coords.latitude;
                    longitud = pos.coords.longitude;
                    ordenarResultados(metodoActual); // Reordena resultados                   
                },
                (error) => {
                    // Si rechaza, usamos coordenadas por defecto
                    Swal.fire({
                        icon: 'info',
                        title: '¡Permisos de ubicación denegados!',
                        text: 'Se usarán coordenadas por defecto.',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        toast: true
                    });
                    ordenarResultados(metodoActual);
                }
            );
        }
        else {
            // Si el navegador no soporta geolocalización, usamos coordenadas por defecto
            Swal.fire({
                icon: 'info',
                title: '¡Ubicación no disponible!',
                text: 'Se usarán coordenadas por defecto.',
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                toast: true
            });
            ordenarResultados(metodoActual);
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

function iniciarMecanismoVotos() {
    const contenedor = document.querySelector('#contenedor_tarjetas');

    if (contenedor) {
        contenedor.addEventListener('click', async (e) => {
            const boton = e.target.closest('.btn_votar');
            if (boton) {
                const idProducto = boton.dataset.id;
                const voto = boton.dataset.voto;

                await registrarVotoFetch(idProducto, voto, boton);
            }
        });
    }
}

async function registrarVotoFetch(idProducto, voto, boton) {
    const votosRealizados = JSON.parse(localStorage.getItem('votos_pricesoff')) || [];

    if (votosRealizados.includes(idProducto.toString())) {
        Swal.fire({
            icon: 'info',
            title: '¡Ya votaste!',
            text: 'Solo puedes votar una vez por producto.',
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            toast: true
        });
        return;
    }

    const datos = new FormData();
    datos.append('idregistroproducto', idProducto);
    datos.append('voto', voto);

    try {
        const url = '/api/votos';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
        const resultado = await respuesta.json();

        if (resultado.datos) {
            // Guarda en localStorage el ID del producto ya votado
            votosRealizados.push(idProducto.toString());
            localStorage.setItem('votos_pricesoff', JSON.stringify(votosRealizados));

            // Actualiza la interfaz
            const conteoSpan = boton.querySelector('.conteo_votos');
            conteoSpan.textContent = parseInt(conteoSpan.textContent) + 1;

            boton.style.color = (voto === "true") ? "#2ecc71" : "#e74c3c";
            boton.disabled = true;

            Swal.fire({
                icon: 'success',
                title: '¡Voto registrado!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                toast: true
            });
        }
    }
    catch (error) {
        console.error("Error al registrar el voto:", error);
    }
}

function ordenarResultados(metodo) {
    if (productosActuales.length === 0) return; // Si no hay productos, no hacemos nada

    const copiaProductos = [...productosActuales]; // Creamos una copia para no modificar el arreglo original

    if (metodo === 'precio') {
        copiaProductos.sort((a, b) => parseFloat(a.preciounitario) - parseFloat(b.preciounitario));
    }
    else if (metodo === 'distancia') {
        copiaProductos.sort((a, b) => {
            const distanciaA = calcularDistancia(latitud, longitud, a.latitud, a.longitud);
            const distanciaB = calcularDistancia(latitud, longitud, b.latitud, b.longitud);
            return parseFloat(distanciaA) - parseFloat(distanciaB);
        });
    }
    mostrarTarjetas(copiaProductos, latitud, longitud);
}

function iniciarAutocompletado() {
    const inputsBusqueda = document.querySelectorAll('input[name="query"]');

    inputsBusqueda.forEach(input => {
        // Contenedor para las sugerencias
        let contenedorSugerencias = input.parentElement.querySelector('.sugerencias_lista');

        if (!contenedorSugerencias) {
            contenedorSugerencias = document.createElement('UL');
            contenedorSugerencias.classList.add('sugerencias_lista');
            input.parentElement.appendChild(contenedorSugerencias);
        }

        input.addEventListener('input', async function () {
            // Si el texto es muy corto, no hace la consulta
            const busqueda = input.value.trim();

            if (busqueda.length < 2) {
                contenedorSugerencias.innerHTML = '';
                return;
            }

            // Hace la consulta a la API de autocompletado
            try {
                const url = `/api/autocompletar?nombre=${busqueda}`;
                const respuesta = await fetch(url);
                const resultado = await respuesta.json();

                if (resultado.datos) {
                    mostrarSugerencias(resultado.datos, contenedorSugerencias, input);
                }
            }
            catch (error) {
                console.error("Error en autocompletado:", error);
            }
        });
        // Cerrar sugerencias al hacer clic fuera del input
        document.addEventListener('click', function (e) {
            if (e.target !== input) {
                contenedorSugerencias.innerHTML = '';
            }
        });
    });
}

function mostrarSugerencias(sugerencias, contenedor, input) {
    contenedor.innerHTML = ''; // Limpia sugerencias anteriores

    // Para cada sugerencia, crea un elemento de lista y lo agrega al contenedor
    sugerencias.forEach(sugerencia => {
        const li = document.createElement('LI');
        li.textContent = sugerencia.nombre;
        li.classList.add('sugerencia_nombre');

        li.addEventListener('click', function () {
            input.value = sugerencia.nombre; // Completa el input con la sugerencia
            contenedor.innerHTML = '';

            const form = input.closest('form');
            if (form) {
                form.dispatchEvent(new Event('submit')); // Envía el formulario para mostrar resultados
            } else {
                // Si no hay formulario, puedes llamar a la función de búsqueda directamente
                buscarYMostrarResultados(sugerencia.nombre);
            }
        });
        contenedor.appendChild(li);
    });
}
