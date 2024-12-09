<?php
require_once 'core/dbConfig.php';

if (isset($_GET['file'])) {
    $file_name = urldecode($_GET['file']); 

    echo "Received file name: " . htmlspecialchars($file_name) . "<br>";

    $stmt = $pdo->prepare("SELECT file_path FROM pdf_uploads WHERE pdf_name = :file_name");
    $stmt->bindParam(':file_name', $file_name);
    $stmt->execute();

    $file = $stmt->fetch();

    if ($file) {
        $file_path = $file['file_path'];

        echo "File path from database: " . htmlspecialchars($file_path) . "<br>";

        if (file_exists($file_path)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));

            readfile($file_path);
            exit(); 
        } else {
            echo "File not found on the server. Please check the path.";
        }
    } else {
        echo "Invalid file name. No matching file found in the database.";
    }
} else {
    echo "No file specified.";
}
?>
