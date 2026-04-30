<?php
use Model\RegistroProducto;

it('rechaza precios menores a cero según el requerimiento PM_1', function () {
    // PASO 1: Instanciar el modelo de registro de productos (con requisitos para pasar el resto de validaciones)
    $producto = new RegistroProducto([
        'cantidad' => 1,
        'unidadmedida' => 'pieza'
    ]);

    // PASO 2: Enviar al componente a evaluar un precio de -50.00
    $producto->precio = -50.00;
    expect($alertas = $producto->validar())->toHaveKey('error')
        ->and($alertas['error'][0])->toBe('El precio debe ser un número válido');

    // PASO 3: Enviar al componente un precio de 0.
    $producto->precio = 0;
    expect($producto->validar())->toBeEmpty();

    // PASO 4: Enviar a evaluar un precio de 25.50.
    $producto->precio = 25.50;
    expect($producto->validar())->toBeEmpty();
});
