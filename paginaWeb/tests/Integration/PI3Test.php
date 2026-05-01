<?php
use Controllers\APIController;

it('procesa una imagen de ticket real usando Google Document AI y retorna los productos mapeados según el PI_3', function () {

    $rutaImagenPrueba = __DIR__ . '/../archivos_prueba/imagen_valida.jpeg';
    
    $_FILES = [
        'ticket' => [
            'name' => 'imagen_valida.jpeg',
            'type' => 'image/jpeg', 
            'tmp_name' => $rutaImagenPrueba,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($rutaImagenPrueba) 
        ]
    ];

    ob_start();
    APIController::escanear();
    $jsonSalida = ob_get_clean();

    $respuesta = json_decode($jsonSalida, true);
    
    // Verificamos que no haya retornado ningún mensaje de error
    expect($respuesta)->not->toHaveKey('error');

    // Verificamos que la estructura básica sea la correcta
    expect($respuesta)->toHaveKey('mensaje', 'Ticket procesado con éxito');
    expect($respuesta)->toHaveKey('datos');
    expect($respuesta['datos'])->toHaveKey('productos');
    expect($respuesta['datos']['productos'])->toBeArray();

    // Verificamos que la IA de Google haya detectado por lo menos 1 producto en la imagen
    expect(count($respuesta['datos']['productos']))->toBeGreaterThan(0);
    expect($respuesta['datos']['productos'])->each->toHaveKeys([
        'nombre', 
        'precio', 
        'cantidad', 
        'unidadmedida'
    ]);

    $_FILES = [];
});