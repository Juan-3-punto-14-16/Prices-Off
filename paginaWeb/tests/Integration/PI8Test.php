<?php
use Model\ActiveRecord;
use Model\Catalogo;
use Model\Ubicacion;
use Model\RegistroProducto;
use Controllers\APIController;

it('registra un producto manualmente y mantiene la integridad de llaves foraneas segun PI_8', function () {

    $nombreProducto = 'Pant_test'; 
    $catalogo = new Catalogo(['nombre' => $nombreProducto]);
    $resCat = $catalogo->guardar();
    $idCat = $resCat['id'];

    try {
        $_POST = [
            'tienda' => 'Tienda JP',
            'direccion' => 'Calle Unica 123 ' . uniqid(),
            'latitud' => '19.432600',
            'longitud' => '-99.133200'
        ];
        $listaProductos = [
            [
                'nombre' => $nombreProducto, 
                'precio' => '10.00',
                'cantidad' => '1',
                'unidadmedida' => 'pieza',
                'idcatalogo' => $idCat
            ]
        ];
        $_POST['productos'] = json_encode($listaProductos);

        ob_start();
        APIController::guardar();
        $jsonSalida = ob_get_clean();
        $respuesta = json_decode($jsonSalida, true);

        try {
            ActiveRecord::confirmarTransaccion();
        } catch (\Throwable $e) {
            // Si no había transacción activa, simplemente ignoramos el error
        }

        // Buscamos usando el ID del catálogo que acabamos de crear
        $registroDB = RegistroProducto::where('idcatalogo', $idCat);
        
        // Si el where falló, intentamos buscar el último registro creado como plan B
        if (!$registroDB) {
            $todos = RegistroProducto::all();
            $registroDB = end($todos);
        }

        expect($registroDB)->toBeInstanceOf(RegistroProducto::class);
        expect((int)$registroDB->idcatalogo)->toBe((int)$idCat);
        expect($registroDB->idubicacion)->not->toBeNull();

        $idUbi = $registroDB->idubicacion;
        $registroDB->eliminar();
        $ubi = Ubicacion::find($idUbi);
        if($ubi) $ubi->eliminar();

    } finally {
        $catalogo->eliminar();
        $_POST = [];
    }
});