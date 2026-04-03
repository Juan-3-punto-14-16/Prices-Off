<?php
namespace Model;

class RegistroProducto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'registroProducto';
    protected static $columnasDB = ['id', 'precio', 'fechaRegistro', 'unidadMedida', 'cantidad', 'idUbicacion', 'idCatalogo'];

    public $precio;
    public $fechaRegistro;
    public $unidadMedida;
    public $cantidad;
    public $idUbicacion;
    public $idCatalogo;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->precio = $args['precio'] ?? '';
        $this->fechaRegistro = date('Y/m/d');
        $this->unidadMedida= $args['unidadMedida'] ?? '';
        $this->cantidad = $args['cantidad'] ?? '';
        $this->idUbicacion = $args['idUbicacion'] ?? '';
        $this->idCatalogo = $args['idCatalogo'] ?? '';
    }
}
