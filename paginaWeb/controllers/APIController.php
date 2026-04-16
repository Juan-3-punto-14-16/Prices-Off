<?php
namespace Controllers;

use Model\ActiveRecord;
use Model\Catalogo;
use Model\RegistroProducto;
use Model\Ubicacion;
use Model\ViewModel;
use Model\Voto;

use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\RawDocument;
use Google\Cloud\DocumentAI\V1\ProcessRequest;

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

        $resultados = ViewModel::buscarProductos($nombre);
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
        // Verificamos que el archivo "ticket" exista y no tenga errores de subida
        if (!isset($_FILES['ticket']) || $_FILES['ticket']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['error' => 'No se recibió ninguna imagen válida']);
            return;
        }

        // Extraemos el archivo de la memoria temporal de la computadora
        $archivoTemporal = $_FILES['ticket']['tmp_name'];

        // Convertimos la imagen a código puro (bytes) y detectamos su formato
        $contenidoImagen = file_get_contents($archivoTemporal);
        $mimeType = mime_content_type($archivoTemporal);

        try {
            // Cargamos la configuración desde el archivo .env
            $projectId = $_ENV['GOOGLE_CLOUD_PROJECT_ID'];
            $location = $_ENV['GOOGLE_CLOUD_LOCATION'] ?? 'us';
            $processorId = $_ENV['DOCUMENT_AI_PROCESSOR_ID'];

            // Construimos la ruta real a tu archivo JSON de credenciales
            $rutaCredenciales = __DIR__ . '/../' . $_ENV['GOOGLE_APPLICATION_CREDENTIALS'];

            // Creamos el Cliente para hablar con Google
            $client = new DocumentProcessorServiceClient(['credentials' => $rutaCredenciales]);

            // Generamos el "Nombre Completo" del procesador
            $nombreProcesador = $client->processorName($projectId, $location, $processorId);

            // Crear el Sobre para la imagen (RawDocument)
            $rawDocument = new RawDocument();
            $rawDocument->setContent($contenidoImagen);
            $rawDocument->setMimeType($mimeType);

            // 9. Crear la "Orden de Trabajo" (ProcessRequest)
            $request = new ProcessRequest([
                'name' => $nombreProcesador,
                'raw_document' => $rawDocument
            ]);

            // Enviar a Google
            $response = $client->processDocument($request);

            // Recibir y abrir el documento ya analizado por la IA
            $document = $response->getDocument();

            // Preparamos nuestro "molde" limpio para enviar al Frontend
            $datos = ['productos' => []];

            // Aquí guardaremos temporalmente los nombres sin precio
            $productoPendiente = null;

            // Recorremos todas las "Entidades" que la IA detectó en el ticket
            foreach ($document->getEntities() as $entity) {
                $tipo = $entity->getType();

                // Detectó un artículo comprado (Line Item)
                if ($tipo === 'line_item') {
                    $producto = self::extraerLineItem($entity);
                    self::procesarLineItem($producto, $datos, $productoPendiente);
                }
            }

            // Colgamos para liberar memoria en el servidor
            $client->close();

            echo json_encode([
                'mensaje' => 'Ticket procesado con éxito',
                'datos' => $datos
            ]);

        } catch (\Exception $e) {
            echo json_encode(['error' => 'Error al procesar el documento con IA: ' . $e->getMessage()]);
        }
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

    public static function registrarVoto() {
        $voto = new Voto($_POST);
        $erroresVoto = $voto->validar();

        if(!empty($erroresVoto)) {
            echo json_encode(['error' => $erroresVoto['error']]);
            return;
        }

        if($voto->id) {
            $votoExistente = Voto::find($voto->id);

            if(!$votoExistente || (int)$votoExistente->idregistroproducto !== (int)$voto->idregistroproducto) {
                echo json_encode(['error' => 'Datos inválidos']);
                return;
            }
        }

        $resultado = '';
        if($voto->voto === '') {
            if ($voto->id) {
                $resultado = $voto->eliminar();
            }
        } else {
            $resultado = $voto->guardar();
        }

        if ($voto->voto === '') {
            echo json_encode(['mensaje' => 'eliminado']);
        } elseif (!$voto->id) {
            echo json_encode([
                'mensaje' => 'creado',
                'id' => $resultado['id']
                ]);
        } else {
            echo json_encode(['mensaje' => 'actualizado']);
        }
    }

    // FUNCIONES HELPER
    // Estas funciones (junto con otras partes del código de este .php) deberían en teoría
    // pertenecer a una carpeta llamada "Services", pero esto queda fuera del alcance del proyecto...
    private static function extraerLineItem ($entity) {
        // Preparamos un producto base con valores por defecto
        $producto = [
            'nombre' => '',
            'precio' => 0,
            'cantidad' => 1,
            'unidadmedida' => ''
        ];

        // Los 'line_item' son como cajas pequeñas que tienen más datos adentro.
        // Iteramos sobre las "Propiedades" de este producto específico:
        foreach ($entity->getProperties() as $propiedad) {
            $subTipo = $propiedad->getType();
            $textoDetectado = $propiedad->getMentionText();

            switch ($subTipo) {
                case 'line_item/description':
                    $producto['nombre'] = trim(str_replace("\n", " ", $textoDetectado));
                    break;
                    
                case 'line_item/amount':
                    $producto['precio'] = (float) filter_var($textoDetectado, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    break;
                    
                case 'line_item/quantity':
                    $producto['cantidad'] = (float) $textoDetectado;
                    break;
                    
                case 'line_item/unit':
                    $producto['unidadmedida'] = trim($textoDetectado);
                    break;

                default:
                    break;
            }
        }

        return $producto;
    }

    private static function procesarLineItem ($producto, &$datos, &$productoPendiente) {
        // Ignorar rebajas totalmente (precios negativos)
        if ($producto['precio'] < 0) {
            return;
        }

        // Producto Perfecto (Tiene nombre Y precio en la misma línea)
        if ($producto['nombre'] !== '' && $producto['precio'] > 0) {
            $datos['productos'][] = $producto;
            $productoPendiente = null; // Limpiamos la sala de espera por seguridad
            return;
        }

        // Nombre Huérfano (Tiene nombre, pero el precio es 0)
        if ($producto['nombre'] !== '' && $producto['precio'] == 0) {
            // Lo metemos a la sala de espera para el siguiente ciclo
            $productoPendiente = $producto;
            return;
        }

        // Si hay alguien en la sala de espera, es su pareja perfecta
        if ($producto['nombre'] === '' && $producto['precio'] > 0 && $productoPendiente !== null) {
            // Fusionamos los datos: Le pasamos el precio, cantidad y unidad al nombre que estaba esperando
            $productoPendiente['precio'] = $producto['precio'];
            $productoPendiente['cantidad'] = $producto['cantidad'] > 0 ? $producto['cantidad'] : 1;
            
            if ($producto['unidadmedida'] !== '') {
                $productoPendiente['unidadmedida'] = $producto['unidadmedida'];
            }

            // ¡Listo! El producto está completo, lo guardamos en la lista final
            $datos['productos'][] = $productoPendiente;

            // Vaciamos la sala de espera
            $productoPendiente = null;
        }
    }

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
}
