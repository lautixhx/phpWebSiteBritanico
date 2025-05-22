<?php
if (isset($_GET['archivo']) && isset($_GET['seccion'])) {
    $seccion = basename($_GET['seccion']); // Sanitiza la sección
    $archivo = urldecode(basename($_GET['archivo'])); // Decodifica espacios y caracteres especiales
    $ruta = __DIR__ . "/secciones/$seccion/$archivo"; // Ruta completa del archivo

    // Verificar si el archivo existe
    if (file_exists($ruta)) {
        // Forzar descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $archivo . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($ruta));
        readfile($ruta);
        exit;
    } else {
        echo "Error: El archivo no existe en la ruta: $ruta";
    }
} else {
    echo "Error: Parámetros inválidos";
}
?>
