<?php
namespace Controllers;

use Model\Catalogo;
use Model\RegistroProducto;
use Model\Ubicacion;

class APIController {
    public static function autocompletar() {
        // TODO: Pendiente de implementar lógica
    }

    public static function buscar() {
        // TODO: Pendiente de implementar lógica
    }

    public static function escanear() {
        // TODO: Pendiente de implementar lógica
    }

    public static function obtenerDireccion() {
        $lat = filter_var($_POST['latitud'] ?? '', FILTER_VALIDATE_FLOAT);
        $lng = filter_var($_POST['longitud'] ?? '', FILTER_VALIDATE_FLOAT);

        if($lat === false || $lng === false) {
            echo json_encode(['error' => 'Coordenadas inválidas']);
            return;
        }

        $apiKey = $_ENV['GOOGLE_GEOCODING_API_KEY'];
        $urlBase = "https://maps.googleapis.com/maps/api/geocode/json?";

        $parametros = http_build_query([
            'latlng' => $lat . ',' . $lng,
            'key' => $apiKey
        ]);

        $urlFinal = $urlBase . $parametros;

        $respuesta = file_get_contents($urlFinal);
        $datos = json_decode($respuesta, true);

        if($datos['status'] === 'OK' && !empty($datos['results'])) {
            $direccion = $datos['results'][0]['formatted_address'];
            echo json_encode(['direccion' => $direccion]);
        } else {
            echo json_encode(['error' => 'No se pudo obtener la dirección']);
        }
    }

    public static function guardar() {
        // Se verifica que los datos de ubicacion sean validos
        $ubicacion = new Ubicacion($_POST);
        $erroresUbicacion = $ubicacion->validar();

        // Si hay errores, se retornan en el JSON y se termina la ejecucion
        if(!empty($erroresUbicacion)) {
            self::respuestaError($erroresUbicacion);
            return;
        }

        // Como no hay errores, se revisa si la dirección ya esta registrada en la BD
        $ubicacionExistente = Ubicacion::where('direccion', $ubicacion->direccion);

        // Si la direccion ya esta registrada, tomamos el id del objeto retornado por el WHERE
        if ($ubicacionExistente) {
            $idubicacion = $ubicacionExistente->id;
        } else { // Si no estaba registrada, la almacenamos en la BD y obtenemos su ID
            $ubicacionResultado = $ubicacion->guardar();
            $idubicacion = $ubicacionResultado['id'];
        }

        // Se extrae cada uno de los productos en un arreglo asociativo
        $productos = json_decode($_POST['productos'] ?? '', true);

        // Se verifica que no este vacio el arreglo de productos, y que sea un arreglo...
        // Si hay errores, se retornan en el JSON y se termina la ejecucion
        if(empty($productos) || !is_array($productos)) {
            self::respuestaError(['error' => ['No se recibieron productos para registrar']]);
            return;
        }

        // Por cada uno de los productos...
        foreach($productos as $producto) {
            // Se crea el producto y el mismo normaliza su nombre en el constructor
            $catalogo = new Catalogo($producto);

            // // Ya normalizado, se valida el nombre
            $erroresCatalogo = $catalogo->validar();

            // Si hay errores, se retornan en el JSON y se termina la ejecucion
            if(!empty($erroresCatalogo)) {
                self::respuestaError($erroresCatalogo);
                return;
            }

            // Como no hay errores, se revisa si el nombre ya esta registrado en la BD
            $productoExistente = Catalogo::where('nombre', $catalogo->nombre);

            // Si el nombre ya esta registrado, tomamos el id del objeto retornado por el WHERE
            if ($productoExistente) {
                $idcatalogo = $productoExistente->id;
            } else { // Si no estaba registrado, lo almacenamos en la BD y obtenemos su ID
                $catalogoResultado = $catalogo->guardar();
                $idcatalogo = $catalogoResultado['id'];
            }

            // Se crea el objeto
            $producto['idubicacion'] = $idubicacion;
            $producto['idcatalogo'] = $idcatalogo;
            $registroProducto = new RegistroProducto($producto);

            // Se verifica que los datos de del producto sean validos
            $erroresRegistro = $registroProducto->validar();

            // Si hay errores, se retornan en el JSON y se termina la ejecucion
            if(!empty($erroresRegistro)) {
                self::respuestaError($erroresRegistro);
                return;
            }

            // Se almacena en la BD
            $registroProducto->guardar();
        }

        echo json_encode(['mensaje' => 'Todos los productos fueron registrados']);
    }

    public static function registrarVoto() {
        // TODO: Pendiente de implementar lógica
    }

    private static function respuestaError($errores) {
        echo json_encode(['error' => $errores['error']]);
    }
}
