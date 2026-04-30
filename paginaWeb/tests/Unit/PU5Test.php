<?php
use Controllers\APIController;

it('extrae y limpia correctamente las propiedades de un objeto Entity simulado según el requerimiento PM_5', function () {
    $extraerLineItem = new \ReflectionMethod(APIController::class, 'extraerLineItem');

    // PASO 1: Instanciar un objeto Mock de la clase entity
    $mockEntity = new class {
        public function getProperties() {
            return [
                new class {
                    public function getType() { return 'line_item/description'; }
                    public function getMentionText() { return "LECHE LALA\n"; }
                },
                new class {
                    public function getType() { return 'line_item/amount'; }
                    public function getMentionText() { return "$28.50"; }
                },
                new class {
                    public function getType() { return 'line_item/unit'; }
                    public function getMentionText() { return "LTS"; }
                }
            ];
        }
    };

    // PASO 2: Pasar el objeto simulado al método del controlador
    $resultado = $extraerLineItem->invoke(null, $mockEntity);
    expect($resultado)->toBeArray()
        ->and($resultado['nombre'])->toBe('LECHE LALA')
        ->and($resultado['precio'])->toBe(28.5)
        ->and($resultado['unidadmedida'])->toBe('litro');

    // PASO 3: Instanciar un segundo objeto que omita la propiedad del precio, y pasarlo al método
    $mockEntity = new class {
        public function getProperties() {
            return [
                new class {
                    public function getType() { return 'line_item/description'; }
                    public function getMentionText() { return " GALLETAS MARIAS       "; }
                },
                new class {
                    public function getType() { return 'line_item/unit'; }
                    public function getMentionText() { return "KG"; }
                }
            ];
        }
    };

    $resultado = $extraerLineItem->invoke(null, $mockEntity);
    expect($resultado)->toBeArray()
        ->and($resultado['nombre'])->toBe('GALLETAS MARIAS')
        ->and($resultado['precio'])->toBe(0)
        ->and($resultado['unidadmedida'])->toBe('kg');
});
