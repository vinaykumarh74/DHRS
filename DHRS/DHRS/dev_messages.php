<?php
/**
 * Development Messages Viewer
 * 
 * This page shows emails and SMS messages that would be sent in development mode
 * Access this page to see OTPs and reset links
 */

// Start session
session_start();

// Load configuration
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Development Messages - DHRS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .message-card {
            border-left: 4px solid #007bff;
        }
        .sms-card {
            border-left: 4px solid #28a745;
        }
        .message-body {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
            font-family: monospace;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">
                    <i class="fas fa-envelope me-2"></i>
                    Development Messages
                </h1>
                <p class="text-muted">This page shows emails and SMS messages that would be sent in development mode.</p>
                
                <div class="row">
                    <!-- Emails Section -->
                    <div class="col-md-6">
                        <h3 class="mb-3">
                            <i class="fas fa-envelope me-2"></i>
                            Emails (<?php echo isset($_SESSION['dev_emails']) ? count($_SESSION['dev_emails']) : 0; ?>)
                        </h3>
                        
                        <?php if (isset($_SESSION['dev_emails']) && !empty($_SESSION['dev_emails'])): ?>
                            <?php foreach (array_reverse($_SESSION['dev_emails']) as $email): ?>
                                <div class="card message-card mb-3">
                                    <div class="card-header">
                                        <strong>To:</strong> <?php echo htmlspecialchars($email['to']); ?>
                                        <small class="text-muted float-end"><?php echo $email['timestamp']; ?></small>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($email['subject']); ?></h6>
                                        <div class="message-body"><?php echo htmlspecialchars($email['body']); ?></div>
                                        
                                        <!-- Extract and highlight OTPs and links -->
                                        <?php
                                        // Extract OTP from body
                                        if (preg_match('/OTP.*?is:\s*<strong>(\d+)<\/strong>/i', $email['body'], $matches)) {
                                            echo '<div class="alert alert-info mt-2">';
                                            echo '<strong>OTP:</strong> <span class="h4 text-primary">' . $matches[1] . '</span>';
                                            echo '</div>';
                                        }
                                        
                                        // Extract reset link
                                        if (preg_match('/href=[\'"]([^\'"]+)[\'"]/', $email['body'], $matches)) {
                                            echo '<div class="alert alert-warning mt-2">';
                                            echo '<strong>Reset Link:</strong><br>';
                                            echo '<a href="' . htmlspecialchars($matches[1]) . '" target="_blank" class="btn btn-sm btn-warning">';
                                            echo '<i class="fas fa-external-link-alt me-1"></i>Open Reset Link';
                                            echo '</a>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No emails sent yet.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- SMS Section -->
                    <div class="col-md-6">
                        <h3 class="mb-3">
                            <i class="fas fa-sms me-2"></i>
                            SMS Messages (<?php echo isset($_SESSION['dev_sms']) ? count($_SESSION['dev_sms']) : 0; ?>)
                        </h3>
                        
                        <?php if (isset($_SESSION['dev_sms']) && !empty($_SESSION['dev_sms'])): ?>
                            <?php foreach (array_reverse($_SESSION['dev_sms']) as $sms): ?>
                                <div class="card sms-card mb-3">
                                    <div class="card-header">
                                        <strong>To:</strong> <?php echo htmlspecialchars($sms['phone']); ?>
                                        <small class="text-muted float-end"><?php echo $sms['timestamp']; ?></small>
                                    </div>
                                    <div class="card-body">
                                        <div class="message-body"><?php echo htmlspecialchars($sms['message']); ?></div>
                                        
                                        <!-- Extract and highlight OTPs -->
                                        <?php
                                        if (preg_match('/OTP.*?is:\s*(\d+)/i', $sms['message'], $matches)) {
                                            echo '<div class="alert alert-success mt-2">';
                                            echo '<strong>OTP:</strong> <span class="h4 text-success">' . $matches[1] . '</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                No SMS messages sent yet.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Back to Home
                    </a>
                    <button onclick="clearMessages()" class="btn btn-outline-danger">
                        <i class="fas fa-trash me-2"></i>Clear All Messages
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function clearMessages() {
            if (confirm('Are you sure you want to clear all messages?')) {
                // Create a form to clear messages
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'dev_messages.php';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'clear_messages';
                input.value = '1';
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

<?php
// Handle clear messages request
if (isset($_POST['clear_messages'])) {
    unset($_SESSION['dev_emails']);
    unset($_SESSION['dev_sms']);
    header('Location: dev_messages.php');
    exit();
}
?>
