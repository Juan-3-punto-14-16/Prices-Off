<?php
namespace Tests;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {
    // Configuraciones globales para que sean compartidas por múltiples pruebas
    // Por ejemplo, la conexión con la BD, variables de entorno, etc.
}
