<?php
/**
 * Admin Settings View
 */

// Set page title
$page_title = 'System Settings';

// Start output buffering
ob_start();
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4><i class="fas fa-cogs me-2"></i>System Settings</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Setting</th>
                                    <th>Value</th>
                                    <th>Description</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($settings) && !empty($settings)): ?>
                                    <?php foreach ($settings as $setting): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($setting['setting_key']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($setting['setting_value']); ?></td>
                                            <td><?php echo htmlspecialchars($setting['description']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editSetting('<?php echo htmlspecialchars($setting['setting_key']); ?>', '<?php echo htmlspecialchars($setting['setting_value']); ?>')">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>No system settings found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Setting Modal -->
<div class="modal fade" id="editSettingModal" tabindex="-1" aria-labelledby="editSettingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSettingModalLabel">Edit Setting</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSettingForm" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="settingKey" class="form-label">Setting Key</label>
                        <input type="text" class="form-control" id="settingKey" name="setting_key" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="settingValue" class="form-label">Setting Value</label>
                        <input type="text" class="form-control" id="settingValue" name="setting_value" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSetting(key, value) {
    document.getElementById('settingKey').value = key;
    document.getElementById('settingValue').value = value;
    new bootstrap.Modal(document.getElementById('editSettingModal')).show();
}

document.getElementById('editSettingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'update_setting');
    
    fetch('index.php?controller=admin&action=update_setting', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Setting updated successfully!');
            location.reload();
        } else {
            alert('Error updating setting: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the setting.');
    });
});
</script>

<?php
// Get the buffered content
$content = ob_get_clean();

// Include the layout file
include('views/layout.php');
?>
