<?php
namespace Controllers;

use Model\ActiveRecord;
use Model\Catalogo;
use Model\RegistroProducto;
use Model\Ubicacion;
use Model\ViewModel;

class APIController {
    public static function autocompletar() {
        $nombre = trim($_GET['nombre'] ?? '');

        if(empty($nombre)) {
            echo json_encode(['datos' => []]);
            return;
        }

        $resultados = Catalogo::iLike('nombre', $nombre);
        echo json_encode(['datos' => $resultados]);
    }

    public static function buscar() {
        $nombre = trim($_GET['nombre'] ?? '');

        if(empty($nombre)) {
            echo json_encode(['error' => ['El campo de búsqueda no puede estar vacío']]);
            return;
        }

        $resultados = ViewModel::consulta($nombre);
        if(empty($resultados)) {
            echo json_encode(['error' => ['No se encontraron productos con ese nombre']]);
            return;
        }

        echo json_encode([
            'mensaje' => 'Búsqueda exitosa',
            'datos' => $resultados 
        ]);
    }

    public static function escanear() {
        // TODO: Pendiente de implementar lógica
    }

    public static function obtenerDireccion() {
        $lat = filter_var($_GET['latitud'] ?? '', FILTER_VALIDATE_FLOAT);
        $lng = filter_var($_GET['longitud'] ?? '', FILTER_VALIDATE_FLOAT);

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
            echo json_encode(['error' => $erroresUbicacion['error']]);
            return;
        }

        // Se extrae cada uno de los productos en un arreglo asociativo
        $productos = json_decode($_POST['productos'] ?? '', true);

        // Se verifica que no este vacio el arreglo de productos, y que sea un arreglo...
        // Si hay errores, se retornan en el JSON y se termina la ejecucion
        if(empty($productos) || !is_array($productos)) {
            echo json_encode(['error' => ['No se recibieron productos para registrar']]);
            return;
        }

        // Ningun guardar() es definitivo aún A PARTIR DE AQUÍ
        ActiveRecord::iniciarTransaccion();

        // Como no hay errores, se revisa si la dirección ya esta registrada en la BD
        $ubicacionExistente = Ubicacion::where('direccion', $ubicacion->direccion);

        // Si la direccion ya esta registrada, tomamos el id del objeto retornado por el WHERE
        if ($ubicacionExistente) {
            $idubicacion = $ubicacionExistente->id;
        } else { // Si no estaba registrada, la almacenamos en la BD y obtenemos su ID
            $ubicacionResultado = $ubicacion->guardar();
            $idubicacion = $ubicacionResultado['id'];
        }

        // Por cada uno de los productos...
        if(!self::procesarListaProductos($productos, $idubicacion)){
            return;
        }

        echo json_encode(['mensaje' => 'Todos los productos fueron registrados']);
    }

    // FUNCIÓN HELPER
    private static function procesarListaProductos($productos, $idubicacion) {
        foreach($productos as $producto) {
            // Se crea el producto y el mismo normaliza su nombre en el constructor
            $catalogo = new Catalogo($producto);

            // Ya normalizado, se valida el nombre
            $erroresCatalogo = $catalogo->validar();

            // Si hay errores, se retornan en el JSON y se termina la ejecucion
            if(!empty($erroresCatalogo)) {
                ActiveRecord::revertirTransaccion();
                echo json_encode(['error' => $erroresCatalogo['error']]);
                return false;
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

            // Se crea el objeto del RegistroProducto
            $producto['idubicacion'] = $idubicacion;
            $producto['idcatalogo'] = $idcatalogo;
            $registroProducto = new RegistroProducto($producto);

            // Se verifica que los datos de del producto sean validos
            $erroresRegistro = $registroProducto->validar();

            // Si hay errores, se retornan en el JSON y se termina la ejecucion
            if(!empty($erroresRegistro)) {
                ActiveRecord::revertirTransaccion();
                echo json_encode(['error' => $erroresRegistro['error']]);
                return false;
            }

            // Se almacena en la BD
            $registroProducto->guardar();
        }

        // Entonces sí, ejecutamos todos los query en la BD se vuelven PERMANENTES
        ActiveRecord::confirmarTransaccion();
        return true; 
    }

    public static function registrarVoto() {
        // TODO: Pendiente de implementar lógica
    }
}
