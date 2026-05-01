<?php
use Controllers\APIController;

it('maneja errores de autenticacion con Google Cloud sin romper el servidor segun PI_10', function () {
    $originalEnv = $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] ?? null;
    $rutaFalsa = __DIR__ . '/../credenciales_falsas.json';
    
    $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] = $rutaFalsa;
    putenv("GOOGLE_APPLICATION_CREDENTIALS=$rutaFalsa");

    // Creamos un archivo temporal real para que las validaciones de PHP pasen
    $tempFile = tempnam(sys_get_temp_dir(), 'test_img');
    copy(__DIR__ . '/../archivos_prueba/imagen_valida.jpeg', $tempFile);

    $_FILES['imagen'] = [
        'name' => 'imagen_valida.jpeg',
        'type' => 'image/jpeg',
        'tmp_name' => $tempFile, // Usamos el archivo temporal real
        'error' => UPLOAD_ERR_OK,
        'size' => filesize($tempFile),
    ];

    ob_start();
    try {
        APIController::escanear();
        $jsonSalida = ob_get_clean();
        $respuesta = json_decode($jsonSalida, true);
    } catch (\Throwable $e) {
        ob_end_clean();
        $this->fail("El servidor se rompió (Fatal Error) en lugar de manejar la excepción: " . $e->getMessage());
    }

    expect($respuesta)->toBeArray();
    expect($respuesta)->toHaveKey('error');
    
    $mensajeError = is_array($respuesta['error']) ? $respuesta['error'][0] : $respuesta['error'];
    
    expect($mensajeError)->toMatch('/(Error al procesar|No se recibió ninguna imagen|autenticación)/i');

    if ($originalEnv) {
        putenv("GOOGLE_APPLICATION_CREDENTIALS=$originalEnv");
        $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] = $originalEnv; // ¡Esta línea faltaba!
    } else {
        unset($_ENV['GOOGLE_APPLICATION_CREDENTIALS']);
    }
    if (file_exists($tempFile)) @unlink($tempFile);
    unset($_FILES['imagen']);
});