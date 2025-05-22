<?php
// Verificamos que el parámetro GET 'seccion' exista
if (!isset($_GET['seccion']) || empty($_GET['seccion'])) {
    die("Error: No se ha especificado ninguna sección.");
}

$seccion = basename($_GET['seccion']); // Sanitiza el nombre de la sección
$rutaSeccion = __DIR__ . "/secciones/$seccion"; // Ruta completa de la carpeta

// Verifica si la carpeta existe
if (!is_dir($rutaSeccion)) {
    die("Error: La sección '$seccion' no existe.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Modificar Sección</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
  
  <h2 class="mb-4">Modificar Sección</h2>
  <p>Estás modificando la sección: <strong><?php echo htmlspecialchars($seccion); ?></strong></p>

  <!-- Formulario para renombrar la sección -->
  <form action="server.php" method="POST">
    <input type="hidden" name="seccion_actual" value="<?php echo htmlspecialchars($seccion); ?>">

    <div class="mb-3">
      <label for="nuevo_nombre" class="form-label">Nuevo nombre de la sección:</label>
      <input 
        type="text" 
        name="nuevo_nombre" 
        id="nuevo_nombre" 
        class="form-control" 
        placeholder="Ejemplo: Administración" 
        required>
    </div>

    <button type="submit" name="modificar_seccion" class="btn btn-primary">
      Guardar cambios
    </button>
  </form>

</body>
</html>
