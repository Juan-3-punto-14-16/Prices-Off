<?php
namespace Controllers;
use MVC\Router;

class ProductoController {
    public static function index(Router $router) {
        

        $router->render('producto/index',
        [

        ]);
    }

    public static function agregar(Router $router) {
        

        $router->render('producto/agregar',
        [

        ]);
    }
}
