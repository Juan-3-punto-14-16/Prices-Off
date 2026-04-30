<?php
use Model\RegistroProducto;

it('asigna autónomamente la fecha actual del sistema ignorando entradas del usuario según el requerimiento PM_10', function () {
    // PASO 1: Instanciar un nuevo registro enviando un paquete de datos que incluya una fecha pasada manipulada
    $producto1 = new RegistroProducto(['fecharegistro' => '2020-20-20']);

    // PASO 2: Instanciar un segundo registro enviando un paquete de datos sin el campo de fecha
    $producto2 = new RegistroProducto();

    // PASO 3: Consultar el atributo interno para ambas instancias
    expect($producto1->fecharegistro)->toBe(date('Y-m-d'));
    expect($producto2->fecharegistro)->toBe(date('Y-m-d'));
});
