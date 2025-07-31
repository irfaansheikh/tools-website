<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['url'])) {
    $url = trim($_POST['url']);
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        require_once __DIR__ . '/../../qrlib/qrlib.php'; // QR Code library (download from https://sourceforge.net/projects/phpqrcode/)
        
        // Create folders if not exist
        $dir = __DIR__ . '/qrcodes/website/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate filename
        $id = uniqid('w_');
        $filename = $dir . $id . '.png';
        $webPath = '../../tools/qr-generator/qrcodes/website/' . $id . '.png';


        // Generate QR
        QRcode::png($url, $filename, QR_ECLEVEL_L, 4);

        // Redirect back with image path
        header("Location: website-qr.html?img=" . $webPath);
        exit;
    } else {
        echo "Invalid URL.";
    }
} else {
    echo "No URL received.";
}
?>
