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
        $this->unidadmedida = trim($args['unidadmedida'] ?? '');
        $this->cantidad = $args['cantidad'] ?? '';
        $this->idubicacion = $args['idubicacion'] ?? '';
        $this->idcatalogo = $args['idcatalogo'] ?? '';
    }

    public function validar() {
        self::$alertas = [];

        if($this->precio === '' || filter_var($this->precio, FILTER_VALIDATE_FLOAT) === false || $this->precio < 0) {
            self::$alertas['error'][] = 'El precio debe ser un número válido.';
        }

        if($this->cantidad === '' || filter_var($this->cantidad, FILTER_VALIDATE_FLOAT) === false || $this->cantidad <= 0) {
            self::$alertas['error'][] = 'La cantidad es obligatoria y debe ser numérica.';
        }

        // ESTO SE TIENE QUE CAMBIAR
        $unidadesPermitidas = ['kg', 'litro', 'pieza'];
        if($this->unidadmedida === '' || !in_array(strtolower($this->unidadmedida), $unidadesPermitidas)) {
            self::$alertas['error'][] = 'La unidad de medida no es válida';
        }

        return self::$alertas;
    }
}
