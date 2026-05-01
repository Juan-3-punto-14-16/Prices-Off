<?php
use Model\ActiveRecord;
use Model\RegistroProducto;
use Model\Catalogo;
use Model\Ubicacion;

it('ejecuta la limpieza de caducidad y elimina unicamente los registros viejos segun la PI_4', function () {
    ActiveRecord::iniciarTransaccion();

    try {
        //CREAMOS DEPENDENCIAS
        $catalogoTemp = new Catalogo(['nombre' => 'Jitomate de Prueba']);
        $resCat = $catalogoTemp->guardar();
        $idCat = $resCat['id'];

        $ubicacionTemp = new Ubicacion([
            'direccion' => 'Calle Falsa 123', 'latitud' => '20.6667', 'longitud' => '-103.3333'
        ]);
        $resUbi = $ubicacionTemp->guardar();
        $idUbi = $resUbi['id'];

        $idsNuevos = [];
        $idsViejos = [];

        // CREAR LOS 10 REGISTROS Y GUARDAR SUS IDs
        for ($i = 0; $i < 5; $i++) {
            // Producto de hoy
            $nuevo = new RegistroProducto([
                'precio' => 25.50, 'cantidad' => 1, 'unidadmedida' => 'pieza',
                'idcatalogo' => $idCat, 'idubicacion' => $idUbi
            ]); 
            $resNuevo = $nuevo->guardar();
            $idsNuevos[] = $resNuevo['id']; 

            // Producto obsoleto (hace 20 días)
            $viejo = new RegistroProducto([
                'precio' => 15.00, 'cantidad' => 2, 'unidadmedida' => 'kg',
                'idcatalogo' => $idCat, 'idubicacion' => $idUbi
            ]);
            $viejo->fecharegistro = date('Y-m-d', strtotime('-20 days'));
            $resViejo = $viejo->guardar();
            $idsViejos[] = $resViejo['id']; 
        }

        RegistroProducto::eliminarRegistrosAntiguos();

        // Obtenemos todos los registros que sobrevivieron en la BD
        $registrosSobrevivientes = RegistroProducto::all();
        
        // Extraemos solo los IDs de los que sobrevivieron para buscar fácilmente
        $idsSobrevivientes = array_map(function($reg) {
            return $reg->id;
        }, $registrosSobrevivientes);

        //Verificamos que los 5 IDs de los productos de HOY sigan existiendo
        foreach ($idsNuevos as $idNuevo) {
            expect(in_array($idNuevo, $idsSobrevivientes))->toBeTrue();
        }

        //Verificamos que los 5 IDs de los productos VIEJOS hayan sido eliminados
        foreach ($idsViejos as $idViejo) {
            expect(in_array($idViejo, $idsSobrevivientes))->toBeFalse();
        }

    } finally {
        ActiveRecord::revertirTransaccion();
    }
});