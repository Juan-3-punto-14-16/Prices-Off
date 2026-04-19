document.addEventListener('DOMContentLoaded', () => {
    iniciarApp();
})

let productosActuales = [], marcadoresGrupo = [];
let busquedaActual = '';
let latitud = 20.655262, longitud = -103.325492;
let mapa, marker, mapaBusqueda;
let pinSeleccionadoActual = null, idProductoSeleccionado = null;

function iniciarApp() {
    iniciarRastreoUbicacion();
    iniciarFormularioProductos();
    iniciarBuscadorInicio();
    enviarProductosFetch();
    iniciarMecanismoVotos();
    iniciarAutocompletado();
    iniciarMapas();
}

function iniciarRastreoUbicacion() {
    if ("geolocation" in navigator) {
        // Ejecutamos una primera vez de inmediato
        actualizarCoordenadas();
        // Configura el intervalo para cada 5 minutos (300000 ms)
        setInterval(() => {
            actualizarCoordenadas();
        }, 300000);
    } else {
        console.log("Geolocalización no disponible en este navegador.");
    }
}

function actualizarCoordenadas() {
    // Intenta obtener la ubicación actual del usuario
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            latitud = pos.coords.latitude;
            longitud = pos.coords.longitude;
            console.log(`Ubicación actualizada: ${latitud}, ${longitud}`);

            // Si el mapa y el marcador ya están definidos, los actualizamos a la nueva ubicación
            if (mapa !== undefined && marker) {
                const nuevaPos = [latitud, longitud];
                mapa.setView(nuevaPos, 16);
                marker.setLatLng(nuevaPos);
                // Actualiza la dirección de Google Maps para esa nueva posición
                obtenerDireccion(latitud, longitud);
                // Si el mapa de resultados está activo, también lo actualizamos para que el usuario vea su nueva ubicación
                if (mapa) {
                    mapa.panTo([latitud, longitud]);
                }
            }
        },
        (error) => {
            console.warn("No se pudo actualizar la ubicación, manteniendo última conocida");
        },
        { enableHighAccuracy: false, timeout: 5000, maximumAge: 60000 } // Opciones para mejorar la eficiencia del GPS
    );
}

