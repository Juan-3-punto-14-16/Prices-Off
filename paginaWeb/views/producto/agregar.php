<div class="contenedor_agregar">
    
    <form class="formulario_productos" id="formulario_productos" action="/api/guardar" method="POST">
        <div class="centrar_contenido">
            <button class="btn_escanear" type="button">Escanear Ticket</button>
            <p class="instruccion_manual">Si no, ingresa los datos del producto manualmente</p>
        </div>

        <div class="caja_formulario">
            
            <div class="producto_encabezados">
                <div class="etiqueta_pill">Nombre</div>
                <div class="etiqueta_pill">Precio</div>
                <div class="etiqueta_pill">Cantidad</div>
                <div class="etiqueta_pill">Unidad de Medida</div>
                <div class="espacio_vacio"></div> 
            </div>

            <div class="lista_productos" id="lista_productos">
                <div class="producto_fila">
                    <input type="text" placeholder="Ej. Jitomate" name="nombre[]" required>
                    <input type="number" placeholder="30" name="precio[]" step="0.01" required>
                    <input type="number" placeholder="2" name="cantidad[]" required>
                    
                    <select name="unidad[]" required>
                        <option value="" disabled selected hidden>Unidad</option>
                        <option value="kg">Kg</option>
                        <option value="litro">Litro</option>
                        <option value="pieza">Pieza</option>
                    </select>
                    
                    <button type="button" class="btn_eliminar">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16zm-9.489 5.14a1 1 0 0 0 -1.218 1.567l1.292 1.293l-1.292 1.293l-.083 .094a1 1 0 0 0 1.497 1.32l1.293 -1.292l1.293 1.292l.094 .083a1 1 0 0 0 1.32 -1.497l-1.292 -1.293l1.292 -1.293l.083 -.094a1 1 0 0 0 -1.497 -1.32l-1.293 1.292l-1.293 -1.292l-.094 -.083z" />
                            <path d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="button" class="btn_agregar_otro" id="agregar_fila">+ Agregar otro producto</button>

            <div class="campo_establecimiento">
                <input type="text" placeholder="Nombre del establecimiento (opcional)" name="establecimiento">
            </div>
        </div>

        <div class="alinear_derecha">
            <button class="btn_enviar" type="submit">ENVIAR</button>
        </div>
    </form> <section class="contenedor_mapa">
        <div class="mapa_placeholder">
            <div class="mapa_etiqueta">Selecciona la ubicación en el mapa</div>
            <div id="mapa" class="mapa_visual"></div>
        </div>
    </section>

</div>

<?php
    $estilos = "<link rel='stylesheet' href='https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
                integrity='sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=' crossorigin='' />";

    $script = "
    <script src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        integrity='sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=' crossorigin=''></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script src='build/js/app.js'></script>
    ";
