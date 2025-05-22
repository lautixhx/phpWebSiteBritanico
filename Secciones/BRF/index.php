<?php
// secciones/Farmacia/index.php

$section = "BRF";
$allowedExtensions = ['pdf', 'docx'];
// Escanear el directorio actual y filtrar sólo archivos válidos
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
          <!-- Botón para modificar -->
          <a href="../../modificar_instructivo.php?seccion=<?php echo urlencode($section); ?>&archivo=<?php echo urlencode($file); ?>" 
             class="btn btn-sm btn-warning">
             <i class="bi bi-pencil"></i> <!-- Ícono de lápiz -->
          </a>

          <!-- Formulario para eliminar -->
          <form action="../../server.php" method="POST" class="d-inline">
            <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($section); ?>">
            <input type="hidden" name="archivo_a_borrar" value="<?php echo htmlspecialchars($file); ?>">
            <button type="submit" name="borrar_instructivo" class="btn btn-sm btn-danger" 
                    onclick="return confirm('¿Está seguro de eliminar este instructivo?');">
              <i class="bi bi-trash"></i> <!-- Ícono de basura -->
            </button>
          </form>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>

  
  <!-- Formulario para subir un nuevo instructivo -->
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