<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prices-Off</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="build/css/app.css">
    <?php echo $estilos ?? ''; ?>
</head>
<body>
    <header class="header">
    <div class="header_logo">
        <a href="/"><img src="build/img/logo.jpg" alt="Logo Prices-Off"></a>
    </div>

    <div class="header_titulo">
        <h1>PRICES-OFF</h1>
    </div>

    <div class="header_enlace_nosotros">
        <a href="/nosotros">Sobre<br>Nosotros</a>
    </div>
    </header>

    <nav class=nav_principal>
    <a href="/">BUSCAR PRODUCTO</a> | <a href="/agregar">AGREGAR PRODUCTO</a>
    </nav>

    <main class="contenido_principal">
    <?php echo $contenido; ?>
    </main>

    <footer class="footer">
        <p>Todos los derechos reservados SA de CV.</p> 
    </footer>

    <?php echo $script ?? ''; ?>
    
</body>
</html>