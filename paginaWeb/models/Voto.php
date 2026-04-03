<?php
namespace Model;

class Voto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'voto';
    protected static $columnasDB = ['id', 'idRegistroProducto', 'voto'];

    public $idRegistroProducto, $voto;

    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->idRegistroProducto = $args['idRegistroProducto'] ?? '';
        $this->voto = $args['voto'] ?? '';
    }
}