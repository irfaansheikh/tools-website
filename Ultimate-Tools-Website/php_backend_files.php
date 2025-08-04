<?php
// api/log-usage.php - Usage logging endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../logs/';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// Prepare log data
$logData = [
    date('Y-m-d H:i:s'),
    $input['action'] ?? '',
    $input['tool'] ?? '',
    $input['details'] ?? '',
    $_SERVER['REMOTE_ADDR'] ?? '',
    $input['user_agent'] ?? '',
    $input['referrer'] ?? '',
    $input['url'] ?? ''
];

// Escape CSV data
$escapedData = array_map(function($field) {
    return '"' . str_replace('"', '""', $field) . '"';
}, $logData);

// Write to daily log file
$logFile = $logDir . 'usage_' . date('Y-m-d') . '.csv';
$logLine = implode(',', $escapedData) . PHP_EOL;

// Create header if file doesn't exist
if (!file_exists($logFile)) {
    $header = '"timestamp","action","tool","details","ip","user_agent","referrer","url"' . PHP_EOL;
    file_put_contents($logFile, $header);
}

file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);

echo json_encode(['status' => 'logged']);
?>

<?php
// tools/qr-generator/generate-qr-server.php - Server-side QR generation with file saving
require_once __DIR__ . '/../../vendor/phpqrcode/qrlib.php'; // Download from https://sourceforge.net/projects/phpqrcode/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$text = trim($input['text'] ?? '');
$type = $input['type'] ?? 'text'; // text, email, phone, wifi, vcard, website

if (empty($text)) {
    http_response_code(400);
    echo json_encode(['error' => 'Text is required']);
    exit;
}

// Create directory structure
$baseDir = __DIR__ . '/qrcodes/';
$typeDir = $baseDir . $type . '/';
$dateDir = $typeDir . date('Y-m-d') . '/';

if (!file_exists($dateDir)) {
    mkdir($dateDir, 0755, true);
}

// Generate filename
$sanitizedText = preg_replace('/[^a-zA-Z0-9]/', '_', substr($text, 0, 30));
$sanitizedText = trim($sanitizedText, '_');
$timestamp = date('His');
$serial = sprintf('%03d', rand(1, 999));
$filename = "{$sanitizedText}_{$timestamp}_{$serial}.png";
$filepath = $dateDir . $filename;
$webPath = "/tools/qr-generator/qrcodes/{$type}/" . date('Y-m-d') . "/{$filename}";

try {
    // Generate QR code
    QRcode::png($text, $filepath, QR_ECLEVEL_M, 6, 2);
    
    // Log the generation
    $logFile = __DIR__ . '/../../logs/qr_generation_' . date('Y-m-d') . '.csv';
    $logData = [
        date('Y-m-d H:i:s'),
        $type,
        strlen($text),
        $_SERVER['REMOTE_ADDR'] ?? '',
        $filename,
        $text // Be careful with sensitive data
    ];
    
    $escapedData = array_map(function($field) {
        return '"' . str_replace('"', '""', $field) . '"';
    }, $logData);
    
    if (!file_exists($logFile)) {
        $header = '"timestamp","type","text_length","ip","filename","content"' . PHP_EOL;
        file_put_contents($logFile, $header);
    }
    
    file_put_contents($logFile, implode(',', $escapedData) . PHP_EOL, FILE_APPEND | LOCK_EX);
    
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'webPath' => $webPath,
        'size' => filesize($filepath)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate QR code']);
}
?>

<?php
// tools/qr-generator/cleanup-old-files.php - Cleanup script (run via cron)
$baseDir = __DIR__ . '/qrcodes/';
$daysToKeep = 30; // Keep files for 30 days
$cutoffTime = time() - ($daysToKeep * 24 * 60 * 60);

function cleanupDirectory($dir, $cutoffTime) {
    if (!is_dir($dir)) return;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getMTime() < $cutoffTime) {
            unlink($file->getPathname());
            echo "Deleted: " . $file->getPathname() . PHP_EOL;
        } elseif ($file->isDir() && iterator_count(new FilesystemIterator($file->getPathname())) === 0) {
            rmdir($file->getPathname());
            echo "Removed empty dir: " . $file->getPathname() . PHP_EOL;
        }
    }
}

cleanupDirectory($baseDir, $cutoffTime);
echo "Cleanup completed at " . date('Y-m-d H:i:s') . PHP_EOL;
?>

<?php
// analytics/daily-report.php - Generate daily usage reports
$logDir = __DIR__ . '/../logs/';
$reportDate = $_GET['date'] ?? date('Y-m-d');
$logFile = $logDir . "usage_{$reportDate}.csv";

if (!file_exists($logFile)) {
    echo "No data found for {$reportDate}";
    exit;
}

$data = array_map('str_getcsv', file($logFile));
$header = array_shift($data);

// Analyze data
$stats = [
    'total_actions' => count($data),
    'unique_ips' => count(array_unique(array_column($data, 4))),
    'tools_used' => array_count_values(array_column($data, 2)),
    'actions' => array_count_values(array_column($data, 1)),
    'hourly_usage' => []
];

// Hourly breakdown
foreach ($data as $row) {
    $hour = date('H', strtotime($row[0]));
    $stats['hourly_usage'][$hour] = ($stats['hourly_usage'][$hour] ?? 0) + 1;
}

header('Content-Type: application/json');
echo json_encode($stats, JSON_PRETTY_PRINT);
?>

<?php
// config/database.php - Database configuration (optional)
class DatabaseLogger {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = new PDO(
                'mysql:host=localhost;dbname=ultimate_tools',
                'username',
                'password',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
        }
    }
    
    public function logUsage($action, $tool, $details, $ip, $userAgent) {
        if (!$this->pdo) return false;
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO usage_logs (timestamp, action, tool, details, ip, user_agent) 
                VALUES (NOW(), ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$action, $tool, $details, $ip, $userAgent]);
        } catch (PDOException $e) {
            error_log("Failed to log usage: " . $e->getMessage());
            return false;
        }
    }
    
    public function getToolStats($days = 30) {
        if (!$this->pdo) return [];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT tool, COUNT(*) as usage_count 
                FROM usage_logs 
                WHERE timestamp >= DATE_SUB(NOW(), INTERVAL ? DAY)
                GROUP BY tool 
                ORDER BY usage_count DESC
            ");
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get stats: " . $e->getMessage());
            return [];
        }
    }
}

-- SQL to create tables
CREATE TABLE usage_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    timestamp DATETIME NOT NULL,
    action VARCHAR(50) NOT NULL,
    tool VARCHAR(50) NOT NULL,
    details TEXT,
    ip VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_timestamp ON usage_logs(timestamp);
CREATE INDEX idx_tool ON usage_logs(tool);
CREATE INDEX idx_action ON usage_logs(action);
?>