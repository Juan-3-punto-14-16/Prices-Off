<?php
namespace Model;

class Ubicacion extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'ubicacion';
    protected static $columnasDB = ['id', 'latitud', 'longitud', 'tienda', 'direccion'];

    public $latitud;
    public $longitud;
    public $direccion;
    public $tienda;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->latitud = $args['latitud'] ?? '';
        $this->longitud = $args['longitud'] ?? '';
        $this->tienda = trim($args['tienda'] ?? 'Sin Nombre');
        $this->direccion = trim($args['direccion'] ?? '');
    }

    public function validar() {
        self::$alertas = [];

        if($this->latitud === '' || filter_var($this->latitud, FILTER_VALIDATE_FLOAT) === false) {
            self::$alertas['error'][] = 'Latitud inválida';
        }

        if($this->longitud === '' || filter_var($this->longitud, FILTER_VALIDATE_FLOAT) === false) {
            self::$alertas['error'][] = 'Longitud inválida';
        }

        if($this->direccion === '') {
            self::$alertas['error'][] = 'La dirección es obligatoria';
        }

        return self::$alertas;
    }
}
