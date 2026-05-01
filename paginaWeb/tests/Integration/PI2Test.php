<?php
use Controllers\APIController;

it('verifica que todos los productos devueltos contengan los campos necesarios según el PI_2', function () {
    $_GET = [
        'nombre' => 'Leche'
    ];

    ob_start();
    APIController::buscar();
    $json = ob_get_clean();

    // Extraemos la respuesta JSON en un arreglo asociativo
    $resultado = json_decode($json, true);

    expect($resultado)->toHaveKey('datos')->and($resultado['datos'])->toBeArray();
    expect($resultado['datos'])->each->toHaveKeys(['preciounitario','latitud', 'longitud']);

    $_GET = [];
}); 
