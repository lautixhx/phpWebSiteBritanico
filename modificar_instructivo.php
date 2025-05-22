<?php
// modificar_instructivo.php

// Verificamos que lleguen los parámetros GET
if (!isset($_GET['seccion']) || !isset($_GET['archivo'])) {
    echo "Faltan parámetros (sección o archivo).";
    exit;
}

$seccion = $_GET['seccion'];
$archivo = $_GET['archivo'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Modificar Instructivo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  <h2>Modificar Instructivo</h2>

  <p>Estás modificando el archivo: <strong><?php echo htmlspecialchars($archivo); ?></strong> de la sección <strong><?php echo htmlspecialchars($seccion); ?></strong>.</p>

  <form action="server.php" method="POST" enctype="multipart/form-data">
    <!-- Sección y archivo actual -->
    <input type="hidden" name="seccion" value="<?php echo htmlspecialchars($seccion); ?>">
    <input type="hidden" name="archivo_actual" value="<?php echo htmlspecialchars($archivo); ?>">

    <!-- Campo para renombrar -->
    <div class="mb-3">
      <label for="nuevo_nombre" class="form-label">Nuevo nombre (sin extensión):</label>
      <input 
        type="text" 
        name="nuevo_nombre" 
        id="nuevo_nombre" 
        class="form-control" 
        placeholder="Ej: Manual de Usuario"
      >
      <small class="text-muted">
        Deja este campo en blanco si no deseas cambiar el nombre.
      </small>
    </div>

    <!-- Campo para reemplazar archivo -->
    <div class="mb-3">
      <label for="nuevo_archivo" class="form-label">
        Reemplazar archivo (opcional, solo PDF/DOCX):
      </label>
      <input 
        type="file" 
        name="nuevo_archivo" 
        id="nuevo_archivo" 
        accept=".pdf,.docx" 
        class="form-control"
      >
      <small class="text-muted">
        Deja este campo en blanco si no deseas reemplazar el archivo.
      </small>
    </div>

    <button type="submit" name="modificar_instructivo" class="btn btn-primary">
      Guardar cambios
    </button>
  </form>
</body>
</html>
