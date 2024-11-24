<?php

$upload_dir = 'uploads/';


if (isset($_GET['file'])) {
    $file_name = basename($_GET['file']); 
    $file_path = $upload_dir . $file_name;

    // Verifica si el archivo existe
    if (file_exists($file_path)) {
        // Configura  para forzar la descarga
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        // Limpia el búfer de salida
        ob_clean();
        flush();
        
        // Lee el archivo y envíalo al navegador
        readfile($file_path);
        exit;
    } else {
        echo "El archivo no existe.";
    }
} else {
    echo "Archivo no especificado.";
}
?>
