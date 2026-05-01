<?php
use Model\ActiveRecord;
use Model\Catalogo;
use Model\Ubicacion;
use Model\RegistroProducto;
use Model\Voto; 
use Controllers\APIController;

it('devuelve la estructura completa de datos con los JOINs y campos extendidos segun PI_6', function () {
    ActiveRecord::iniciarTransaccion();

    try {
        //Crear datos con todas las relaciones (JOINs)
        $nombreProducto = 'Leche_test';

        $catalogoTemp = new Catalogo(['nombre' => $nombreProducto]);
        $resCat = $catalogoTemp->guardar();
        $idCat = $resCat['id'];

        $ubicacionTemp = new Ubicacion([
            'tienda' => 'Supermercado Testing',
            'direccion' => 'Avenida Código 404',
            'latitud' => '20.0',
            'longitud' => '-103.0'
        ]);
        $resUbi = $ubicacionTemp->guardar();
        $idUbi = $resUbi['id'];

        $registro = new RegistroProducto([
            'precio' => 25.00,
            'cantidad' => 1,
            'unidadmedida' => 'litro',
            'idcatalogo' => $idCat,
            'idubicacion' => $idUbi
        ]);
        $resReg = $registro->guardar();
        $idProd = $resReg['id'];

        (new Voto(['voto' => 'true', 'idregistroproducto' => $idProd]))->guardar();    
        (new Voto(['voto' => 'false', 'idregistroproducto' => $idProd]))->guardar();
        (new Voto(['voto' => 'false', 'idregistroproducto' => $idProd]))->guardar();

        //Ejecutar la petición GET al endpoint de búsqueda
        $_GET['nombre'] = $nombreProducto;

        ob_start();
        APIController::buscar();
        $jsonSalida = ob_get_clean();

        $respuesta = json_decode($jsonSalida, true);

        //asegurarnos de que la petición fue exitosa
        expect($respuesta)->toHaveKey('datos');
        $datos = $respuesta['datos'];
        
        // Verificamos que se recuperó el producto que acabamos de crear
        expect(count($datos))->toBe(1);

        // Capturamos el primer y único objeto del arreglo
        $primerObjeto = $datos[0];

        //Validar la existencia obligatoria de las llaves extendidas
        $llavesRequeridas = [
            'unidadmedida', 'publicado', 'tienda', 'direccion', 
            'votospositivos', 'votosnegativos'
        ];

        foreach ($llavesRequeridas as $llave) {
            expect($primerObjeto)->toHaveKey($llave);
        }

        //Validar formato de la fecha 'publicado' (DD/MM/YYYY)
        // Usamos una Expresión Regular para confirmar el formato exacto devuelto por TO_CHAR
        expect($primerObjeto['publicado'])->toMatch('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/\d{4}$/');
        // Además, como lo insertamos hoy, debe ser el día actual
        expect($primerObjeto['publicado'])->toBe(date('d/m/Y'));

        // Validar que los valores corresponden a la información de los JOINs reales
        expect($primerObjeto['unidadmedida'])->toBe('litro');
        expect($primerObjeto['tienda'])->toBe('Supermercado Testing');
        expect($primerObjeto['direccion'])->toBe('Avenida Código 404');

        // Validar matemáticamente los contadores de PostgreSQL 
        expect((int) $primerObjeto['votospositivos'])->toBe(1);
        expect((int) $primerObjeto['votosnegativos'])->toBe(2);


    } finally {
        // Limpiamos la base de datos
        ActiveRecord::revertirTransaccion();
        unset($_GET['nombre']);
    }
});