<?php
use Model\Ubicacion;

it('rechaza coordenadas fuera de rango y acepta coordenadas válidas según el requerimiento PM_7', function () {
    // PASO 1: Ingresar al componente validador una latitud de 95.5 (fuera de rango)
    $ubicacion1 = new Ubicacion([
        'latitud' => 95.5,
        'longitud' => 0,
        'direccion' => 'Casa de fulanito'
    ]);
    $alertas = $ubicacion1->validar();
    expect($alertas)->toHaveKey('error');

    // PASO 2: Ingresar una longitud de -190.0 (fuera de rango)
    $ubicacion2 = new Ubicacion([
        'latitud' => 0,
        'longitud' => -190.0,
        'direccion' => 'Casa de fulanito'
    ]);
    $alertas = $ubicacion2->validar();
    expect($alertas)->toHaveKey('error');

    // PASO 3: Ingresar una longitud de 20.6 y longitud de -103.3 (coordenadas válidas)
    $ubicacion3 = new Ubicacion([
        'latitud' => 20.6,
        'longitud' => -103.3,
        'direccion' => 'Casa de fulanito'
    ]);
    $alertas = $ubicacion3->validar();
    expect($alertas)->toBeEmpty();
});
