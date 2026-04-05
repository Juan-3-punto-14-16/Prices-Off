<?php
namespace Model;

class Ubicacion extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'ubicacion';
    protected static $columnasDB = ['id', 'latitud', 'longitud', 'tienda'];

    public $latitud;
    public $longitud;
    public $direccion;
    public $tienda;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->latitud = $args['latitud'] ?? '';
        $this->longitud = $args['longitud'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->tienda = $args['tienda'] ?? 'Sin Nombre';
    }
}
