<?php
namespace Model;

class Voto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'votos';
    protected static $columnasDB = ['id', 'voto', 'idregistroproducto', ];

    public $idregistroproducto;
    public $voto;

    public function __construct($args = []){
        $this->id = filter_var($args['id'] ?? null, FILTER_VALIDATE_INT) ?: null;
        $this->idregistroproducto = trim($args['idregistroproducto'] ?? '');
        $this->voto = $args['voto'] ?? '';
    }

    public function validar() {
        self::$alertas = [];

        if($this->idregistroproducto === '' || !filter_var($this->idregistroproducto, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'El ID del producto es inválido';
        }

        if ($this->voto !== 'true' && $this->voto !== 'false' && $this->voto !== '') {
            self::$alertas['error'][] = 'El valor del voto es inválido';
        }

        return self::$alertas;
    }
}
