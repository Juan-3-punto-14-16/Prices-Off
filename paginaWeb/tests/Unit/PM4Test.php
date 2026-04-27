<?php
use Model\ActiveRecord;
use Model\RegistroProducto;

it('elimina únicamente los registros con más de 15 días de antigüedad según el requerimiento PM_4', function () {
    $producto = new RegistroProducto([
        'precio' => 30,
        'unidadmedida' => 'kg',
        'cantidad' => 3,
        'idubicacion' => 15,
        'idcatalogo' => 1
    ]);

    ActiveRecord::iniciarTransaccion();
    try {
        // PASO 1: Insertar en la BD el registro A con una fecha de registro de hace 200 días
        $producto->fecharegistro = date('Y/m/d', strtotime('-200 day'));
        $resultadoA = $producto->guardar();

        // PASO 2: Insertar en la BD el registro B con una fecha de registro de hace 5 días
        $producto->fecharegistro = date('Y/m/d', strtotime('-5 day'));
        $resultadoB = $producto->guardar();
        
        // PASO 3: Invocar al método de eliminación
        $filasAfectadas = RegistroProducto::eliminarRegistrosAntiguos();
        expect($filasAfectadas)->toBeGreaterThanOrEqual(1);

        // PASO 4: Consultar el conteo total de registros restantes
        $registro = RegistroProducto::find($resultadoA['id']);
        expect($registro)->toBeNull();

        $registro = RegistroProducto::find($resultadoB['id']);
        expect($registro)->not()->toBeNull();
    } finally {
        ActiveRecord::revertirTransaccion();
    }
});
