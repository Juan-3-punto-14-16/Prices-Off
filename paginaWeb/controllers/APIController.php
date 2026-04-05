<?php
namespace Controllers;

class APIController {
    public static function buscar() {
        // TODO: Pendiente de implementar lógica
    }

    public static function escanear() {
        // TODO: Pendiente de implementar lógica
    }

    public static function obtenerDireccion() {
        $lat = $_POST['latitud'] ?? '';
        $lng = $_POST['longitud'] ?? '';

        if(!$lat || !$lng) {
            echo json_encode(['error' => 'No se enviaron las coordenadas']);
            return; // Detiene la ejecución aquí mismo
        }

        $apiKey = $_ENV['GOOGLE_GEOCODING_API_KEY'];
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

        $respuesta = file_get_contents($url);
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
