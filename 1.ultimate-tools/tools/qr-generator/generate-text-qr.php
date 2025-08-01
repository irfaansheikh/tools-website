<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qrtext'])) {
    $text = trim($_POST['qrtext']);

    if ($text !== '') {
        require_once __DIR__ . '/../../qrlib/qrlib.php';

        // Save image in filesystem
        $dir = __DIR__ . '/qrcodes/text/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $id = uniqid('txt_');
        $filename = $dir . $id . '.png';

        // ✅ This path is used in <img src="..."> and must be correct relative to the SERVER ROOT
        $webPath = '/1.ultimate-tools/tools/qr-generator/qrcodes/text/' . $id . '.png';

        // Generate QR code
        QRcode::png($text, $filename, QR_ECLEVEL_L, 4);

        // ✅ Logging (optional)
        $logFile = __DIR__ . '/logs/text-qr-log.csv';
        $logData = [date('Y-m-d H:i:s'), $text, $filename];
        file_put_contents($logFile, implode(",", array_map("addslashes", $logData)) . PHP_EOL, FILE_APPEND);

        // ✅ Redirect to HTML page with correct public image path
        header("Location: text-qr.html?img=" . urlencode($webPath));
        exit;
    } else {
        echo "Please enter some text.";
    }
} else {
    echo "Invalid request.";
}
