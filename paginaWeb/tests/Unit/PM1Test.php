<?php
use Model\RegistroProducto;

it('rechaza precios menores a cero según el requerimiento PM_1', function () {
    // PASO 1: Instanciar el modelo de registro de productos
    $producto = new RegistroProducto([
        'cantidad' => 1,
        'unidadmedida' => 'pieza'
    ]);

    // PASO 2: Enviar al componente a evaluar un precio de -50.00
    $producto->precio = -50.00;
    $alertas = $producto->validar();
    expect($alertas)->toHaveKey('error');

    // PASO 3: Enviar al componente un precio de 0.
    $producto->precio = 0;
    $alertas = $producto->validar();
    expect($alertas)->toBeEmpty();

    // PASO 4: Enviar a evaluar un precio de 25.50.
    $producto->precio = 25.50;
    $alertas = $producto->validar();
    expect($alertas)->toBeEmpty();
});
