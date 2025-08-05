<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qrtext'])) {
    $text = trim($_POST['qrtext']);

    if ($text !== '') {
        require_once __DIR__ . '/lib/qrlib/qrlib.php'; // Confirmed working

        // Directory setup
        $dir = __DIR__ . '/qrcodes/text/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate filename: input_date_serial.png
        $sanitizedText = preg_replace('/[^a-zA-Z0-9]/', '_', $text); // Sanitize input
        $date = date('Y-m-d'); // Dynamic date, currently 2025-08-04
        $files = glob($dir . $sanitizedText . '_' . $date . '_*.png');
        $serial = str_pad(count($files) + 1, 3, '0', STR_PAD_LEFT); // e.g., 001
        $filename = $dir . $sanitizedText . '_' . $date . '_' . $serial . '.png';

        // Web path relative to the server root (use port dynamically if needed)
        $port = 8001; // Adjust based on which instance (8000, 8001, 8002)
        $webPath = 'http://localhost:' . $port . '/qrcodes/text/' . $sanitizedText . '_' . $date . '_' . $serial . '.png';

        // Generate QR code
        QRcode::png($text, $filename, QR_ECLEVEL_L, 4);

        // Verify file creation
        if (file_exists($filename)) {
            // Logging to CSV
            $logFile = __DIR__ . '/logs/text-qr-log.csv';
            if (!file_exists($logFile)) {
                file_put_contents($logFile, "Date,Input,Filename\n");
            }
            $logData = [date('Y-m-d H:i:s'), $text, $filename];
            file_put_contents($logFile, implode(",", array_map("addslashes", $logData)) . "\n", FILE_APPEND);

            // Redirect with image path
            header("Location: text-to-qr.php?img=" . urlencode($webPath));
            exit;
        } else {
            echo "Error: QR code file not created at " . $filename;
            exit;
        }
    } else {
        echo "Please enter some text.";
    }
} else {
    header("Location: text-to-qr.php");
    exit;
}
?>