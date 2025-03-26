<?php
include 'db.php';

if ($pdo) {
    echo "Database connection successful!";
} else {
    echo "Failed to connect.";
}
?>
