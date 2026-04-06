<?php
namespace Model;

class RegistroProducto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'registroproducto';
    protected static $columnasDB = ['id', 'precio', 'fecharegistro', 'unidadmedida', 'cantidad', 'idubicacion', 'idcatalogo'];

    public $precio;
    public $fecharegistro;
    public $unidadmedida;
    public $cantidad;
    public $idubicacion;
    public $idcatalogo;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->precio = $args['precio'] ?? '';
        $this->fecharegistro = date('Y/m/d');
        $this->unidadmedida= $args['unidadmedida'] ?? '';
        $this->cantidad = $args['cantidad'] ?? '';
        $this->idubicacion = $args['idubicacion'] ?? '';
        $this->idcatalogo = $args['idcatalogo'] ?? '';
    }
}
