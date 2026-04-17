<?php
$host = getenv('DB_HOST');
$ip = gethostbyname($host);

echo "HOST: " . $host . "<br>";
echo "DNS resolved to: " . $ip . "<br>";

if ($ip === $host) {
    echo "❌ DNS FAILED";
} else {
    echo "✅ DNS OK - " . $ip;
}
exit;
?>
