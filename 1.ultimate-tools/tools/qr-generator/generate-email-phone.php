<?php
require 'vendor/autoload.php'; // Include Composer QR code lib (endroid/qr-code)

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$type = $_POST['type']; // email or phone
$data = $_POST['data'];

if (!$data || !in_array($type, ['email', 'phone'])) {
    die("Invalid input.");
}

$prefix = $type === 'email' ? 'e_' : 'p_';
$folder = $type === 'email' ? 'email' : 'phone';

$content = $type === 'email' ? "mailto:$data" : "tel:$data";

// Auto increment filename
$path = __DIR__ . "/$folder/";
$files = glob($path . "$prefix*.png");
$max = 100;
foreach ($files as $file) {
    $num = (int)filter_var(basename($file), FILTER_SANITIZE_NUMBER_INT);
    if ($num > $max) $max = $num;
}
$nextNum = $max + 1;
$filename = "$prefix$nextNum.png";
$fullPath = $path . $filename;

// Generate QR and save
$qr = QrCode::create($content);
$writer = new PngWriter();
$result = $writer->write($qr);
file_put_contents($fullPath, $result->getString());

echo json_encode(["status" => "success", "file" => "$folder/$filename"]);
?>
