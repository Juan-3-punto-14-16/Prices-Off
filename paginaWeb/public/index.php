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

// API's
$router->get('/api/autocompletar', [APIController::class, 'autocompletar']);
$router->get('/api/buscar', [APIController::class, 'buscar']);
$router->post('/api/votos', [APIController::class, 'registrarVoto']);

$router->post('/api/escanear', [APIController::class, 'escanear']);
$router->get('/api/direccion', [APIController::class, 'obtenerDireccion']);
$router->post('/api/guardar', [APIController::class, 'guardar']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
