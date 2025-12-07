<?php
/**
 * Flash Messages Component
 * Displays success, error, warning, and info messages
 * 
 * Note: This file is now deprecated in favor of the display_flash_message() function.
 * It is kept for backward compatibility but should not be used in new code.
 */
?>

<!-- Debug: Flash Messages Component -->
<div style="display: none;">
    <pre><?php print_r($_SESSION); ?></pre>
</div>

<?php 
// Skip if display_flash_message has already been called
if (isset($_SESSION['flash_messages']) && !empty($_SESSION['flash_messages'])): 
?>
    <?php foreach ($_SESSION['flash_messages'] as $type => $messages): ?>
        <?php foreach ($messages as $message): ?>
            <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
                <?php 
                $icon = '';
                switch ($type) {
                    case 'success':
                        $icon = '<i class="fas fa-check-circle me-2"></i>';
                        break;
                    case 'danger':
                        $icon = '<i class="fas fa-exclamation-circle me-2"></i>';
                        break;
                    case 'warning':
                        $icon = '<i class="fas fa-exclamation-triangle me-2"></i>';
                        break;
                    case 'info':
                        $icon = '<i class="fas fa-info-circle me-2"></i>';
                        break;
                }
                echo $icon . htmlspecialchars($message); 
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <?php 
    // Don't unset here, let display_flash_message handle it
    // unset($_SESSION['flash_messages']); 
    ?>
<?php endif; ?>