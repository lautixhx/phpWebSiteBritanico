<?php
// server.php

// --------------------------
// 1. Subir instructivo
// --------------------------
if (isset($_POST['subir_instructivo'])) {
    $seccion = $_POST['seccion'];
    $nombre = $_POST['nombre']; // Se usará para renombrar el archivo
    $archivo = $_FILES['archivo'];

    // Validar extensión permitida
    $allowedExtensions = ['pdf', 'docx'];
    $fileExtension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "<script>
                alert('Error: Solo se permiten archivos PDF o DOCX. Por favor selecciona un archivo válido.');
                window.history.back();
              </script>";
        exit;
    }

    // Construir la ruta destino
    $targetDir = __DIR__ . "/secciones/$seccion/";
    if (!is_dir($targetDir)) {
        echo "<script>
                alert('Error: La sección especificada no existe.');
                window.history.back();
              </script>";
        exit;
    }

    // Si se proporciona un nombre, usamos ese nombre + extensión
    $nuevoNombreArchivo = $nombre . "." . $fileExtension;
    $targetFile = $targetDir . $nuevoNombreArchivo;
    if (!move_uploaded_file($archivo['tmp_name'], $targetFile)) {
        echo "<script>
                alert('Error al mover el archivo. Verifica permisos.');
                window.history.back();
              </script>";
        exit;
    }

    // Redirigir a la sección (index.php dinámico)
    header("Location: secciones/$seccion/index.php");
    exit;
}

// --------------------------
// 2. Borrar instructivo
// --------------------------
if (isset($_POST['borrar_instructivo'])) {
    $seccion = $_POST['seccion'];
    $archivoBorrar = $_POST['archivo_a_borrar'];

    $targetDir = __DIR__ . "/secciones/$seccion/";
    $filePath = $targetDir . $archivoBorrar;

    if (file_exists($filePath)) {
        unlink($filePath);
    }
    header("Location: secciones/$seccion/index.php");
    exit;
}

// --------------------------
// 3. Agregar una nueva sección
// --------------------------
if (isset($_POST['add_section'])) {
    $newSection = trim($_POST['new_section']);

    // Validar que no esté vacío
    if (empty($newSection)) {
        echo "<script>
                alert('Error: El nombre de la sección no puede estar vacío.');
                window.history.back();
              </script>";
        exit;
    }

    // Definir la ruta de la nueva sección
    $sectionDir = __DIR__ . "/secciones/$newSection";

    // Verificar si la carpeta ya existe
    if (is_dir($sectionDir)) {
        echo "<script>
                alert('Error: La sección ya existe.');
                window.history.back();
              </script>";
        exit;
    }

    // Crear la carpeta de la sección
    mkdir($sectionDir, 0777, true);

    // Crear el archivo index.php con la estructura deseada
    $indexFile = $sectionDir . "/index.php";
    $baseContent = "<?php
\$section = \"$newSection\";
\$allowedExtensions = ['pdf', 'docx'];
\$files = array_filter(scandir(__DIR__), function(\$file) use (\$allowedExtensions) {
    if (\$file === '.' || \$file === '..' || \$file === 'index.php') return false;
    \$ext = strtolower(pathinfo(\$file, PATHINFO_EXTENSION));
    return in_array(\$ext, \$allowedExtensions);
});
?>
<!DOCTYPE html>
<html lang=\"es\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <title><?php echo \$section; ?> - Instructivos</title>
  <link href=\"https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css\" rel=\"stylesheet\">
  <link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css\">
</head>
<body class=\"container py-4\">
  <h2 class=\"mb-4\">Instructivos de <?php echo \$section; ?></h2>
  <ul class=\"list-group\">
    <?php foreach (\$files as \$file): ?>
      <li class=\"list-group-item d-flex justify-content-between align-items-center\">
        <span>
          <a href=\"../../descargar.php?seccion=<?php echo urlencode(\$section); ?>&archivo=<?php echo urlencode(\$file); ?>\" 
             class=\"text-decoration-none\">
            <?php echo htmlspecialchars(\$file); ?>
          </a>
        </span>

        <span>
          <a href=\"../../modificar_instructivo.php?seccion=<?php echo urlencode(\$section); ?>&archivo=<?php echo urlencode(\$file); ?>\" 
             class=\"btn btn-sm btn-warning\">
             <i class=\"bi bi-pencil\"></i>
          </a>
          <form action=\"../../server.php\" method=\"POST\" class=\"d-inline\">
            <input type=\"hidden\" name=\"seccion\" value=\"<?php echo htmlspecialchars(\$section); ?>\">
            <input type=\"hidden\" name=\"archivo_a_borrar\" value=\"<?php echo htmlspecialchars(\$file); ?>\">
            <button type=\"submit\" name=\"borrar_instructivo\" class=\"btn btn-sm btn-danger\" 
                    onclick=\"return confirm('¿Está seguro de eliminar este instructivo?');\">
              <i class=\"bi bi-trash\"></i>
            </button>
          </form>
        </span>
      </li>
    <?php endforeach; ?>
  </ul>

  <form action=\"../../server.php\" method=\"POST\" enctype=\"multipart/form-data\" class=\"mt-4\">
    <input type=\"hidden\" name=\"seccion\" value=\"<?php echo htmlspecialchars(\$section); ?>\">
    <div class=\"mb-3\">
      <label for=\"archivo\" class=\"form-label\">Seleccionar archivo (DOCX/PDF):</label>
      <input type=\"file\" name=\"archivo\" id=\"archivo\" accept=\".pdf,.docx\" class=\"form-control\" required>
    </div>
    <div class=\"mb-3\">
      <label for=\"nombre\" class=\"form-label\">Nombre para mostrar:</label>
      <input type=\"text\" name=\"nombre\" id=\"nombre\" class=\"form-control\" required>
    </div>
    <button type=\"submit\" name=\"subir_instructivo\" class=\"btn btn-primary\">Subir</button>
  </form>
</body>
</html>";

    // Guardar el `index.php` en la nueva sección
    file_put_contents($indexFile, $baseContent);

    // Redirigir a la página principal
    header("Location: index.php");
    exit;
}


