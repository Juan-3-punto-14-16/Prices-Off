<?php
require_once __DIR__ . '/../includes/app.php';

use Controllers\APIController;
use Controllers\PaginasController;
use Controllers\ProductoController;
use MVC\Router;

$router = new Router();

// Productos
$router->get('/', [ProductoController::class, 'index']);
$router->get('/agregar', [ProductoController::class, 'agregar']);

// Nosotros
$router->get('/nosotros', [PaginasController::class, 'nosotros']);

// API
$router->get('/api/autocompletar', [APIController::class, 'autocompletar']);
$router->get('/api/buscar', [APIController::class, 'buscar']);
$router->post('/api/escanear', [APIController::class, 'escanear']);

// Es de tipo POST para evitar que viajen las coordenadas en la URL
$router->post('/api/direccion', [APIController::class, 'obtenerDireccion']);

$router->post('/api/guardar', [APIController::class, 'guardar']);
$router->post('/api/votos', [APIController::class, 'registrarVoto']);


// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
