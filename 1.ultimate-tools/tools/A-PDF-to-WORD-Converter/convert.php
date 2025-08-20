<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
        $tempFile = $_FILES['pdfFile']['tmp_name'];
        $outputFile = 'output.docx';

        if (convertPdfToWord($tempFile, $outputFile)) {
            
            header('Content-Type: application/docx');
            header('Content-Disposition: attachment; filename="' . $outputFile . '"');
            readfile($outputFile);

            
            unlink($tempFile);
            unlink($outputFile);

            exit;
        } else {
            echo 'Conversion failed.';
        }
    } else {
        echo 'Error uploading the PDF file.';
    }
}

function convertPdfToWord($pdfPath, $outputPath)
{

    return copy($pdfPath, $outputPath);
}

?>