// --------------------------
// 4. Borrar sección
// --------------------------
if (isset($_POST['delete_section'])) {
    $sectionToDelete = $_POST['section_name'];
    $sectionDir = __DIR__ . "/secciones/$sectionToDelete";

    if (is_dir($sectionDir)) {
        $it = new RecursiveDirectoryIterator($sectionDir, RecursiveDirectoryIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($sectionDir);
    }
    header("Location: index.php");
    exit;
}

// --------------------------
// 5. Modificar seccion
// --------------------------
if (isset($_POST['modificar_seccion'])) {
    $seccionActual = basename($_POST['seccion_actual']); // Sección actual
    $nuevoNombre = trim($_POST['nuevo_nombre']); // Nuevo nombre

    $rutaActual = __DIR__ . "/secciones/$seccionActual";
    $nuevaRuta = __DIR__ . "/secciones/$nuevoNombre";

    // Verifica que la sección exista antes de renombrarla
    if (!is_dir($rutaActual)) {
        echo "<script>
                alert('Error: La sección no existe.');
                window.history.back();
              </script>";
        exit;
    }

    // Verifica si ya existe otra carpeta con el mismo nombre
    if (is_dir($nuevaRuta)) {
        echo "<script>
                alert('Error: Ya existe una sección con ese nombre.');
                window.history.back();
              </script>";
        exit;
    }

    // Renombra la carpeta
    if (rename($rutaActual, $nuevaRuta)) {
        echo "<script>
                alert('Sección renombrada con éxito.');
                window.location.href='index.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Error al renombrar la sección.');
                window.history.back();
              </script>";
        exit;
    }
}

// --------------------------
// 6. Modificar instructivo
// --------------------------
if (isset($_POST['modificar_instructivo'])) {
    $seccion = basename($_POST['seccion']); // Sanitizar la sección
    $archivo_actual = basename($_POST['archivo_actual']); // Sanitizar el archivo actual
    $nuevo_nombre = trim($_POST['nuevo_nombre']); // Nuevo nombre sin extensión

    $targetDir = __DIR__ . "/secciones/$seccion/";
    $oldFilePath = $targetDir . $archivo_actual;

    // 1️⃣ Verifica que el archivo exista antes de modificarlo
    if (!file_exists($oldFilePath)) {
        echo "<script>
                alert('El archivo que intentas modificar no existe.');
                window.history.back();
              </script>";
        exit;
    }

    // 2️⃣ Verifica si se subió un nuevo archivo para reemplazarlo
    if (!empty($_FILES['nuevo_archivo']['name'])) {
        $archivoSubido = $_FILES['nuevo_archivo'];
        $fileExtension = strtolower(pathinfo($archivoSubido['name'], PATHINFO_EXTENSION));

        $allowedExtensions = ['pdf', 'docx'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            echo "<script>
                    alert('Error: Solo se permiten archivos PDF o DOCX.');
                    window.history.back();
                  </script>";
            exit;
        }

        $nuevoNombreArchivo = $nuevo_nombre ? $nuevo_nombre . "." . $fileExtension : $archivoSubido['name'];
        $newFilePath = $targetDir . $nuevoNombreArchivo;

        // Mueve el archivo subido
        if (!move_uploaded_file($archivoSubido['tmp_name'], $newFilePath)) {
            echo "<script>
                    alert('Error al subir el nuevo archivo.');
                    window.history.back();
                  </script>";
            exit;
        }

        // Si el nuevo archivo es diferente al anterior, borra el viejo
        if ($archivo_actual !== $nuevoNombreArchivo) {
            unlink($oldFilePath);
        }

    } elseif (!empty($nuevo_nombre)) { 
        // 3️⃣ Si solo se quiere renombrar sin subir un archivo nuevo
        $fileExtension = strtolower(pathinfo($archivo_actual, PATHINFO_EXTENSION));
        $nuevoNombreArchivo = $nuevo_nombre . "." . $fileExtension;
        $newFilePath = $targetDir . $nuevoNombreArchivo;

        if (file_exists($newFilePath)) {
            echo "<script>
                    alert('Ya existe un archivo con este nombre.');
                    window.history.back();
                  </script>";
            exit;
        }

        if (!rename($oldFilePath, $newFilePath)) {
            echo "<script>
                    alert('Error al renombrar el archivo.');
                    window.history.back();
                  </script>";
            exit;
        }
    }

    // 4️⃣ Redirigir de vuelta a la sección del instructivo
    header("Location: secciones/$seccion/index.php");
    exit;
}

?>


