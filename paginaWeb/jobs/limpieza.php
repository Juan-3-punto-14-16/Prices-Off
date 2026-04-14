<?php
require_once __DIR__ . '/../includes/app.php';
use Model\RegistroProducto;
use Model\Catalogo;
use Model\Ubicacion;

$archivoMemoria = __DIR__ . '/memoria_limpieza.txt';
$ultimaLimpieza = file_exists($archivoMemoria) ? trim(file_get_contents($archivoMemoria)) : '';
while (true) {
    $hoy = date('Y-m-d');

    if ($ultimaLimpieza !== $hoy) {
        echo "[" . date('Y-m-d H:i:s') . "]: Iniciando limpieza de registros con más de 15 días...\n";
        try {
            $borrados = RegistroProducto::eliminarRegistrosAntiguos();
            echo "Se eliminaron $borrados productos caducados.\n";

            $borrados = Catalogo::eliminarHuerfanos();
            echo "Se limpiaron $borrados nombres de catálogo huérfanos.\n";

            $borrados = Ubicacion::eliminarHuerfanos();
            echo "Se limpiaron $borrados ubicaciones huérfanas.\n\n";

            $ultimaLimpieza = $hoy;
            file_put_contents($archivoMemoria, $hoy);
        } catch (\Exception $e) {
            echo "Error en la base de datos: " . $e->getMessage() . "\n\n";
        }
    }

    // Cada hora vuelve a revisar si ya cambio de día
    sleep(3600);
}