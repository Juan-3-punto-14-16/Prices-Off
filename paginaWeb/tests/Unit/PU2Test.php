<?php
use Model\Voto;

it('rechaza valores numéricos o cadenas inválidas y acepta solo estados oficiales según el requerimiento PM_2', function () {
    // Paso 1: Instanciar el modelo de votos
    $voto = new Voto([
        'idregistroproducto' => 1,
    ]);

    // Paso 2: Ingresar valores numéricos (2, -1, 100)
    $voto->voto = 2;
    expect($voto->validar())->toHaveKey('error');

    $voto->voto = -1;
    expect($voto->validar())->toHaveKey('error');

    $voto->voto = 100;
    expect($voto->validar())->toHaveKey('error');

    // Paso 3: Ingresar cadenas de texto no permitidas ("null", "voto")
    $voto->voto = "null";
    expect($voto->validar())->toHaveKey('error');

    $voto->voto = "voto";
    expect($voto->validar())->toHaveKey('error');

    // Paso 4: Ingresar un valor válido (true y false)
    $voto->voto = "true";
    expect($voto->validar())->toBeEmpty();

    $voto->voto = "false";
    expect($voto->validar())->toBeEmpty();

    // Paso 5: Ingresar un valor de retiro válido (string vacío)
    $voto->voto = "";
    expect($voto->validar())->toBeEmpty();
});
