<?php
use Tests\TestCase;

require_once __DIR__ . '/../includes/app.php';
pest()->extend(TestCase::class)->in('Unit', 'Integration');
