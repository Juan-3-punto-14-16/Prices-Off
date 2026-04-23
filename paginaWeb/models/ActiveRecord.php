<?php
namespace Model;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    public $id;

    protected const SELECT_BASE = "SELECT * FROM ";

    public static function iniciarTransaccion() {
        self::$db->beginTransaction();
    }

    public static function confirmarTransaccion() {
        self::$db->commit();
    }

    public static function revertirTransaccion() {
        self::$db->rollBack();
    }

    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function obtenerObjetos($query, $params = []) {
        // Consultar la base de datos
        $stmt = self::$db->prepare($query);
        $stmt->execute($params);

        // Iterar los resultados
        $array = [];
        while($registro = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $stmt->closeCursor();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Sincroniza POST con Objetos en memoria
    public function sincronizar($args=[]) {
        foreach($args as $key => $value) {
            if(property_exists($this, $key) && !is_null($value)) {
                $this->$key = $value;
            }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $resultado = '';
        if(!is_null($this->id)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all() {
        $query = self::SELECT_BASE . static::$tabla;
        return self::obtenerObjetos($query);
    }

    // Busca un registro por su id
    public static function find($id) {
        $query = self::SELECT_BASE . static::$tabla  ." WHERE id = ? LIMIT 1";
        $resultado = self::obtenerObjetos($query, [$id]);
        return array_shift( $resultado ) ;
    }

    // Busca un registro por columna y su valor
    public static function where($col, $valor) {
        $query = self::SELECT_BASE . static::$tabla  ." WHERE {$col} = ? LIMIT 1";
        $resultado = self::obtenerObjetos($query, [$valor]);
        return array_shift( $resultado ) ;
    }

    // Consulta Plana de SQL (Utilizar cuando los métodos del modelo no son suficientes)
    public static function sql($query, $params = []) {
        return self::obtenerObjetos($query, $params);
    }

    // Obtener Registros Parecidos A
    public static function iLike($col, $valor, $limite = 5) {
        $limite_seguro = (int)$limite;
        $query = self::SELECT_BASE . static::$tabla . " WHERE {$col} ILIKE ? LIMIT {$limite_seguro}";
        $valor = "%$valor%";
        return self::obtenerObjetos($query, [$valor]);
    }

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $limite_seguro = (int)$limite;
        $query = self::SELECT_BASE . static::$tabla . " LIMIT {$limite_seguro}";
        return self::obtenerObjetos($query);
    }

    // Identificar y unir los atributos
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') {
                continue;
            }

            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // crea un nuevo registro
    public function crear() {
        // Obtener los atributos
        $atributos = $this->atributos();

        // Insertar en la base de datos
        $columnas = join(', ', array_keys($atributos));
        $placeholders = join(', ', array_fill(0, count($atributos), '?'));

        $query = " INSERT INTO " . static::$tabla . " ( {$columnas} ) VALUES ( {$placeholders} ) ";
        
        $stmt = self::$db->prepare($query);

        // Resultado de la consulta
        $resultado = $stmt->execute(array_values($atributos));
        return [
            'resultado' =>  $resultado,
            'id' => self::$db->lastInsertId()
        ];
    }

    // Actualizar el registro
    public function actualizar() {
        // Obtener los atributos
        $atributos = $this->atributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach(array_keys($atributos) as $key) {
            $valores[] = "{$key}=?";
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE id = ?";

        $stmt = self::$db->prepare($query);

        $valores_ejecucion = array_values($atributos);
        $valores_ejecucion[] = $this->id;

        // Actualizar BD
        return $stmt->execute($valores_ejecucion);
    }

    // Eliminar un Registro por su ID
    public function eliminar() {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        
        return $stmt->execute([$this->id]);
    }
}
