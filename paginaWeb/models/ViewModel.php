<?php
namespace Model;

class ViewModel extends ActiveRecord {
    // Base de datos
    protected static $tabla = 'registroproducto';
    protected static $columnasDB = [
        'id', 'publicado', 'preciounitario', 'unidadmedida',
        'nombre',
        'latitud', 'longitud', 'tienda', 'direccion',
        'votospositivos', 'votosnegativos'
    ];

    public $publicado;
    public $preciounitario;
    public $unidadmedida;
    public $nombre;
    public $latitud;
    public $longitud;
    public $tienda;
    public $direccion;
    public $votospositivos;
    public $votosnegativos;

    // El constructor no se utiliza como tal, pero bueno, es necesario...
    public function __construct($args = []){
        $this->id = $args['id'] ?? null;
        $this->publicado = $args['publicado'] ?? '';
        $this->preciounitario = $args['preciounitario'] ?? '';
        $this->unidadmedida = $args['unidadmedida'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->latitud = $args['latitud'] ?? '';
        $this->longitud = $args['longitud'] ?? '';
        $this->tienda = $args['tienda'] ?? '';
        $this->direccion = $args['direccion'] ?? '';
        $this->votospositivos = $args['votospositivos'] ?? '';
        $this->votosnegativos = $args['votosnegativos'] ?? '';
    }

    // ESTA CONSULTA ESTA OPTIMIZADA PARA POSTGRES
    public static function buscarProductos ($nombre) {
        $query = "
            SELECT
                rp.id,
                TO_CHAR(rp.fecharegistro, 'DD/MM/YYYY') AS publicado,
                ROUND((rp.precio / rp.cantidad),2) AS preciounitario,
                rp.unidadmedida,
                c.nombre,
                u.latitud,
                u.longitud,
                u.tienda,
                u.direccion,
                COUNT(*) FILTER (WHERE v.voto = TRUE) AS votospositivos,
                COUNT(*) FILTER (WHERE v.voto = FALSE) AS votosnegativos
            FROM registroproducto rp
                JOIN catalogo c ON rp.idcatalogo = c.id
                JOIN ubicacion u ON rp.idubicacion = u.id
                LEFT JOIN votos v ON rp.id = v.idregistroproducto
            WHERE c.nombre = ?
            GROUP BY rp.id, c.nombre, u.tienda, u.direccion, u.latitud, u.longitud
            ORDER BY preciounitario
        ";

        return self::sql($query, [$nombre]);
    }
}
