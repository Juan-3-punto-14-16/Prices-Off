<div class="inicio_busqueda" id="contenedor_principal">

    <div class="contenedor_mapa_fondo"></div>
    <div class="bloque_central">
        <form class="formulario_inicio">
            <div class="input_grupo">
                <input type="text" name="query" placeholder="Jitomate, cebolla..." autocomplete="off">
                <button type="submit" class="btn_buscar_inicio">BUSCAR</button>
            </div>
        </form>
    </div>

    <div class="cuerpo_resultados">
        <aside class="sidebar_productos">
            <div class="ordenamiento">
                <label for="orden">Ordenar por:</label>
                <select id="orden">
                    <option value="precio">Precio</option>
                    <option value="distancia">Distancia</option>
                </select>
            </div>

            <div class="lista_cards" id="contenedor_tarjetas"></div>
        </aside>

        <main class="mapa_contenedor">
            <div class="buscador_flotante_mapa">
                <form class="formulario_inicio">
                    <div class="input_grupo">
                        <input type="text" name="query" id="input_busqueda_flotante" autocomplete="off">
                        <button type="submit" class="btn_buscar_inicio">BUSCAR</button>
                    </div>
                </form>
            </div>
            
            <div id="mapa_resultados" class="mapa_full"></div>
        </main>
    </div>
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
