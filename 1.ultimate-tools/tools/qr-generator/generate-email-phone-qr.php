<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact'])) {
    $input = trim($_POST['contact']);

    if (!empty($input)) {
        require_once __DIR__ . '/../../qrlib/qrlib.php';

        $prefix = strpos($input, '@') !== false ? 'mailto:' : 'tel:';
        $typeFolder = strpos($input, '@') !== false ? 'email' : 'phone';

        $dir = __DIR__ . '/qrcodes/' . $typeFolder . '/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $id = uniqid(($typeFolder === 'email' ? 'e_' : 'p_'));
        $filename = $dir . $id . '.png';
        $webPath = './qrcodes/' . $typeFolder . '/' . $id . '.png';

        QRcode::png($prefix . $input, $filename, QR_ECLEVEL_L, 4);

        header("Location: email-phone-qr.html?img=" . urlencode($webPath));
        exit;
    } else {
        echo "No input provided.";
    }
} else {
    echo "Invalid request.";
}
