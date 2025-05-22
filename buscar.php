<?php
if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    die("Por favor, introduce un término de búsqueda.");
}

$query = trim($_GET['q']);
$queryNormalizado = normalizarTexto($query); // Normaliza la búsqueda
$directorioSecciones = __DIR__ . "/secciones";
$resultados = [];

// Función para quitar tildes y convertir a minúsculas
function normalizarTexto($texto) {
    $texto = strtolower($texto);
    $tildes = ['á', 'é', 'í', 'ó', 'ú', 'ü'];
    $sinTildes = ['a', 'e', 'i', 'o', 'u', 'u'];
    return str_replace($tildes, $sinTildes, $texto);
}

// Recorremos todas las secciones
foreach (glob("$directorioSecciones/*", GLOB_ONLYDIR) as $seccion) {
    $nombreSeccion = basename($seccion);
    
    // Recorremos los archivos dentro de cada sección
    foreach (glob("$seccion/*.{pdf,docx}", GLOB_BRACE) as $archivo) {
        $nombreArchivo = basename($archivo);
        $nombreNormalizado = normalizarTexto($nombreArchivo); // Normaliza el nombre del archivo

        // Si el nombre del archivo coincide con la búsqueda, lo guardamos
        if (strpos($nombreNormalizado, $queryNormalizado) !== false) {
            $resultados[] = [
                'seccion' => $nombreSeccion,
                'archivo' => $nombreArchivo,
                'ruta' => "secciones/$nombreSeccion/$nombreArchivo"
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resultados de Búsqueda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2 class="mb-4">Resultados de Búsqueda</h2>

    <?php if (empty($resultados)): ?>
        <p>No se encontraron instructivos que coincidan con "<strong><?php echo htmlspecialchars($query); ?></strong>".</p>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($resultados as $resultado): ?>
                <li class="list-group-item">
                    <a href="<?php echo $resultado['ruta']; ?>" class="text-decoration-none" target="_blank">
                        <?php echo htmlspecialchars($resultado['archivo']); ?>
                    </a>
                    <span class="text-muted">(Sección: <?php echo htmlspecialchars($resultado['seccion']); ?>)</span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <a href="index.php" class="btn btn-secondary mt-3">Volver</a>
</body>
</html>
                
