<?php
use Model\ViewModel;

it('retorna la estructura de datos correcta al buscar un producto y rechaza los inexistentes según el requerimiento PM_3', function () {
    // PASOS 1 Y 2 LISTOS GRACIAS A LA BD DE PRUEBA Y A LA INTEGRACIÓN DEL APP.PHP EN EL ARCHIVO PEST.PHP

    // PASO 3: Solicitar al componente la información de un producto enviando un nombre válido (Jitomate)
    $resultados = ViewModel::buscarProductos('Jitomate');
    expect($resultados)->toBeArray()->not()->toBeEmpty();

    // Debe retornar por lo menos un objeto, estructurado con los atributos correctos necesarios para el frontend
    $producto = $resultados[0];
    expect($producto->nombre)->toBe('Jitomate')
        ->and($producto)->toHaveProperties([
            'id', 'publicado', 'preciounitario', 'unidadmedida',
            'nombre',
            'latitud', 'longitud', 'tienda', 'direccion',
            'votospositivos', 'votosnegativos'
        ]);

    // PASO 4: Solicitar al componente la información de un producto enviando un nombre inexistente (Plutonio)
    $resultados = ViewModel::buscarProductos('Plutonio');
    expect($resultados)->toBeArray()->toBeEmpty();

    // PASO 5: Solicitar al componente la información de un producto enviando un nombre vacío
    $resultados = ViewModel::buscarProductos('');
    expect($resultados)->toBeArray()->toBeEmpty();
});
