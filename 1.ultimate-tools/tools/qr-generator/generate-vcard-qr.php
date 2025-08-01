<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['phone'], $_POST['email'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    if ($name && $phone && $email) {
        require_once __DIR__ . '/../../qrlib/qrlib.php';

        $vcard = "BEGIN:VCARD\nVERSION:3.0\nFN:$name\nTEL:$phone\nEMAIL:$email\nEND:VCARD";

        $dir = __DIR__ . '/qrcodes/vcard/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $id = uniqid('v_');
        $filename = $dir . $id . '.png';
        $webPath = './qrcodes/vcard/' . $id . '.png';

        QRcode::png($vcard, $filename, QR_ECLEVEL_L, 4);

        header("Location: vcard-qr.html?img=" . urlencode($webPath));
        exit;
    } else {
        echo "All fields are required.";
    }
} else {
    echo "Invalid request.";
}
