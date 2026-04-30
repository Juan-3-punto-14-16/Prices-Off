<?php
use Controllers\APIController;

it('rechaza búsquedas vacías o nulas según el requerimiento PM_9', function () {
    // PASO 1: Enviar a la función de búsqueda un valor nulo
    $_GET['nombre'] = null;

    ob_start();
    APIController::buscar();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('error', 'El campo de búsqueda no puede estar vacío');

    // PASO 2: Enviar a la función una cadena de puros espacios
    $_GET['nombre'] = '     ';
    ob_start();
    APIController::buscar();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('error', 'El campo de búsqueda no puede estar vacío');

    // PASO 3: Enviar a la función una cadena de texto válida
    $_GET['nombre'] = 'Jitomate';
    ob_start();
    APIController::buscar();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('mensaje', 'Búsqueda exitosa');

    // LIMPIEZA FINAL
    $_GET = [];
});
