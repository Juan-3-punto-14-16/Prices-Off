<?php
namespace Controllers;

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
        // TODO: Pendiente de implementar lógica
    }

    public static function registrarVoto() {
        // TODO: Pendiente de implementar lógica
    }
}
