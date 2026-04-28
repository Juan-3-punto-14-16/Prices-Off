<?php
use Controllers\APIController;
use Model\ActiveRecord;
use Model\Voto;

it('comunica el controlador con el modelo para registrar un voto y guardarlo en la BD según el PI_1', function () {
    ActiveRecord::iniciarTransaccion();
    try {
        // PASO 1: Inyectar una petición POST al controlador con un ID de producto válido y un voto true
        $_POST = [
            'idregistroproducto' => 1,
            'voto' => 'true'
        ];

        // PASO 2: El controlador delega la tarea al Modelo
        ob_start();
        APIController::registrarVoto();
        $json = ob_get_clean();

        // Extraemos la respuesta JSON en un arreglo asociativo
        $resultado = json_decode($json, true);

        expect($resultado)->toHaveKey('mensaje', 'creado');
        expect($resultado)->toHaveKey('id');

        // PASO 3: Consultar directamente la bd para buscar el nuevo registro
        $votoBD = Voto::find($resultado['id']);
        expect($votoBD)->not()->toBeNull()->toBeInstanceOf(Voto::class);
    } finally {
        ActiveRecord::revertirTransaccion();
        $_POST = [];
    }
});
