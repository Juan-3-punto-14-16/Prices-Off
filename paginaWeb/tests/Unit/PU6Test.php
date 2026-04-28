<?php
use Model\Catalogo;

it('sanitiza el nombre del producto para prevenir ataques XSS según el requerimiento PM_6', function () {
    // PASO 1: Instanciar un objeto del modelo Catalogo pasando en el atributo nombre <script>alert('hack')</script>Leche
    $producto1 = new Catalogo(['nombre' => "<script>alert('hack')</script>Leche"]);

    // PASO 2: Instanciar un segundo objeto pasando en el nombre el string <b>Frijoles</b>
    $producto2 = new Catalogo(['nombre' => "<b>Frijoles</b>"]);

    // PASO 3: Instanciar un tercer objeto pasando en el nombre el string Huevo San Juan
    $producto3 = new Catalogo(['nombre' => "Huevo San Juan"]);

    // PASO 4: Inspeccionar el valor interno del atributo nombre de cada uno de los objetos
    expect($producto1->nombre)->toBe("&lt;script&gt;alert(&#039;hack&#039;)&lt;/script&gt;leche");
    expect($producto2->nombre)->toBe("&lt;b&gt;frijoles&lt;/b&gt;");
    expect($producto3->nombre)->toBe("Huevo San Juan");
});
