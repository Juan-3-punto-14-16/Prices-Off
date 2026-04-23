<?php
use Model\RegistroProducto;

it('rechaza precios menores a cero según el requerimiento PM_1', function () {
    
    // Paso 1: Instanciar el modelo de registro de productos
    $producto= new RegistroProducto();

    // Paso 2: Enviar al componente a evaluar un precio de -50.00
    $producto->precio = -50.00;

    // Llenamos lo demás válido para aislar el error
    $producto->cantidad = 1; 
    $producto->unidadmedida = 'pieza';
    
    // Esperamos que el arreglo devuelto tenga la llave 'error'
    $alertas = $producto->validar();
    expect($alertas)->toHaveKey('error'); 

    // Paso 3: Enviar al componente un precio de 0.
    $producto->precio = 0;

    // Esperamos que el arreglo esté completamente vacío (sin errores)
    $alertas = $producto->validar();
    expect($alertas)->toBeEmpty();

    // Paso 4: Enviar a evaluar un precio de 25.50.
    $producto->precio = 25.50;
    $alertas = $producto->validar();
    
    // Esperamos que el arreglo esté completamente vacío (sin errores)
    expect($alertas)->toBeEmpty();
});