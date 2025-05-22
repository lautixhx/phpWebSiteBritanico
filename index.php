<?php
// Ruta de las secciones
$directorio = 'secciones';

// Escanear todas las carpetas dentro de "secciones/"
$secciones = array_filter(glob($directorio . '/*'), 'is_dir');

// Estructura base del index.php para cada sección
$template = <<<'PHP'
<?php
$section = basename(__DIR__);
$allowedExtensions = ['pdf', 'docx'];
$files = array_filter(scandir(__DIR__), function($file) use ($allowedExtensions) {
    if ($file === '.' || $file === '..' || $file === 'index.php') return false;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($ext, $allowedExtensions);
});
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo $section; ?> - Instructivos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>
<body class="container py-4">
  <h2 class="mb-4">Instructivos de <?php echo $section; ?></h2>
  <ul class="list-group">
    <?php foreach ($files as $file): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center">
        <span>
          <a href="../../descargar.php?seccion=<?php echo urlencode($section); ?>&archivo=<?php echo urlencode($file); ?>" 
             class="text-decoration-none">
            <?php echo htmlspecialchars($file); ?>
          </a>
        </span>
        <span>
          <a href="../../modificar_instructivo.php?seccion=<?php echo urlencode($section); ?>&archivo=<?php echo urlencode($file); ?>" 
             class="btn btn-sm btn-warning">
             <i class="bi bi-pencil"></i>
          </a>
          <form action="../../server.php" method="POST" class="d-inline">
            <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($section); ?>">
            <input type="hidden" name="archivo_a_borrar" value="<?php echo htmlspecialchars($file); ?>">
            <button type="submit" name="borrar_instructivo" class="btn btn-sm btn-danger" 
                    onclick="return confirm('¿Está seguro de eliminar este instructivo?');">
              <i class="bi bi-trash"></i>
            </button>
          </form>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>
  <form action="../../server.php" method="POST" enctype="multipart/form-data" class="mt-4">
    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($section); ?>">
    <div class="mb-3">
      <label for="archivo" class="form-label">Seleccionar archivo (DOCX/PDF):</label>
      <input type="file" name="archivo" id="archivo" accept=".pdf,.docx" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre para mostrar:</label>
      <input type="text" name="nombre" id="nombre" class="form-control" required>
    </div>
    <button type="submit" name="subir_instructivo" class="btn btn-primary">Subir</button>
  </form>
</body>
</html>
PHP;

// Verificar cada carpeta dentro de "secciones/" y crear index.php si falta
foreach ($secciones as $seccion) {
    $indexPath = "$seccion/index.php";
    if (!file_exists($indexPath)) {
        file_put_contents($indexPath, $template);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Instructivos</title>

  <!-- Favicons -->
  <link rel="icon" type="image/x-icon" href="images/favicon.ico">
  <link rel="icon" type="image/png" sizes="96x96" href="images/favicon-96x96.png">
  <link rel="icon" type="image/svg+xml" href="images/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
  <link rel="manifest" href="images/site.webmanifest">

  <!-- CSS -->
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  <style>
    /* Ajustes para evitar recortes */
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .row {
        flex: 1;
        display: flex;
        overflow: hidden;
    }

    .sidebar {
        height: 100vh;
        overflow-y: auto;
        padding: 15px;
    }

    .content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    iframe {
        flex-grow: 1;
        height: 100%;
        border: none;
    }
  </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
      
      <!-- SIDEBAR -->
      <nav class="col-md-4 col-lg-3 bg-dark text-white sidebar">
        <h2 class="text-center mt-3">Secciones</h2>

        <!-- Buscador Global -->
        <form action="buscar.php" method="GET" class="p-2">
            <input type="text" name="q" class="form-control" placeholder="Buscar instructivo..." required>
            <button type="submit" class="btn btn-primary mt-2 w-100">Buscar</button>
        </form>
        
        <ul class="nav flex-column">
            <?php foreach ($secciones as $seccion) : 
                $nombreSeccion = basename($seccion); 
            ?>
            <li class="nav-item d-flex align-items-center justify-content-between">
              <div>
                <a class="nav-link text-white" href="secciones/<?php echo $nombreSeccion; ?>/index.php" target="content">
                    <?php echo $nombreSeccion; ?>
                </a>
              </div>
              <div>
                <a href="modificar_seccion.php?seccion=<?php echo urlencode($nombreSeccion); ?>" 
                   class="btn btn-sm btn-warning">
                   <i class="bi bi-pencil"></i>
                </a>
                <form action="server.php" method="POST" class="d-inline">
                    <input type="hidden" name="section_name" value="<?php echo htmlspecialchars($nombreSeccion); ?>">
                    <button type="submit" name="delete_section" class="btn btn-sm btn-danger"
                            onclick="return confirm('¿Está seguro de eliminar esta sección?');">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
              </div>
            </li>
            <?php endforeach; ?>
          <li class="nav-item mt-3">
            <form action="server.php" method="POST" class="p-2 d-flex">
              <input type="text" name="new_section" class="form-control me-2" placeholder="Nueva sección" required>
              <button type="submit" name="add_section" class="btn btn-success">Agregar</button>
            </form>
          </li> 
        </ul>
      </nav>

      <main class="col-md-8 col-lg-9 px-md-4 content">
        <iframe name="content" src="" class="w-100"></iframe>
      </main>
      
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