function iniciarFormularioProductos() {
    const btnAgregar = document.querySelector('#agregar_fila');
    const listaProductos = document.querySelector('#lista_productos');

    if (btnAgregar && listaProductos) {
        btnAgregar.addEventListener('click', () => {
            // Tomamos la primera fila como "molde"
            const filaPlantilla = listaProductos.querySelector('.producto_fila');
            // Clonamos ese molde (el 'true' indica que copie el HTML de adentro)
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

        try {
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
            const direccion = document.querySelector('#direccion_input')?.value || "Dirección no encontrada";
            datos.append('direccion', direccion); // Agrega la dirección obtenida

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
        // Verifica si el usuario ya votó por este producto consultando el localStorage
        const almacenamiento = JSON.parse(localStorage.getItem('votos_pricesoff')) || {};
        const registroVoto = almacenamiento[producto.id];
        const miVoto = registroVoto ? registroVoto.voto : null;

        // Si el usuario ya votó, aplicamos un estilo diferente al botón correspondiente
        const colorLike = miVoto === 'true' ? 'style="color: #2ecc71"' : '';
        const colorDislike = miVoto === 'false' ? 'style="color: #e74c3c"' : '';

        // Calcula la distancia real entre el usuario y el producto usando sus coordenadas
        const distanciaReal = calcularDistancia(latU, lonU, producto.latitud, producto.longitud);

        const card = document.createElement('DIV');
        card.classList.add('card_producto');
        card.dataset.id = producto.id;

        // Si el producto está seleccionado es resaltado
        if (producto.id === idProductoSeleccionado) {
            card.classList.add('tarjeta_seleccionada');
        }

        // Evento de clic en tarjeta
        card.addEventListener('click', (e) => {
            // Si el clic fue en un botón de voto, no hace nada
            if (e.target.closest('.btn_votar')) return;

            // Buscael pin de la tarjeta a la que se hizo clic
            const pinCorrespondiente = marcadoresGrupo.find(m => m.options.id === producto.id);

            if (pinCorrespondiente) {
                // Centra el mapa en el pin y abre su popup
                mapaBusqueda.setView(pinCorrespondiente.getLatLng(), 15);
                pinCorrespondiente.openPopup();
                // La función openPopup disparará el evento para resaltar
                seleccionarProducto(producto.id, pinCorrespondiente);
            }
        });

        card.innerHTML = `
            <div class="card_header">
                <h3>${producto.tienda}</h3>
                <span class="distancia">A ${distanciaReal} km de ti</span>
            </div>

            <p class="direccion">${producto.direccion}</p>
            <p class="fecha">Publicado ${producto.publicado}</p>

            <div class="card_footer">
                <div class="reacciones">
                    <button class="icon_btn btn_votar" data-id="${producto.id}" data-voto="true" ${colorLike}>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 10v12" />
                            <path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2h0a3.13 3.13 0 0 1 3 3.88Z" />
                        </svg>
                        <span class="conteo_votos">${producto.votospositivos}</span>
                    </button>
                    <button class="icon_btn btn_votar" data-id="${producto.id}" data-voto="false" ${colorDislike}>
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
        busquedaActual = textoBuscado; // Guarda el término de búsqueda actual para usarlo después
        const url = `/api/buscar?nombre=${textoBuscado}`;
        const respuesta = await fetch(url);
        const resultado = await respuesta.json();

        if (!resultado.datos) return;
        // Guarda los productos actuales para ordenarlos despues sin tener que hacer otra consulta
        productosActuales = resultado.datos;
        const metodoActual = document.querySelector('#orden').value; // Método de ordenamiento actual
        ordenarResultados(metodoActual);
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

async function registrarVotoFetch(idProducto, votoNuevo, boton) {
    // Obtiene objeto de votos
    const almacenamiento = JSON.parse(localStorage.getItem('votos_pricesoff')) || {};
    const votoPrevio = almacenamiento[idProducto];

    const datos = new FormData();
    datos.append('idregistroproducto', idProducto);

    if (votoPrevio) {
        // Si el usuario presionó el mismo botón de voto que antes, se elimina el voto
        if (votoPrevio.voto === votoNuevo) {
            datos.append('id', votoPrevio.id); // ID del voto a eliminar
            datos.append('voto', '');
        } else {
            // Si el usuario cambia su voto (like por dislike o viceversa), se actualiza el voto
            datos.append('id', votoPrevio.id); // ID del voto a actualizar
            datos.append('voto', votoNuevo);
        }
    } else {
        // Si es el primer voto del usuario para este producto, se crea
        datos.append('voto', votoNuevo);
    }
    console.log("Enviando voto -> ID Producto:", idProducto, "Voto:", votoNuevo, "ID Voto Previo:", votoPrevio ? votoPrevio.id : "N/A");

    try {
        const url = '/api/votos';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });
        const resultado = await respuesta.json();
        if (resultado.mensaje) {
            if (resultado.mensaje === 'eliminado') {
                delete almacenamiento[idProducto]; // Elimina el voto del almacenamiento
                Swal.fire({
                    icon: 'info',
                    title: '¡Voto eliminado!',
                    position: 'top-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 1500
                });
            } else {
                almacenamiento[idProducto] = {
                    // Guarda el ID del voto que regresa la API para después
                    id: resultado.id || votoPrevio.id,
                    voto: votoNuevo
                };
                Swal.fire({
                    icon: 'success',
                    title: `¡Voto ${resultado.mensaje}!`,
                    position: 'top-end',
                    toast: true,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
            localStorage.setItem('votos_pricesoff', JSON.stringify(almacenamiento));
            // Refresca los resultados para mostrar el nuevo conteo de votos
            if (busquedaActual) {
                buscarYMostrarResultados(busquedaActual);
            }
        }

    } catch (error) {
        console.error("Error al registrar el voto:", error);
    }
}

function ordenarResultados(metodo) {
    if (productosActuales.length === 0) return; // Si no hay productos, no hacemos nada

    const copiaProductos = [...productosActuales]; // Creamos una copia para no modificar el arreglo original

    if (metodo === 'precio') {
        copiaProductos.sort((a, b) => Number.parseFloat(a.preciounitario) - Number.parseFloat(b.preciounitario));
    }
    else if (metodo === 'distancia') {
        copiaProductos.sort((a, b) => {
            const distanciaA = calcularDistancia(latitud, longitud, a.latitud, a.longitud);
            const distanciaB = calcularDistancia(latitud, longitud, b.latitud, b.longitud);
            return Number.parseFloat(distanciaA) - Number.parseFloat(distanciaB);
        });
    }
    mostrarTarjetas(copiaProductos, latitud, longitud);
    pintarMarcadores(copiaProductos);

    // Si hay un producto seleccionado se hace scroll hacia la tarjeta
    if (idProductoSeleccionado) {
        const tarjeta = document.querySelector(`.card_producto[data-id="${idProductoSeleccionado}"]`);
        if (tarjeta) {
            tarjeta.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
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

        const realizarBusqueda = async (busqueda) => {
            if (busqueda.length < 2) {
                contenedorSugerencias.innerHTML = '';
                return;
            }

            try {
                const url = `/api/autocompletar?nombre=${busqueda}`;
                const respuesta = await fetch(url);
                const resultado = await respuesta.json();

                if (resultado.datos) {
                    mostrarSugerencias(resultado.datos, contenedorSugerencias, input);
                }
            } catch (error) {
                console.error("Error en autocompletado:", error);
            }
        };

        // Usa debounce y espera 400ms después de dejar de escribir
        const busquedaDebounce = debounce((valor) => realizarBusqueda(valor), 400);

        input.addEventListener('input', async function () {
            const busqueda = input.value.trim();

            // Si borra el texto, limpiamos las sugerencias y no hacemos consulta
            if (busqueda.length === 0) {
                contenedorSugerencias.innerHTML = '';
                return;
            }

            busquedaDebounce(busqueda);
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

async function obtenerDireccion(lat, lng) {
    try {
        const url = `/api/direccion?latitud=${lat}&longitud=${lng}`;
        const respuesta = await fetch(url);
        const datos = await respuesta.json();

        if (datos.direccion && marker) {
            const inputDireccion = document.querySelector('#direccion_input');
            if (inputDireccion) inputDireccion.value = datos.direccion;
            // Mostramos la dirección en un pequeño globo de texto sobre el pin
            marker.bindPopup(`<b>Ubicación seleccionada:</b><br>${datos.direccion}`, {
                minwidth: 250,
                maxwidth: 400,
                className: 'popup_grande'
            }).openPopup();
        }
    } catch (error) {
        console.log("Error obteniendo la dirección:", error);
    }
}

function iniciarMapas() {
    // 1. MAPA PARA LA VISTA DE "AGREGAR PRODUCTO" (agregar.php)
    if (document.querySelector('#mapa')) {
        mapa = L.map('mapa').setView([latitud, longitud], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapa);

        // Pin que el usuario moverá
        marker = L.marker([latitud, longitud], {
            draggable: true
        }).addTo(mapa);

        obtenerDireccion(latitud, longitud); // Obtenemos la dirección inicial al cargar el mapa

        //Detectar cuando el usuario suelta el pin en otro lado -
        marker.on('moveend', function () {
            const posicion = marker.getLatLng();
            console.log("Nueva ubicación -> Lat: " + posicion.lat + ", Lng: " + posicion.lng);
            latitud = posicion.lat;
            longitud = posicion.lng;
            // Llamamos a la API para traducir la nueva ubicación
            obtenerDireccion(latitud, longitud);
        });
    }

    // 2. MAPA PARA LA VISTA DE "INICIO / RESULTADOS" (index.php)
    if (document.querySelector('#mapa_resultados')) {
        mapaBusqueda = L.map('mapa_resultados').setView([latitud, longitud], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapaBusqueda);

        // Cuando tu contenedor del mapa cambia de "display: none" a "flex/block", 
        // Leaflet a veces se "rompe" y se ve gris
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.target.classList.contains('resultados_activos')) {
                    setTimeout(() => {
                        mapaBusqueda.invalidateSize();
                        // Centra el mapa en el usuario al abrir resultados
                        mapaBusqueda.setView([latitud, longitud], 13);
                    }, 100); // recalcule su tamaño
                }
            });
        });

        const contenedorPrincipal = document.querySelector('#contenedor_principal');
        if (contenedorPrincipal) {
            observer.observe(contenedorPrincipal, { attributes: true, attributeFilter: ['class'] });
        }
    }

    // 3. MAPA PARA EL FONDO DE LA PÁGINA DE INICIO 
    if (document.querySelector('#mapa_inicio')) {
        const mapaInicio = L.map('mapa_inicio', {
            zoomControl: false, // Quitamos los botones de + y - para que se vea más limpio
            scrollWheelZoom: false // Evitamos que el usuario haga zoom por accidente al scrollear
        }).setView([latitud, longitud], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(mapaInicio);
    }
}

function pintarMarcadores(productos) {
    // Borrar los marcadores anteriores
    marcadoresGrupo.forEach(m => mapaBusqueda.removeLayer(m));
    marcadoresGrupo = [];
    // Si no hay productos o mapa, no hace nada
    if (!mapaBusqueda || productos.length === 0) return;

    productos.forEach(producto => {
        // Decide que ícono usar
        const iconoAUsar = (producto.id === idProductoSeleccionado) ? obtenerIcono('seleccionado') : obtenerIcono('normal');
        // Crea un marcador para cada producto y lo agrega al mapa
        const pin = L.marker([producto.latitud, producto.longitud], { icon: iconoAUsar, id: producto.id }).addTo(mapaBusqueda);
        // Si es el seleccionado, lo guardamos en la referencia de pin actual
        if (producto.id === idProductoSeleccionado) {
            pinSeleccionadoActual = pin;
        }

        pin.on('click', function () {
            seleccionarProducto(producto.id, pin);
        });
        // Configura InfoWindow con la información del producto
        const contenidoPopup = `
            <div class="info_window">
                <p style="margin:0 0 5px 0;">${producto.tienda}</p>
                <p style="margin:0;"><b>$${producto.preciounitario} / ${producto.unidadmedida}</b></p>
                <hr>
                <p style="font-size: 11px; color: #666; margin:0;">${producto.direccion}</p>
            </div>
        `;
        pin.bindPopup(contenidoPopup); // Asocia el popup al marcador

        marcadoresGrupo.push(pin); // Guarda el marcador en el arreglo para poder eliminarlo después
    });
}

function resaltarTarjeta(idProducto) {
    // Borrar resaltados anteriores
    const tarjetasAnteriores = document.querySelectorAll('.card_producto.tarjeta_seleccionada');
    tarjetasAnteriores.forEach(tarjeta => {
        tarjeta.classList.remove('tarjeta_seleccionada');
    });

    // Si el ID de la tarjeta coincide con el producto del marcador, le aplicamos un estilo diferente
    const tarjeta = document.querySelector(`.card_producto[data-id="${idProducto}"]`);
    if (tarjeta) {
        tarjeta.classList.add('tarjeta_seleccionada');
        tarjeta.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function seleccionarProducto(id, pin) {
    if (pinSeleccionadoActual) {
        pinSeleccionadoActual.setIcon(obtenerIcono('normal')); // Regresa el pin anterior a su estado nromal
    }
    pin.setIcon(obtenerIcono('seleccionado')); // Cambia el pin del producto seleccionado al icono de selección
    pinSeleccionadoActual = pin;
    idProductoSeleccionado = id; // Guarda el ID del producto seleccionado
    resaltarTarjeta(id); // Resalta la tarjeta del producto seleccionado
}

function debounce(fn, delay) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(this, args), delay);
    };
}

function obtenerIcono(tipo) {
    const configBase = {
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    };

    // Icono seleccionado (amarillo)
    if (tipo === 'seleccionado') {
        return L.icon({
            ...configBase,
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png'
        });
    }

    // Icono normal (rojo)
    return L.icon({
        ...configBase,
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png'
    });
}