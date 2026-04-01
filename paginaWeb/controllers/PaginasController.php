<?php
namespace Controllers;
use MVC\Router;

class PaginasController {
    public static function nosotros(Router $router) {
        $router->render('paginas/nosotros');
    }
}