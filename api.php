<?php
$storageDir = 'uploads/';
if (!is_dir($storageDir)) mkdir($storageDir, 0777, true);

$action = $_GET['action'] ?? '';

// 1. LIST FILES
if ($action == 'list') {
    $files = array_diff(scandir($storageDir), array('.', '..'));
    echo json_encode(array_values($files));
}

// 2. DOWNLOAD SINGLE FILE
if ($action == 'download' && isset($_GET['filename'])) {
    $file = $storageDir . basename($_GET['filename']);
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        readfile($file);
        exit;
    }
}

// 3. DOWNLOAD ALL AS ZIP
if ($action == 'download_all') {
    $zipName = 'all_files.zip';
    $zip = new ZipArchive;
    if ($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        $files = array_diff(scandir($storageDir), array('.', '..'));
        foreach ($files as $file) {
            $zip->addFile($storageDir . $file, $file);
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipName);
        header('Content-Length: ' . filesize($zipName));
        readfile($zipName);
        unlink($zipName); // Delete zip after download
        exit;
    }
}

// 4. UPLOAD / UPDATE
if ($action == 'upload' && !empty($_FILES['file'])) {
    $targetFile = $storageDir . basename($_FILES['file']['name']);
    move_uploaded_file($_FILES['file']['tmp_name'], $targetFile);
    echo json_encode(['status' => 'success']);
}

// 5. DELETE
if ($action == 'delete' && isset($_POST['filename'])) {
    unlink($storageDir . $_POST['filename']);
    echo json_encode(['status' => 'success']);
}
?>