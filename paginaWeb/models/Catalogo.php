<?php
namespace Model;

class Catalogo extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'catalogo';
    protected static $columnasDB = ['id', 'nombre'];

    public $nombre;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = trim($args['nombre'] ?? '');

        $this->normalizarNombre();
    }

    public function validar() {
        self::$alertas = [];

        if($this->nombre === '') {
            self::$alertas['error'][] = 'El nombre del producto es obligatorio';
        }

        if($this->nombre !== '' && strlen($this->nombre) < 3) {
            self::$alertas['error'][] = 'El nombre del producto es demasiado corto';
        }

        return self::$alertas;
    }

    public function normalizarNombre () {
        // Quitar acentos
        $buscar  = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'];
        $reemplazar = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];
        $this->nombre = str_replace($buscar, $reemplazar, $this->nombre);

        // A minusculas
        $this->nombre = mb_strtolower($this->nombre, 'UTF-8');

        // Primera letra mayuscula y se envia la cadena ya estandarizada
        $this->nombre = ucfirst($this->nombre);
    }
}
