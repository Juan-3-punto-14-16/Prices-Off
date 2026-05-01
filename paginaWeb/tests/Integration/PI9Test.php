<?php
use Model\ActiveRecord;
use Model\Voto;
use Controllers\APIController;

it('aborta la votacion y devuelve error si el producto no existe segun PI_9', function () {
    $idFicticio = 999999;

    $_POST = [
        'idregistroproducto' => $idFicticio,
        'voto' => 'true'
    ];

    ob_start();
    APIController::registrarVoto();
    $jsonSalida = ob_get_clean();
    $respuesta = json_decode($jsonSalida, true);

    // 3. INSPECCIÓN (Expected Results)

    // El controlador debe abortar y devolver un error
    expect($respuesta)->toHaveKey('error');

    // debería caer en una validación previa o en un error de datos inválidos.
    expect($respuesta['error'])->toContain('Datos inválidos');

    // Verificamos que NO se haya creado ningún voto con ese ID ficticio
    $votoEnDB = Voto::where('idregistroproducto', $idFicticio);
    expect($votoEnDB)->toBeNull();

    $_POST = [];
});