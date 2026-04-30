<?php
use Controllers\APIController;

it('valida el peso y formato de los archivos subidos según el requerimiento PM_8', function () {
    $rutaDocx = __DIR__ . '/../archivos_prueba/extension_invalida.docx';
    $rutaJpeg = __DIR__ . '/../archivos_prueba/imagen_valida.jpeg';

    // PASO 1: Inyectar un arreglo en FILES con tamaño valido, pero extensión invalida
    $_FILES['ticket'] = [
        'name' => 'extension_invalida.docx',
        'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'tmp_name' => $rutaDocx, // EXTENSIÓN INVÁLIDA
        'error' => UPLOAD_ERR_OK,
        'size' => 1024
    ];

    ob_start();
    APIController::escanear();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('error', 'Formato no permitido. Solo sube imágenes JPG, PNG o WEBP.');

    // PASO 2: Inyectar un arreglo simulando un archivo con extensión válida, pero tamaño inválido
    $_FILES['ticket'] = [
        'name' => 'imagen_valida.jpeg',
        'type' => 'image/jpeg',
        'tmp_name' => $rutaJpeg,
        'error' => UPLOAD_ERR_OK,
        'size' => 52428800 // <- Tamaño inválido (50MB)
    ];

    ob_start();
    APIController::escanear();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('error', 'El archivo es demasiado pesado. Máximo 5MB.');

    // PASO 3: Inyectar un arreglo simulando un archivo con tamaño y extensión válida
    $_FILES['ticket'] = [
        'name' => 'imagen_valida.jpeg',
        'type' => 'image/png',
        'tmp_name' => $rutaJpeg,
        'error' => UPLOAD_ERR_OK,
        'size' => 2097152 // <- Tamaño válido
    ];

    // Tronamos a proposito el código para abotar la petición a la API de Document AI
    $credencialReal = $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] ?? '';
    $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] = 'archivo_falso_para_abortar_api.json';

    ob_start();
    APIController::escanear();
    $resultado = json_decode(ob_get_clean(), true);
    expect($resultado)->toHaveKey('error')
        ->and($resultado['error'])->toContain('Error al procesar el documento con IA:');

    // LIMPIEZA FINAL
    $_FILES = [];
    $_ENV['GOOGLE_APPLICATION_CREDENTIALS'] = $credencialReal;
});
