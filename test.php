<?php
$host = 'mysql-1a3b94fa-tip-b6a3.i.aivencloud.com';

// Test DNS
$ip = gethostbyname($host);
echo "DNS resolved to: " . $ip . "<br>";

// Test if it resolved (gethostbyname returns the input if it fails)
if ($ip === $host) {
    echo "❌ DNS FAILED - cannot resolve hostname<br>";
} else {
    echo "✅ DNS OK<br>";
}

// Test port
$conn = @fsockopen($host, 15368, $errno, $errstr, 5);
if ($conn) {
    echo "✅ Port 15368 is reachable<br>";
    fclose($conn);
} else {
    echo "❌ Port blocked: $errstr ($errno)<br>";
}
?>
