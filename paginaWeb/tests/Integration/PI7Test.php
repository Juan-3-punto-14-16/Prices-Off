<?php
use Controllers\APIController;

it('valida la conexión y credenciales IAM de Google Cloud Document AI segun PI_7', function () {
    $rutaImagenPrueba = __DIR__ . '/../archivos_prueba/imagen_valida.jpeg';

    $_FILES['imagen'] = [
        'name' => 'imagen_valida.jpg',
        'type' => 'image/jpeg',
        'tmp_name' => $rutaImagenPrueba,
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($rutaImagenPrueba),
    ];

    // 2. ACCIÓN: Ejecutar la petición al endpoint
    ob_start();
    
    // Capturamos cualquier posible excepción que Google Cloud lance
    try {
        APIController::escanear(); 
        $jsonSalida = ob_get_clean();
        $respuesta = json_decode($jsonSalida, true);
        $excepcionGoogle = null;
    } catch (\Google\ApiCore\ApiException $e) {
        ob_end_clean();
        $excepcionGoogle = $e;
        $respuesta = [];
    } catch (\Exception $e) {
        ob_end_clean();
        $excepcionGoogle = $e;
        $respuesta = [];
    }
    
    //Confirmar que no hubo excepciones a nivel servidor o SDK de Google
    expect($excepcionGoogle)->toBeNull();

    //Evitar errores 401 (Unauthorized) o 403 (Forbidden) del lado de la API
    expect($respuesta)->not->toHaveKey('error', 'Unauthorized');
    expect($respuesta)->not->toHaveKey('error', 'Forbidden');
    expect($respuesta)->not->toHaveKey('error', 'Unauthenticated');

    //Validar que la petición se resolvió exitosamente devolviendo datos del Documento
    expect($respuesta)->toBeArray();
    unset($_FILES['imagen']);
});