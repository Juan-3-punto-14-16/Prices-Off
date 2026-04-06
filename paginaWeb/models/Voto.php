<?php
namespace Model;

class Voto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'voto';
    protected static $columnasDB = ['id', 'voto', 'idregistroproducto', ];

    public $idregistroproducto;
    public $voto;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->idregistroproducto = $args['idregistroproducto'] ?? '';
        $this->voto = $args['voto'] ?? '';
    }
}
