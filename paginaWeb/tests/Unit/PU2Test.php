<?php
use Model\Voto;

it('rechaza valores numéricos o cadenas inválidas y acepta solo estados oficiales según el requerimiento PM_2', function () {
    // Paso 1: Instanciar el modelo de votos (con idregistroproducto para pasar el resto de validaciones)
    $voto = new Voto(['idregistroproducto' => 1]);

    // Paso 2: Ingresar valores numéricos (2, -1, 100)
    $voto->voto = 2;
    expect($alertas = $voto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El valor del voto es inválido');

    $voto->voto = -1;
    expect($alertas = $voto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El valor del voto es inválido');

    $voto->voto = 100;
    expect($alertas = $voto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El valor del voto es inválido');

    // Paso 3: Ingresar cadenas de texto no permitidas ("null", "voto")
    $voto->voto = "null";
    expect($alertas = $voto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El valor del voto es inválido');

    $voto->voto = "voto";
    expect($alertas = $voto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El valor del voto es inválido');

    // Paso 4: Ingresar un valor válido (true y false)
    $voto->voto = "true";
    expect($voto->validar())->toBeEmpty();

    $voto->voto = "false";
    expect($voto->validar())->toBeEmpty();

    // Paso 5: Ingresar un valor de retiro válido (string vacío)
    $voto->voto = "";
    expect($voto->validar())->toBeEmpty();
});
