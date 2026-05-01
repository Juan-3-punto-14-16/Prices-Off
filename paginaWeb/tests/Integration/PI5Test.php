<?php
use Model\ActiveRecord;
use Model\Catalogo;
use Model\Ubicacion;
use Model\RegistroProducto;
use Controllers\APIController;

it('devuelve los resultados de búsqueda ordenados ascendentemente por precio segun PI_5', function () {
    ActiveRecord::iniciarTransaccion();

    try {
        // Crear dependencias falsas con un nombre ÚNICO
        $nombreProducto = 'Leche_test';
        $catalogoTemp = new Catalogo(['nombre' => $nombreProducto]);
        $resCat = $catalogoTemp->guardar();
        $idCat = $resCat['id'];

        $ubicacionTemp = new Ubicacion([
            'direccion' => 'Sucursal Prueba', 'latitud' => '20.0', 'longitud' => '-103.0'
        ]);
        $resUbi = $ubicacionTemp->guardar();
        $idUbi = $resUbi['id'];

        // Insertamos los precios de forma DESORDENADA intencionalmente
        $preciosDesordenados = [25.00, 15.00, 30.00, 10.00];

        foreach ($preciosDesordenados as $precio) {
            $registro = new RegistroProducto([
                'precio' => $precio,
                'cantidad' => 1,
                'unidadmedida' => 'litro',
                'idcatalogo' => $idCat,
                'idubicacion' => $idUbi
            ]);
            $registro->guardar();
        }

        $_GET['nombre'] = $nombreProducto;

        ob_start();
        APIController::buscar();
        $jsonSalida = ob_get_clean();

        $respuesta = json_decode($jsonSalida, true);

        //La petición debe ser exitosa y contener 'datos'
        expect($respuesta)->toHaveKey('datos');
        $datos = $respuesta['datos'];
        
        // Debemos tener exactamente los 4 registros que insertamos
        expect(count($datos))->toBe(4);

        // Iterar el arreglo y comparar que el índice [i] sea menor o igual al [i+1]
        for ($i = 0; $i < count($datos) - 1; $i++) {

            $precioActual = (float) $datos[$i]['preciounitario'];
            $precioSiguiente = (float) $datos[$i + 1]['preciounitario'];

            // El precio en la posición actual SIEMPRE debe ser menor o igual al de la siguiente
            expect($precioActual)->toBeLessThanOrEqual($precioSiguiente);
        }

        // Validación explícita: El índice [0] siempre debe contener el precio más bajo (10.00)
        $precioMasBajo = (float) $datos[0]['preciounitario'];
        expect($precioMasBajo)->toBe(10.00);

    } finally {
        ActiveRecord::revertirTransaccion();
        unset($_GET['nombre']);
    }
});