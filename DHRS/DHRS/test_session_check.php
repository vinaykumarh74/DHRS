<?php
// Start session
session_start();

// Output session information
echo '<h1>Session Check</h1>';
echo '<p>Session ID: ' . session_id() . '</p>';
echo '<p>Session Data:</p>';
echo '<pre>';
print_r($_SESSION);
echo '</pre>';

// Display flash messages if they exist
echo '<h2>Flash Messages</h2>';
if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])) {
    foreach ($_SESSION['flash_messages'] as $type => $messages) {
        foreach ($messages as $message) {
            echo "<div style='padding: 10px; margin: 5px; border: 1px solid #ccc; background-color: #f8f9fa;'>";
            echo "<strong>$type:</strong> $message";
            echo "</div>";
        }
    }
    
    // Clear flash messages after displaying
    unset($_SESSION['flash_messages']);
} else {
    echo '<p>No flash messages found.</p>';
}

echo '<p><a href="test_session.php">Go Back to First Page</a></p>';