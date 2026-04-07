<?php
namespace Model;

class Catalogo extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'catalogo';
    protected static $columnasDB = ['id', 'nombre'];

    public $nombre;

    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }

    public static function normalizarNombre ($nombre) {
        // Quitar espacios al principio y al final
        $nombre = trim($nombre);

        // Quitar acentos
        $buscar  = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'];
        $reemplazar = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'];
        $nombre = str_replace($buscar, $reemplazar, $nombre);

        // Todo a minusculas
        $nombre = mb_strtolower($nombre, 'UTF-8');

        // Primera letra mayuscula y se envia la cadena ya estandarizada
        return ucfirst($nombre);
    } 
}
