<?php
namespace Model;

class Catalogo extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'catalogo';
    protected static $columnasDB = ['id', 'nombre'];

    public $nombre;

    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->nombre = $args['nombre'] ?? '';
    }
}