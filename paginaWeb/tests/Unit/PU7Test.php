<?php
use Model\Ubicacion;

it('rechaza coordenadas fuera de rango y acepta coordenadas válidas según el requerimiento PM_7', function () {
    // PASO 1: Ingresar al componente validador una latitud de 95.5 (fuera de rango)
    $ubicacion = new Ubicacion([
        'latitud' => 95.5,
        'longitud' => 0,
        'direccion' => 'Casa de fulanito'
    ]);
    expect($alertas = $ubicacion->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('Latitud inválida');

    // PASO 2: Ingresar una longitud de -190.0 (fuera de rango)
    $ubicacion = new Ubicacion([
        'latitud' => 0,
        'longitud' => -190.0,
        'direccion' => 'Casa de fulanito'
    ]);
    expect($alertas = $ubicacion->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('Longitud inválida');

    // PASO 3: Ingresar una longitud de 20.6 y longitud de -103.3 (coordenadas válidas)
    $ubicacion = new Ubicacion([
        'latitud' => 20.6,
        'longitud' => -103.3,
        'direccion' => 'Casa de fulanito'
    ]);
    expect($ubicacion->validar())->toBeEmpty();
});
