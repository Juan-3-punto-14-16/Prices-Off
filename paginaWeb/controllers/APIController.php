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
        $direccionBusqueda = $_POST['direccion'];
        $ubicacionExistente = Ubicacion::where('direccion', $direccionBusqueda);

        // Se registra u obtiene la ubicación
        if ($ubicacionExistente) {
            $idubicacion = $ubicacionExistente->id;
        } else {
            $ubicacion = new Ubicacion($_POST);
            $ubicacionResultado = $ubicacion->guardar();
            $idubicacion = $ubicacionResultado['id'];
        }

        // Se extrae cada uno de los productos en un arreglo asociativo
        $productos = json_decode($_POST['productos'], true);

        // Por cada uno de los productos...
        foreach($productos as $producto) {
            $nombre = Catalogo::normalizarNombre($producto['nombre']);
            $productoExistente = Catalogo::where('nombre', $nombre);

            // Se registra u obtiene el nombre en el catalogo
            if ($productoExistente) {
                $idcatalogo = $productoExistente->id;
            } else {
                $catalogo = new Catalogo(['nombre' => $nombre]);
                $catalogoResultado = $catalogo->guardar();
                $idcatalogo = $catalogoResultado['id'];
            }

            // Se crea el objeto y se almacena en la BD
            $registroProducto = new RegistroProducto([
                'precio' => $producto['precio'],
                'unidadmedida' => $producto['unidadmedida'],
                'cantidad' => $producto['cantidad'],
                'idubicacion' => $idubicacion,
                'idcatalogo' => $idcatalogo
            ]);

            $registroProducto->guardar();
        }

        echo json_encode([
            'resultado' => 'ok',
            'mensaje' => 'Todos los productos fueron registrados'
        ]);
    }

    public static function registrarVoto() {
        // TODO: Pendiente de implementar lógica
    }
}
