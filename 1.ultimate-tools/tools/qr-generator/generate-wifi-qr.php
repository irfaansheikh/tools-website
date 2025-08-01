<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ssid'], $_POST['password'], $_POST['encryption'])) {
    $ssid = trim($_POST['ssid']);
    $password = trim($_POST['password']);
    $encryption = trim($_POST['encryption']);

    if ($ssid) {
        require_once __DIR__ . '/../../qrlib/qrlib.php';

        $wifiData = "WIFI:T:$encryption;S:$ssid;P:$password;;";

        $dir = __DIR__ . '/qrcodes/wifi/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $id = uniqid('w_');
        $filename = $dir . $id . '.png';
        $webPath = './qrcodes/wifi/' . $id . '.png';

        QRcode::png($wifiData, $filename, QR_ECLEVEL_L, 4);

        header("Location: wifi-qr.html?img=" . urlencode($webPath));
        exit;
    } else {
        echo "SSID is required.";
    }
} else {
    echo "Invalid request.";
}
