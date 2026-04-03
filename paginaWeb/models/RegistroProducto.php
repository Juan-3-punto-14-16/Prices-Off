<?php
namespace Model;

class RegistroProducto extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'registroProducto';
    protected static $columnasDB = ['id', 'precio', 'fechaRegistro', 'unidadMedida', 'cantidad', 'idUbicacion', 'idCatalogo'];

    public $precio, $fechaRegistro, $unidadMedida, $cantidad, $idUbicacion, $idCatalogo;

    public function __construct($args = []){
        $this->id = $args['id'] ?? NULL;
        $this->precio = $args['precio'] ?? '';
        $this->fechaRegistro = date('Y/m/d');
        $this->unidadMedida= $args['unidadMedida'] ?? '';
        $this->cantidad = $args['cantidad'] ?? '';
        $this->idUbicacion = $args['idUbicacion'] ?? '';
        $this->idCatalogo = $args['idCatalogo'] ?? '';
    }
}