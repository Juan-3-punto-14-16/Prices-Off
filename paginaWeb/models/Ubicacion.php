<?php
namespace Model;

class Ubicacion extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'ubicacion';
    protected static $columnasDB = ['id', 'latitud', 'longitud', 'tienda'];

    public $latitud, $longitud, $tienda;

    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->latitud = $args['latitud'] ?? '';
        $this->longitud = $args['longitud'] ?? '';
        $this->tienda = $args['tienda'] ?? '';
    }
}