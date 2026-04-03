<?php
namespace Model;

class Voto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'voto';
    protected static $columnasDB = ['id', 'idRegistroProducto', 'voto'];

    public $idRegistroProducto;
    public $voto;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->idRegistroProducto = $args['idRegistroProducto'] ?? '';
        $this->voto = $args['voto'] ?? '';
    }
}
